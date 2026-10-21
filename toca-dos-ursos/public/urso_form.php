<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
Auth::exigir();

$repo = new UrsoRepository(Database::conexao());
$id   = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$urso = $id > 0 ? $repo->buscar($id) : null;

if ($id > 0 && !$urso) {
    Flash::add('erro', 'Urso não encontrado.');
    redirecionar('ursos.php');
}

$dados = $urso ?? [
    'nome' => '', 'especie' => '', 'habitat' => '', 'peso_kg' => '', 'idade_anos' => '',
    'cor_pelagem' => '', 'dieta' => 'onivora', 'status_conservacao' => 'LC', 'descricao' => '',
];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validar();
    [$limpo, $erros] = Urso::validar($_POST);
    if (!$erros) {
        if ($urso) {
            $repo->atualizar($id, $limpo);
            Flash::add('ok', 'Urso atualizado.');
        } else {
            $repo->criar($limpo);
            Flash::add('ok', 'Urso cadastrado.');
        }
        redirecionar('ursos.php');
    }
    $dados = array_merge($dados, $_POST);
}

$titulo = $urso ? 'Editar urso' : 'Novo urso';
require BASE_PATH . '/templates/header.php';
?>
<section class="cartao">
    <h1><?= e($titulo) ?></h1>
    <?php if ($erros): ?>
        <div class="aviso aviso-erro" role="alert"><ul><?php foreach ($erros as $m): ?><li><?= e($m) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>
    <form method="post" class="grade">
        <?= Csrf::campo() ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
        <label>Nome *<input type="text" name="nome" maxlength="100" required value="<?= e($dados['nome']) ?>"></label>
        <label>Espécie *<input type="text" name="especie" maxlength="100" required value="<?= e($dados['especie']) ?>"></label>
        <label>Habitat<input type="text" name="habitat" maxlength="150" value="<?= e($dados['habitat']) ?>"></label>
        <label>Cor da pelagem<input type="text" name="cor_pelagem" maxlength="60" value="<?= e($dados['cor_pelagem']) ?>"></label>
        <label>Peso (kg)<input type="text" inputmode="decimal" name="peso_kg" value="<?= e($dados['peso_kg']) ?>"></label>
        <label>Idade (anos)<input type="number" name="idade_anos" min="0" max="60" value="<?= e($dados['idade_anos']) ?>"></label>
        <label>Dieta
            <select name="dieta">
                <?php foreach (Urso::DIETAS as $cod => $rot): ?>
                    <option value="<?= e($cod) ?>" <?= $dados['dieta'] === $cod ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Status de conservação
            <select name="status_conservacao">
                <?php foreach (Urso::STATUS as $cod => $rot): ?>
                    <option value="<?= e($cod) ?>" <?= $dados['status_conservacao'] === $cod ? 'selected' : '' ?>><?= e($rot) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="largo">Descrição<textarea name="descricao" rows="4" maxlength="2000"><?= e($dados['descricao']) ?></textarea></label>
        <div class="largo botoes">
            <button type="submit" class="botao">Salvar urso</button>
            <a href="ursos.php">Cancelar</a>
        </div>
    </form>
</section>
<?php require BASE_PATH . '/templates/footer.php';
