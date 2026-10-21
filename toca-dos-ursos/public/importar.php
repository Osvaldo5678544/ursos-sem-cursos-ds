<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
Auth::exigir();

$resultado = null;
$erro      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validar();
    $arq = $_FILES['arquivo'] ?? null;

    if (!$arq || $arq['error'] !== UPLOAD_ERR_OK) {
        $erro = 'Escolha um arquivo CSV para enviar.';
    } elseif ($arq['size'] > 2 * 1024 * 1024) {
        $erro = 'O arquivo passa de 2 MB.';
    } elseif (!in_array(strtolower(pathinfo($arq['name'], PATHINFO_EXTENSION)), ['csv', 'txt'], true)) {
        $erro = 'Envie um arquivo com extensão .csv.';
    } elseif (!is_uploaded_file($arq['tmp_name'])) {
        $erro = 'Upload inválido.';
    } else {
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($arq['tmp_name']);
        if (!str_starts_with((string) $mime, 'text/') && !in_array($mime, ['application/csv', 'application/vnd.ms-excel'], true)) {
            $erro = 'O conteúdo não parece um CSV de texto.';
        } else {
            try {
                $resultado = (new CsvImporter(new UrsoRepository(Database::conexao())))->importar($arq['tmp_name']);
            } catch (Throwable $ex) {
                $erro = 'Falha ao importar. Nada foi gravado.';
            }
        }
    }
}

$titulo = 'Importar CSV';
require BASE_PATH . '/templates/header.php';
?>
<section class="cartao">
    <h1>Importar ursos por CSV</h1>
    <p>A primeira linha deve ser o cabeçalho. Colunas aceitas: <code><?= e(implode(', ', CsvImporter::COLUNAS)) ?></code>. Só <code>nome</code> e <code>especie</code> são obrigatórias. O separador pode ser vírgula ou ponto e vírgula. Ursos com mesmo nome e espécie já cadastrados são ignorados.</p>
    <p><a href="modelo_ursos.csv" download>Baixar arquivo de exemplo</a></p>

    <?php if ($erro): ?><div class="aviso aviso-erro" role="alert"><?= e($erro) ?></div><?php endif; ?>

    <?php if ($resultado): ?>
        <div class="aviso aviso-ok" role="status">
            <?= (int) $resultado['inseridos'] ?> urso(s) importado(s) e <?= (int) $resultado['ignorados'] ?> linha(s) ignorada(s).
        </div>
        <?php if ($resultado['erros']): ?>
            <div class="aviso aviso-erro"><ul>
                <?php foreach (array_slice($resultado['erros'], 0, 50) as $m): ?><li><?= e($m) ?></li><?php endforeach; ?>
            </ul></div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <?= Csrf::campo() ?>
        <label>Arquivo CSV (até 2 MB)
            <input type="file" name="arquivo" accept=".csv,text/csv" required>
        </label>
        <button type="submit" class="botao">Importar ursos</button>
    </form>
</section>
<?php require BASE_PATH . '/templates/footer.php';
