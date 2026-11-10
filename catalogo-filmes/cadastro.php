<?php
require_once __DIR__ . '/config/funcoes.php';

if (!empty($_SESSION['usuario_id'])) {
    redirecionar('index.php');
}

$erro = '';
$usuario = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validar_csrf();

    $usuario = is_string($_POST['usuario'] ?? null) ? trim($_POST['usuario']) : '';
    $senha = is_string($_POST['senha'] ?? null) ? $_POST['senha'] : '';
    $confirmacao = is_string($_POST['confirmar_senha'] ?? null) ? $_POST['confirmar_senha'] : '';

    if (!preg_match('/\A[A-Za-z0-9_.-]{3,60}\z/', $usuario)) {
        $erro = 'O usuário deve ter de 3 a 60 caracteres: letras, números, ponto, hífen ou sublinhado.';
    } elseif (strlen($senha) < 8 || strlen($senha) > 72) {
        $erro = 'A senha deve ter entre 8 e 72 caracteres.';
    } elseif ($senha !== $confirmacao) {
        $erro = 'As senhas não coincidem.';
    } else {
        $stmt = $conexao->prepare('SELECT id FROM usuarios WHERE usuario = ? LIMIT 1');
        $stmt->bind_param('s', $usuario);
        $stmt->execute();

        if ($stmt->get_result()->fetch_assoc()) {
            $erro = 'Esse nome de usuário já está em uso.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            try {
                $stmt = $conexao->prepare('INSERT INTO usuarios (usuario, senha) VALUES (?, ?)');
                $stmt->bind_param('ss', $usuario, $hash);
                $stmt->execute();

                session_regenerate_id(true);
                $_SESSION['usuario_id'] = (int) $conexao->insert_id;
                $_SESSION['usuario_nome'] = $usuario;
                redirecionar('index.php');
            } catch (mysqli_sql_exception $e) {
                if ((int) $e->getCode() === 1062) {
                    $erro = 'Esse nome de usuário já está em uso.';
                } else {
                    throw $e;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta | CineCatalog</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>" type="text/css">
</head>
<body class="login-page">
    <section class="login-card">
        <a class="brand login-brand" href="<?= e(url('login.php')) ?>">
            <span class="brand-mark">▶</span> CineCatalog
        </a>

        <p class="eyebrow">JUNTE-SE AO CATÁLOGO</p>
        <h1>Criar conta</h1>
        <p class="muted">Escolha um usuário e uma senha para começar.</p>

        <?php if ($erro !== ''): ?>
            <div class="alert alert-erro"><?= e($erro) ?></div>
        <?php endif; ?>

        <form method="post" autocomplete="on">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

            <label for="usuario">Usuário</label>
            <input
                id="usuario"
                type="text"
                name="usuario"
                required
                minlength="3"
                maxlength="60"
                pattern="[A-Za-z0-9_.-]{3,60}"
                autocomplete="username"
                value="<?= e($usuario) ?>"
                aria-describedby="usuario-ajuda"
            >
            <small id="usuario-ajuda">Use de 3 a 60 letras, números, ponto, hífen ou sublinhado.</small>

            <label for="senha">Senha</label>
            <div class="password-field">
                <input
                    id="senha"
                    type="password"
                    name="senha"
                    required
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                >
                <button class="password-toggle" type="button" aria-label="Mostrar senha" aria-pressed="false" hidden>Mostrar</button>
            </div>
            <small>Use pelo menos 8 caracteres.</small>

            <label for="confirmar-senha">Confirmar senha</label>
            <div class="password-field">
                <input
                    id="confirmar-senha"
                    type="password"
                    name="confirmar_senha"
                    required
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                >
                <button class="password-toggle" type="button" aria-label="Mostrar senha" aria-pressed="false" hidden>Mostrar</button>
            </div>

            <button class="btn btn-primary btn-full" type="submit">Criar conta</button>
        </form>

        <p class="login-help">Já tem uma conta? <a href="<?= e(url('login.php')) ?>">Entrar</a>.</p>
    </section>
    <script src="<?= e(url('assets/js/password-toggle.js')) ?>" defer></script>
</body>
</html>
