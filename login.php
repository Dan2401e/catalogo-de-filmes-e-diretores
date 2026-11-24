<?php
require_once __DIR__ . '/config/funcoes.php';
require_once __DIR__ . '/config/oauth.php';

if (!empty($_SESSION['usuario_id'])) {
    redirecionar('index.php');
}

$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $usuario = is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';

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
$provedoresOAuth = [
    'google' => 'Google',
    'github' => 'GitHub',
    'microsoft' => 'Microsoft',
    'apple' => 'Apple',
];
$provedoresDisponiveis = array_filter(
    $provedoresOAuth,
    static fn (string $nome, string $provedor): bool => oauth_provider_ready($provedor),
    ARRAY_FILTER_USE_BOTH
);
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

        <div class="alert alert-info login-demo" role="note">
            Acesso de demonstração local: <strong>admin</strong> / <strong>123</strong>.
            Altere ou remova essa conta antes de qualquer publicação.
        </div>

        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="usuario">Usuário</label>
            <input id="usuario" type="text" name="usuario" required maxlength="60" autocomplete="username">

            <label for="senha">Senha</label>
            <div class="password-field">
                <input id="senha" type="password" name="senha" required autocomplete="current-password">
                <button class="password-toggle" type="button" aria-label="Mostrar senha" aria-pressed="false" hidden>Mostrar</button>
            </div>

            <button class="btn btn-primary btn-full" type="submit">Entrar</button>
        </form>

        <div class="oauth-divider"><span>ou entre com</span></div>
        <?php if ($provedoresDisponiveis): ?>
            <div class="oauth-buttons">
                <?php foreach ($provedoresDisponiveis as $provedor => $nome): ?>
                    <a class="oauth-button" href="<?= e(url('oauth-start.php?provider=' . $provedor)) ?>">
                        <span class="oauth-mark" aria-hidden="true"><?= e(substr($nome, 0, 1)) ?></span>
                        Continuar com <?= e($nome) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="oauth-note">Configure os provedores e execute <code>composer install</code> para habilitar o login social.</p>
        <?php endif; ?>

        <p class="login-help">Ainda não tem uma conta? <a href="<?= e(url('cadastro.php')) ?>">Cadastre-se</a>.</p>
    </section>
    <script src="<?= e(url('assets/js/password-toggle.js')) ?>" defer></script>
</body>
</html>
