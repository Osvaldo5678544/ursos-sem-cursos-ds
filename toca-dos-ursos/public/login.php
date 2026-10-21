<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

if (Auth::logado()) {
    redirecionar('ursos.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validar();
    if (Auth::tentar(trim((string) ($_POST['usuario'] ?? '')), (string) ($_POST['senha'] ?? ''))) {
        redirecionar('ursos.php');
    }
    $erro = 'Usuário ou senha incorretos.';
}

$titulo = 'Entrar';
require BASE_PATH . '/templates/header.php';
?>
<section class="cartao estreito">
    <h1>Entrar na toca</h1>
    <?php if ($erro): ?><div class="aviso aviso-erro" role="alert"><?= e($erro) ?></div><?php endif; ?>
    <form method="post" autocomplete="on">
        <?= Csrf::campo() ?>
        <label>Usuário
            <input type="text" name="usuario" value="<?= e($_POST['usuario'] ?? '') ?>" required autofocus autocomplete="username">
        </label>
        <label>Senha
            <input type="password" name="senha" required autocomplete="current-password">
        </label>
        <button type="submit" class="botao">Entrar</button>
    </form>
</section>
<?php require BASE_PATH . '/templates/footer.php';
