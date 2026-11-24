<?php
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Prefer environment variables. A local.php file may be used for XAMPP and is ignored by Git.
$configLocal = [];
$arquivoLocal = __DIR__ . '/local.php';
if (is_file($arquivoLocal)) {
    $configLocal = require $arquivoLocal;
    if (!is_array($configLocal)) {
        $configLocal = [];
    }
}

$host = getenv('CINECATALOG_DB_HOST') ?: ($configLocal['host'] ?? '127.0.0.1');
$usuario = getenv('CINECATALOG_DB_USER') ?: ($configLocal['usuario'] ?? 'root');
$senha = getenv('CINECATALOG_DB_PASSWORD');
if ($senha === false) {
    $senha = $configLocal['senha'] ?? '';
}
$banco = getenv('CINECATALOG_DB_NAME') ?: ($configLocal['banco'] ?? 'catalogo_filmes');
$porta = (int) (getenv('CINECATALOG_DB_PORT') ?: ($configLocal['porta'] ?? 3306));

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco, $porta);
    $conexao->set_charset('utf8mb4');
} catch (Throwable $e) {
    error_log('[CineCatalog] Falha na conexão MySQL: ' . $e->getMessage());
    http_response_code(500);
    die('Não foi possível conectar ao MySQL. Verifique o serviço, o banco e as configurações locais.');
}
