<?php
require_once __DIR__ . '/config/funcoes.php';

if (!empty($_SESSION['usuario_id'])) {
    redirecionar('index.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $stmt = $conexao->prepare('SELECT id, usuario, senha FROM usuarios WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $registro = $stmt->get_result()->fetch_assoc();

    if ($registro && password_verify($senha, $registro['senha'])) {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = (int) $registro['id'];
        $_SESSION['usuario_nome'] = $registro['usuario'];
        redirecionar('index.php');
    }

    $erro = 'Usuário ou senha incorretos.';
}

$pageTitle = 'Entrar';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | CineCatalog</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>" type="text/css">
</head>
<body class="login-page">
    <section class="login-card">
        <a class="brand login-brand" href="<?= e(url('login.php')) ?>">
            <span class="brand-mark">▶</span> CineCatalog
        </a>

        <p class="eyebrow">SUA BIBLIOTECA DE CINEMA</p>
        <h1>Bem-vindo de volta</h1>
        <p class="muted">Entre para gerenciar seus filmes e diretores.</p>

        <?php if ($erro): ?>
            <div class="alert alert-erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <?php
        $flash = obter_mensagem();
        if ($flash):
            ?>
            <div class="alert alert-<?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label>Usuário</label>
            <input type="text" name="usuario" required maxlength="60" autocomplete="username">

            <label>Senha</label>
            <input type="password" name="senha" required autocomplete="current-password">

            <button class="btn btn-primary btn-full" type="submit">Entrar</button>
        </form>

        <p class="login-help">Primeiro acesso? Execute <strong>criar_admin.php</strong> uma vez.</p>
    </section>
</body>
</html>
