<?php
$prefs = Preferencias::obter();
$app   = config('app')['nome'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo ?? 'Início') ?> - <?= e($app) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="tema-<?= e($prefs['tema']) ?> fonte-<?= e($prefs['fonte']) ?>">
<header class="topo">
    <a class="marca" href="ursos.php">🐻 <?= e($app) ?></a>
    <nav class="menu" aria-label="Principal">
        <?php if (Auth::logado()): ?>
            <a href="ursos.php">Ursos</a>
            <a href="urso_form.php">Novo urso</a>
            <a href="importar.php">Importar CSV</a>
        <?php endif; ?>
        <a href="preferencias.php">Aparência</a>
        <?php if (Auth::logado()): ?>
            <form method="post" action="logout.php" class="inline">
                <?= Csrf::campo() ?>
                <button type="submit" class="link">Sair (<?= e(Auth::usuario()['usuario']) ?>)</button>
            </form>
        <?php endif; ?>
    </nav>
</header>
<main class="conteudo">
<?php foreach (Flash::todas() as $f): ?>
    <div class="aviso aviso-<?= e($f['tipo']) ?>" role="status"><?= e($f['msg']) ?></div>
<?php endforeach; ?>
