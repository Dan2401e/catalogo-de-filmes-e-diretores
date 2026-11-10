<?php
require_once dirname(__DIR__) . '/includes/auth.php';
exigir_post();
validar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$diretorId = filter_input(INPUT_POST, 'diretor_id', FILTER_VALIDATE_INT);
$titulo = trim($_POST['titulo'] ?? '');
$ano = filter_input(INPUT_POST, 'ano', FILTER_VALIDATE_INT);
$genero = trim($_POST['genero'] ?? '');
$sinopse = trim($_POST['sinopse'] ?? '');
$trailer = null;
$atual = null;
$capaNova = null;

try {
    if (!$diretorId || $titulo === '' || !$ano || $genero === '') {
        throw new RuntimeException('Preencha título, diretor, ano e gênero.');
    }
    if ($ano < 1888 || $ano > (int)date('Y') + 5) throw new RuntimeException('Ano inválido.');
    $stmt = $conexao->prepare('SELECT id FROM diretores WHERE id = ?');
    $stmt->bind_param('i', $diretorId);
    $stmt->execute();
    if (!$stmt->get_result()->fetch_assoc()) throw new RuntimeException('Diretor selecionado não existe.');

    $trailer = validar_link($_POST['trailer_link'] ?? '');
    if ($id) {
        $stmt = $conexao->prepare('SELECT capa FROM filmes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $atual = $stmt->get_result()->fetch_assoc();
        if (!$atual) throw new RuntimeException('Filme não encontrado.');
    }

    $capaNova = salvar_imagem('capa', 'filmes');
    $capa = $capaNova ?? ($atual['capa'] ?? null);

    if ($id) {
        $stmt = $conexao->prepare('UPDATE filmes SET diretor_id=?, titulo=?, ano=?, genero=?, sinopse=?, trailer_link=?, capa=? WHERE id=?');
        $stmt->bind_param('isissssi', $diretorId, $titulo, $ano, $genero, $sinopse, $trailer, $capa, $id);
        $stmt->execute();
        if ($capaNova) excluir_imagem($atual['capa']);
        mensagem('sucesso', 'Filme atualizado com sucesso.');
    } else {
        $stmt = $conexao->prepare('INSERT INTO filmes (diretor_id, titulo, ano, genero, sinopse, trailer_link, capa) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isissss', $diretorId, $titulo, $ano, $genero, $sinopse, $trailer, $capa);
        $stmt->execute();
        mensagem('sucesso', 'Filme cadastrado com sucesso.');
    }
} catch (Throwable $e) {
    if ($capaNova) excluir_imagem($capaNova);
    mensagem('erro', $e->getMessage());
}
redirecionar('filmes/index.php');
