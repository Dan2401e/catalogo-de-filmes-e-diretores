<?php
// Execute este arquivo uma única vez pelo navegador e depois APAGUE-O.
require_once __DIR__ . '/config/funcoes.php';

$usuario = 'admin';
$senha = 'admin123';
$hash = password_hash($senha, PASSWORD_BCRYPT);

$stmt = $conexao->prepare(
    'INSERT INTO usuarios (usuario, senha) VALUES (?, ?)
     ON DUPLICATE KEY UPDATE senha = VALUES(senha)'
);
$stmt->bind_param('ss', $usuario, $hash);
$stmt->execute();

echo 'Usuário administrador criado/atualizado.<br>';
echo 'Login: admin<br>Senha: admin123<br>';
echo 'Por segurança, apague o arquivo criar_admin.php agora.';
