<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(function (string $classe): void {
    $arquivo = __DIR__ . '/' . $classe . '.php';
    if (is_file($arquivo)) {
        require $arquivo;
    }
});

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

function config(string $chave): mixed
{
    static $cfg = null;
    $cfg ??= require BASE_PATH . '/config/config.php';
    return $cfg[$chave] ?? null;
}

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function redirecionar(string $url): never
{
    header('Location: ' . $url);
    exit;
}
