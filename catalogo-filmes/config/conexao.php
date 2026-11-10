<?php
require_once __DIR__ . '/config.php';

// Garante que erros do MySQL sejam exibidos como exceções para melhor tratamento.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Configuração do banco local do XAMPP.
$host = 'localhost';
$usuario = 'root';
$senha = ''; // padrão do XAMPP; altere se você configurou uma senha
$banco = 'catalogo_filmes';

try {
    // Cria a conexão global reutilizada em todas as páginas.
    $conexao = new mysqli($host, $usuario, $senha, $banco);
    $conexao->set_charset('utf8mb4');
} catch (Throwable $e) {
    http_response_code(500);
    die('Não foi possível conectar ao MySQL. Confira se o MySQL do XAMPP está ligado e se o banco foi importado.');
}
