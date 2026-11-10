<?php
require_once dirname(__DIR__) . '/includes/auth.php';
exigir_post();
validar_csrf();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT) ?: 0;
$nome = trim($_POST['nome'] ?? '');
$nacionalidade = trim($_POST['nacionalidade'] ?? '');
$data = trim($_POST['data_nascimento'] ?? '');
$biografia = trim($_POST['biografia'] ?? '');

try {
    if ($nome === '') throw new RuntimeException('O nome do diretor é obrigatório.');
    $data = validar_data_br($data);

    $atual = null;
    if ($id) {
        $stmt = $conexao->prepare('SELECT foto FROM diretores WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $atual = $stmt->get_result()->fetch_assoc();
        if (!$atual) throw new RuntimeException('Diretor não encontrado.');
    }

    $fotoNova = salvar_imagem('foto', 'diretores');
    $foto = $fotoNova ?? ($atual['foto'] ?? null);

    if ($id) {
        $stmt = $conexao->prepare('UPDATE diretores SET nome=?, nacionalidade=?, data_nascimento=NULLIF(?, ""), biografia=?, foto=? WHERE id=?');
        $stmt->bind_param('sssssi', $nome, $nacionalidade, $data, $biografia, $foto, $id);
        $stmt->execute();
        if ($fotoNova) excluir_imagem($atual['foto']);
        mensagem('sucesso', 'Diretor atualizado com sucesso.');
    } else {
        $stmt = $conexao->prepare('INSERT INTO diretores (nome, nacionalidade, data_nascimento, biografia, foto) VALUES (?, ?, NULLIF(?, ""), ?, ?)');
        $stmt->bind_param('sssss', $nome, $nacionalidade, $data, $biografia, $foto);
        $stmt->execute();
        mensagem('sucesso', 'Diretor cadastrado com sucesso.');
    }
} catch (Throwable $e) {
    if (!empty($fotoNova)) excluir_imagem($fotoNova);
    mensagem('erro', $e->getMessage());
}
redirecionar('diretores/index.php');
