<?php
declare(strict_types=1);

final class UrsoRepository
{
    private const CAMPOS = ['nome', 'especie', 'habitat', 'peso_kg', 'idade_anos', 'cor_pelagem', 'dieta', 'status_conservacao', 'descricao'];

    public function __construct(private PDO $pdo)
    {
    }

    /** @return array{itens: array, total: int} */
    public function listar(string $busca, int $pagina, int $porPagina): array
    {
        $where  = '';
        $params = [];
        if ($busca !== '') {
            $where  = 'WHERE nome LIKE :b OR especie LIKE :b OR habitat LIKE :b';
            $params = ['b' => '%' . $busca . '%'];
        }

        $st = $this->pdo->prepare("SELECT COUNT(*) FROM ursos $where");
        $st->execute($params);
        $total = (int) $st->fetchColumn();

        $offset = max(0, ($pagina - 1) * $porPagina);
        $st = $this->pdo->prepare("SELECT * FROM ursos $where ORDER BY nome ASC LIMIT $porPagina OFFSET $offset");
        $st->execute($params);

        return ['itens' => $st->fetchAll(), 'total' => $total];
    }

    public function buscar(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM ursos WHERE id = :id');
        $st->execute(['id' => $id]);
        return $st->fetch() ?: null;
    }

    public function existe(string $nome, string $especie): bool
    {
        $st = $this->pdo->prepare('SELECT 1 FROM ursos WHERE nome = :n AND especie = :e LIMIT 1');
        $st->execute(['n' => $nome, 'e' => $especie]);
        return (bool) $st->fetchColumn();
    }

    public function criar(array $d): int
    {
        $cols = implode(', ', self::CAMPOS);
        $vals = ':' . implode(', :', self::CAMPOS);
        $this->pdo->prepare("INSERT INTO ursos ($cols) VALUES ($vals)")->execute($this->filtrar($d));
        return (int) $this->pdo->lastInsertId();
    }

    public function atualizar(int $id, array $d): void
    {
        $set = implode(', ', array_map(fn($c) => "$c = :$c", self::CAMPOS));
        $this->pdo->prepare("UPDATE ursos SET $set WHERE id = :id")->execute($this->filtrar($d) + ['id' => $id]);
    }

    public function excluir(int $id): void
    {
        $this->pdo->prepare('DELETE FROM ursos WHERE id = :id')->execute(['id' => $id]);
    }

    public function transacao(callable $fn): mixed
    {
        $this->pdo->beginTransaction();
        try {
            $r = $fn();
            $this->pdo->commit();
            return $r;
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function filtrar(array $d): array
    {
        $out = [];
        foreach (self::CAMPOS as $c) {
            $v = $d[$c] ?? null;
            $out[$c] = ($v === '' ? null : $v);
        }
        return $out;
    }
}
