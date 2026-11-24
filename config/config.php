<?php

declare(strict_types=1);

// Configura a sessão antes de iniciá-la, reduzindo riscos de sequestro de sessão.
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    $httpsAtivo = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $appleOauthRequest = in_array(basename($_SERVER['SCRIPT_NAME'] ?? ''), ['oauth-start.php', 'oauth-callback.php'], true)
        && ($_GET['provider'] ?? '') === 'apple';
    $sameSite = $appleOauthRequest && $httpsAtivo ? 'None' : 'Lax';
    ini_set('session.cookie_samesite', $sameSite);

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $httpsAtivo,
        'httponly' => true,
        'samesite' => $sameSite,
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

// Quando o projeto é publicado por um alias ou junction fora do DocumentRoot,
// considera o nome da pasta na URL como base da aplicação.
if ($basePath === '') {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $applicationName = basename(str_replace('\\', '/', $applicationRoot ?: dirname(__DIR__)));
    if ($applicationName !== '' && preg_match('~(?:^|/)' . preg_quote($applicationName, '~') . '(?:/|$)~i', $scriptName)) {
        $basePath = $applicationName;
    }
}
define('BASE_URL', $basePath === '' ? '/' : '/' . $basePath . '/');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

date_default_timezone_set('America/Sao_Paulo');
