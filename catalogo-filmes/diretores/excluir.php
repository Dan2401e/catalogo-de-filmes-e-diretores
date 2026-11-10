<?php
require_once dirname(__DIR__) . '/includes/auth.php';
exigir_post();
validar_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

try {
    if (!$id) throw new RuntimeException('Diretor inválido.');
    $stmt = $conexao->prepare('SELECT foto FROM diretores WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $diretor = $stmt->get_result()->fetch_assoc();
    if (!$diretor) throw new RuntimeException('Diretor não encontrado.');

    $stmt = $conexao->prepare('DELETE FROM diretores WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    excluir_imagem($diretor['foto']);
    mensagem('sucesso', 'Diretor excluído.');
} catch (mysqli_sql_exception $e) {
    mensagem('erro', 'Este diretor possui filmes vinculados e não pode ser excluído.');
} catch (Throwable $e) {
    mensagem('erro', $e->getMessage());
}
redirecionar('diretores/index.php');
