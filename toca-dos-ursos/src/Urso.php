<?php
declare(strict_types=1);

/** Regras de domínio: rótulos, normalização e validação de um urso. */
final class Urso
{
    public const DIETAS = ['onivora' => 'Onívora', 'carnivora' => 'Carnívora', 'herbivora' => 'Herbívora'];
    public const STATUS = [
        'LC' => 'Pouco preocupante',
        'NT' => 'Quase ameaçado',
        'VU' => 'Vulnerável',
        'EN' => 'Em perigo',
        'CR' => 'Criticamente em perigo',
    ];

    private static function chave(string $s): string
    {
        $s = mb_strtolower(trim($s), 'UTF-8');
        $s = strtr($s, ['á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ç' => 'c']);
        return preg_replace('/[^a-z0-9]/', '', $s);
    }

    private static function mapear(string $valor, array $opcoes): ?string
    {
        $k = self::chave($valor);
        foreach ($opcoes as $codigo => $rotulo) {
            if ($k === self::chave((string) $codigo) || $k === self::chave($rotulo)) {
                return (string) $codigo;
            }
        }
        return null;
    }

    /** @return array{0: array, 1: string[]} [dados limpos, erros] */
    public static function validar(array $in): array
    {
        $erros = [];
        $txt = fn(string $c, int $max) => mb_substr(trim((string) ($in[$c] ?? '')), 0, $max);

        $d = [
            'nome'        => $txt('nome', 100),
            'especie'     => $txt('especie', 100),
            'habitat'     => $txt('habitat', 150),
            'cor_pelagem' => $txt('cor_pelagem', 60),
            'descricao'   => $txt('descricao', 2000),
        ];
        if ($d['nome'] === '')    { $erros[] = 'Informe o nome.'; }
        if ($d['especie'] === '') { $erros[] = 'Informe a espécie.'; }

        $peso = str_replace(',', '.', trim((string) ($in['peso_kg'] ?? '')));
        if ($peso === '') {
            $d['peso_kg'] = null;
        } elseif (!is_numeric($peso) || $peso < 0 || $peso > 1500) {
            $erros[] = 'Peso deve ser um número entre 0 e 1500 kg.';
            $d['peso_kg'] = null;
        } else {
            $d['peso_kg'] = round((float) $peso, 2);
        }

        $idade = trim((string) ($in['idade_anos'] ?? ''));
        if ($idade === '') {
            $d['idade_anos'] = null;
        } elseif (!ctype_digit($idade) || (int) $idade > 60) {
            $erros[] = 'Idade deve ser um número inteiro entre 0 e 60.';
            $d['idade_anos'] = null;
        } else {
            $d['idade_anos'] = (int) $idade;
        }

        $dieta = trim((string) ($in['dieta'] ?? ''));
        $d['dieta'] = $dieta === '' ? 'onivora' : self::mapear($dieta, self::DIETAS);
        if ($d['dieta'] === null) { $erros[] = 'Dieta inválida.'; }

        $status = trim((string) ($in['status_conservacao'] ?? ''));
        $d['status_conservacao'] = $status === '' ? 'LC' : self::mapear($status, self::STATUS);
        if ($d['status_conservacao'] === null) { $erros[] = 'Status de conservação inválido.'; }

        return [$d, $erros];
    }
}
