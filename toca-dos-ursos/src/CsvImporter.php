<?php
declare(strict_types=1);

/** Lê um CSV (separador "," ou ";", UTF-8 ou ISO-8859-1) e grava os ursos. */
final class CsvImporter
{
    public const COLUNAS = ['nome', 'especie', 'habitat', 'peso_kg', 'idade_anos', 'cor_pelagem', 'dieta', 'status_conservacao', 'descricao'];
    private const MAX_LINHAS = 1000;

    public function __construct(private UrsoRepository $repo)
    {
    }

    /** @return array{inseridos: int, ignorados: int, erros: string[]} */
    public function importar(string $caminho): array
    {
        $res = ['inseridos' => 0, 'ignorados' => 0, 'erros' => []];

        $h = fopen($caminho, 'rb');
        if ($h === false) {
            $res['erros'][] = 'Não foi possível ler o arquivo.';
            return $res;
        }

        $primeira = (string) fgets($h);
        $sep = substr_count($primeira, ';') > substr_count($primeira, ',') ? ';' : ',';
        rewind($h);

        $cab = fgetcsv($h, 0, $sep);
        if (!$cab) {
            $res['erros'][] = 'Arquivo vazio.';
            fclose($h);
            return $res;
        }
        $cab = array_map(fn($c) => strtolower(trim($this->utf8(preg_replace('/^\xEF\xBB\xBF/', '', (string) $c)))), $cab);

        if (!in_array('nome', $cab, true) || !in_array('especie', $cab, true)) {
            $res['erros'][] = 'O cabeçalho precisa ter ao menos as colunas "nome" e "especie".';
            fclose($h);
            return $res;
        }

        $validos = [];
        $linha = 1;
        while (($row = fgetcsv($h, 0, $sep)) !== false) {
            $linha++;
            if ($row === [null] || count(array_filter($row, fn($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }
            if ($linha - 1 > self::MAX_LINHAS) {
                $res['erros'][] = 'Limite de ' . self::MAX_LINHAS . ' linhas excedido; o restante foi ignorado.';
                break;
            }
            $dados = [];
            foreach ($cab as $i => $nome) {
                if (in_array($nome, self::COLUNAS, true)) {
                    $dados[$nome] = $this->utf8((string) ($row[$i] ?? ''));
                }
            }
            [$limpo, $erros] = Urso::validar($dados);
            if ($erros) {
                $res['erros'][] = "Linha $linha: " . implode(' ', $erros);
                $res['ignorados']++;
                continue;
            }
            $validos[] = $limpo;
        }
        fclose($h);

        $this->repo->transacao(function () use ($validos, &$res) {
            foreach ($validos as $u) {
                if ($this->repo->existe($u['nome'], $u['especie'])) {
                    $res['ignorados']++;
                    continue;
                }
                $this->repo->criar($u);
                $res['inseridos']++;
            }
        });

        return $res;
    }

    private function utf8(string $v): string
    {
        return mb_check_encoding($v, 'UTF-8') ? $v : mb_convert_encoding($v, 'UTF-8', 'ISO-8859-1');
    }
}
