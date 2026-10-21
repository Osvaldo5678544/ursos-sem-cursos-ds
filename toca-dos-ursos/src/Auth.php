<?php
declare(strict_types=1);

final class Auth
{
    public static function tentar(string $usuario, string $senha): bool
    {
        $st = Database::conexao()->prepare('SELECT id, usuario, nome, senha_hash FROM usuarios WHERE usuario = :u LIMIT 1');
        $st->execute(['u' => $usuario]);
        $u = $st->fetch();

        if (!$u || !password_verify($senha, $u['senha_hash'])) {
            usleep(400000); // atrasa tentativas erradas
            return false;
        }
        session_regenerate_id(true);
        $_SESSION['usuario'] = ['id' => (int) $u['id'], 'usuario' => $u['usuario'], 'nome' => $u['nome']];
        return true;
    }

    public static function logado(): bool
    {
        return isset($_SESSION['usuario']);
    }

    public static function usuario(): ?array
    {
        return $_SESSION['usuario'] ?? null;
    }

    public static function exigir(): void
    {
        if (!self::logado()) {
            redirecionar('login.php');
        }
    }

    public static function sair(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
