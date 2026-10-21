<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
Auth::exigir();

$porPagina = Preferencias::obter()['por_pagina'];
$busca     = trim((string) ($_GET['q'] ?? ''));
$pagina    = max(1, (int) ($_GET['p'] ?? 1));

$r       = (new UrsoRepository(Database::conexao()))->listar($busca, $pagina, $porPagina);
$paginas = max(1, (int) ceil($r['total'] / $porPagina));
$link    = fn(int $p) => 'ursos.php?' . http_build_query(['q' => $busca, 'p' => $p]);

$titulo = 'Ursos';
require BASE_PATH . '/templates/header.php';
?>
<div class="barra">
    <h1>Ursos cadastrados <small>(<?= (int) $r['total'] ?>)</small></h1>
    <a class="botao" href="urso_form.php">Novo urso</a>
</div>

<form method="get" class="busca" role="search">
    <input type="search" name="q" value="<?= e($busca) ?>" placeholder="Buscar por nome, espécie ou habitat" aria-label="Buscar ursos">
    <button type="submit" class="botao secundario">Buscar</button>
    <?php if ($busca !== ''): ?><a href="ursos.php">Limpar busca</a><?php endif; ?>
</form>

<?php if (!$r['itens']): ?>
    <p class="vazio">Nenhum urso encontrado. <a href="urso_form.php">Cadastre o primeiro</a> ou <a href="importar.php">importe um CSV</a>.</p>
<?php else: ?>
<div class="tabela-wrap">
<table>
    <thead>
        <tr><th>Nome</th><th>Espécie</th><th>Habitat</th><th>Peso</th><th>Idade</th><th>Dieta</th><th>Conservação</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($r['itens'] as $u): ?>
        <tr>
            <td><strong><?= e($u['nome']) ?></strong></td>
            <td><?= e($u['especie']) ?></td>
            <td><?= e($u['habitat']) ?></td>
            <td><?= $u['peso_kg'] !== null ? e(number_format((float) $u['peso_kg'], 1, ',', '.')) . ' kg' : '-' ?></td>
            <td><?= $u['idade_anos'] !== null ? (int) $u['idade_anos'] . ' anos' : '-' ?></td>
            <td><?= e(Urso::DIETAS[$u['dieta']] ?? $u['dieta']) ?></td>
            <td><span class="selo selo-<?= e($u['status_conservacao']) ?>"><?= e(Urso::STATUS[$u['status_conservacao']] ?? $u['status_conservacao']) ?></span></td>
            <td class="acoes">
                <a href="urso_form.php?id=<?= (int) $u['id'] ?>">Editar</a>
                <form method="post" action="urso_excluir.php" class="inline" onsubmit="return confirm('Excluir <?= e(addslashes($u['nome'])) ?>?');">
                    <?= Csrf::campo() ?>
                    <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                    <button type="submit" class="link perigo">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($paginas > 1): ?>
<nav class="paginacao" aria-label="Paginação">
    <?php if ($pagina > 1): ?><a href="<?= e($link($pagina - 1)) ?>">Anterior</a><?php endif; ?>
    <span>Página <?= $pagina ?> de <?= $paginas ?></span>
    <?php if ($pagina < $paginas): ?><a href="<?= e($link($pagina + 1)) ?>">Próxima</a><?php endif; ?>
</nav>
<?php endif; ?>
<?php endif; ?>
<?php require BASE_PATH . '/templates/footer.php';
