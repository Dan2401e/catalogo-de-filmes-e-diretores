<?php
require_once __DIR__ . '/config.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$usuario = 'root';
$senha = ''; // padrão do XAMPP; altere se você configurou uma senha
$banco = 'catalogo_filmes';

try {
    $conexao = new mysqli($host, $usuario, $senha, $banco);
    $conexao->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    die('Não foi possível conectar ao MySQL. Confira se o MySQL do XAMPP está ligado e se o banco foi importado.');
}
