<?php
require_once __DIR__ . '/includes/auth.php';

exigir_post();
validar_csrf();
$id = filter_input(INPUT_POST, 'tmdb_id', FILTER_VALIDATE_INT);
$acao = $_POST['acao'] ?? '';
$titulo = is_string($_POST['titulo'] ?? null) ? trim($_POST['titulo']) : '';
$poster = is_string($_POST['poster'] ?? null) ? trim($_POST['poster']) : '';
$ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT) ?: null;
$retorno = is_string($_POST['retorno'] ?? null) ? $_POST['retorno'] : 'biblioteca-tmdb.php';

if (!$id || !in_array($acao, ['adicionar', 'remover'], true)) {
    mensagem('erro', 'Não foi possível atualizar os favoritos.');
    redirecionar($retorno);
}

$usuarioId = (int) $_SESSION['usuario_id'];
if ($acao === 'adicionar') {
    $stmt = $conexao->prepare(
        'INSERT IGNORE INTO favoritos (usuario_id, tmdb_movie_id, titulo, poster_path, ano) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('iissi', $usuarioId, $id, $titulo, $poster, $ano);
    $stmt->execute();
    mensagem('sucesso', $stmt->affected_rows ? 'Filme adicionado aos favoritos.' : 'Este filme já está nos seus favoritos.');
} else {
    $stmt = $conexao->prepare('DELETE FROM favoritos WHERE usuario_id = ? AND tmdb_movie_id = ?');
    $stmt->bind_param('ii', $usuarioId, $id);
    $stmt->execute();
    mensagem('sucesso', 'Filme removido dos favoritos.');
}

if (!preg_match('~\A[A-Za-z0-9/_?.=&%-]+\z~', $retorno)) $retorno = 'biblioteca-tmdb.php';
redirecionar($retorno);
