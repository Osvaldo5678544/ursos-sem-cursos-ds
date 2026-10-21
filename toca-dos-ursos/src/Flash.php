<?php
declare(strict_types=1);

final class Flash
{
    public static function add(string $tipo, string $msg): void
    {
        $_SESSION['flash'][] = ['tipo' => $tipo, 'msg' => $msg];
    }

    public static function todas(): array
    {
        $f = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $f;
    }
}
