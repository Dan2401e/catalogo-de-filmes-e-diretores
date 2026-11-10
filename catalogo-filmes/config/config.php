<?php

declare(strict_types=1);

// Inicia a sessão global para login, flash messages e autenticação.
if (session_status() === PHP_SESSION_NONE) {
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
