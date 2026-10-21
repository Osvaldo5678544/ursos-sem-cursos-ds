<?php
declare(strict_types=1);

final class Csrf
{
    public static function token(): string
    {
        return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function campo(): string
    {
        return '<input type="hidden" name="csrf" value="' . e(self::token()) . '">';
    }

    public static function validar(): void
    {
        $enviado = $_POST['csrf'] ?? '';
        if (!is_string($enviado) || !hash_equals(self::token(), $enviado)) {
            http_response_code(419);
            exit('Sessão expirada. Volte à página anterior, recarregue e tente de novo.');
        }
    }
}
