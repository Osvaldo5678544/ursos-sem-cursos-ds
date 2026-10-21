<?php
declare(strict_types=1);
require __DIR__ . '/../src/bootstrap.php';
Auth::exigir();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Csrf::validar();
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) {
        (new UrsoRepository(Database::conexao()))->excluir($id);
        Flash::add('ok', 'Urso excluído.');
    }
}
redirecionar('ursos.php');
