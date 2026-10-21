<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validar();
    Preferencias::salvar($_POST);
    Flash::add('ok', 'Preferências salvas neste navegador.');
    redirecionar('preferencias.php');
}

$prefs  = Preferencias::obter();
$titulo = 'Aparência';
require BASE_PATH . '/templates/header.php';
?>
<section class="cartao estreito">
    <h1>Aparência</h1>
    <p>Estas escolhas ficam salvas em um cookie por 1 ano.</p>
    <form method="post">
        <?= Csrf::campo() ?>
        <label>Tema
            <select name="tema">
                <?php foreach (Preferencias::TEMAS as $cod => $rot): ?>
                    <option value="<?= e($cod) ?>" <?= $prefs['tema'] === $cod ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tamanho do texto
            <select name="fonte">
                <?php foreach (Preferencias::FONTES as $cod => $rot): ?>
                    <option value="<?= e($cod) ?>" <?= $prefs['fonte'] === $cod ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Ursos por página
            <select name="por_pagina">
                <?php foreach (Preferencias::PAGINAS as $n): ?>
                    <option value="<?= $n ?>" <?= $prefs['por_pagina'] === $n ? 'selected' : '' ?>><?= $n ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="botao">Salvar preferências</button>
    </form>
</section>
<?php require BASE_PATH . '/templates/footer.php';
