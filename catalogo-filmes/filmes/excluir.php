<?php
require_once dirname(__DIR__) . '/includes/auth.php';
exigir_post();
validar_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

try {
    if (!$id) throw new RuntimeException('Filme inválido.');
    $stmt = $conexao->prepare('SELECT capa FROM filmes WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $filme = $stmt->get_result()->fetch_assoc();
    if (!$filme) throw new RuntimeException('Filme não encontrado.');

    $stmt = $conexao->prepare('DELETE FROM filmes WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    excluir_imagem($filme['capa']);
    mensagem('sucesso', 'Filme excluído e capa removida.');
} catch (Throwable $e) {
    mensagem('erro', $e->getMessage());
}
redirecionar('filmes/index.php');
