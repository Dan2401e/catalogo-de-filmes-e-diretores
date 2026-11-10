<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
$basePath = str_replace('\\', '/', dirname($scriptName));

if ($basePath === '/' || $basePath === '.') {
    $basePath = '';
}

define('BASE_URL', $basePath === '' ? '/' : rtrim($basePath, '/') . '/');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');

date_default_timezone_set('America/Sao_Paulo');
