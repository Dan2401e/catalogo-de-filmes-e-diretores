<?php

declare(strict_types=1);

// Configura a sessão antes de iniciá-la, reduzindo riscos de sequestro de sessão.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');

    $httpsAtivo = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $httpsAtivo,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

// Calcula a URL da pasta do projeto relativa à raiz pública do servidor.
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$applicationRoot = realpath(dirname(__DIR__));
$basePath = '';

if ($documentRoot !== false && $applicationRoot !== false) {
    $documentRoot = rtrim(str_replace('\\', '/', $documentRoot), '/');
    $applicationRoot = str_replace('\\', '/', $applicationRoot);

    if (strncasecmp($applicationRoot, $documentRoot, strlen($documentRoot)) === 0) {
        $basePath = substr($applicationRoot, strlen($documentRoot));
    }
}

$basePath = trim(str_replace('\\', '/', $basePath), '/');
define('BASE_URL', $basePath === '' ? '/' : '/' . $basePath . '/');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

date_default_timezone_set('America/Sao_Paulo');
