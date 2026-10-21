<?php
declare(strict_types=1);

/** Preferências de aparência guardadas em cookie (1 ano). */
final class Preferencias
{
    private const COOKIE = 'ursos_prefs';
    public const TEMAS   = ['claro' => 'Claro (creme)', 'escuro' => 'Escuro (verde)'];
    public const FONTES  = ['pequena' => 'Pequena', 'media' => 'Média', 'grande' => 'Grande'];
    public const PAGINAS = [5, 10, 20];
    private const PADRAO = ['tema' => 'claro', 'fonte' => 'media', 'por_pagina' => 10];

    public static function obter(): array
    {
        $dados = json_decode($_COOKIE[self::COOKIE] ?? '', true);
        return self::sanear(is_array($dados) ? $dados : []);
    }

    public static function salvar(array $entrada): void
    {
        $prefs = self::sanear($entrada);
        $json  = json_encode($prefs);
        setcookie(self::COOKIE, $json, [
            'expires'  => time() + 31536000,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE] = $json;
    }

    private static function sanear(array $d): array
    {
        $tema  = $d['tema'] ?? '';
        $fonte = $d['fonte'] ?? '';
        $pag   = (int) ($d['por_pagina'] ?? 0);
        return [
            'tema'       => isset(self::TEMAS[$tema]) ? $tema : self::PADRAO['tema'],
            'fonte'      => isset(self::FONTES[$fonte]) ? $fonte : self::PADRAO['fonte'],
            'por_pagina' => in_array($pag, self::PAGINAS, true) ? $pag : self::PADRAO['por_pagina'],
        ];
    }
}
