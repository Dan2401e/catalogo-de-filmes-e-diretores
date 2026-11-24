<?php
require_once dirname(__DIR__) . '/config/funcoes.php';

$pageTitle = $pageTitle ?? 'Catálogo de Filmes';
$flash = obter_mensagem();
$caminhoAtual = str_replace('\\', '/', $_SERVER['PHP_SELF'] ?? '');
$navAtiva = static function (string $trecho) use ($caminhoAtual): string {
    return str_contains($caminhoAtual, $trecho) ? 'active' : '';
};
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#070b14">
    <meta name="description" content="CineCatalog — organize filmes, diretores e descubra histórias do cinema.">
    <title><?= e($pageTitle) ?> | CineCatalog</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="CineCatalog — início">
            <span class="brand-mark" aria-hidden="true">▶</span>
            <span>CineCatalog</span>
        </a>

        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
            <span class="menu-toggle-icon" aria-hidden="true"></span>
            <span class="sr-only">Abrir menu</span>
        </button>

        <nav id="primary-navigation" aria-label="Navegação principal">
            <a class="<?= e($navAtiva('/biblioteca-tmdb.php')) ?>" href="<?= e(url('biblioteca-tmdb.php')) ?>">Explorar</a>
            <a class="<?= e($navAtiva('/meus-favoritos.php')) ?>" href="<?= e(url('meus-favoritos.php')) ?>">Favoritos</a>
        </nav>

        <div class="user-area">
            <span><?= e($_SESSION['usuario_nome'] ?? '') ?></span>
            <a class="btn btn-small btn-outline" href="<?= e(url('logout.php')) ?>">Sair</a>
        </div>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['tipo']) ?>" role="status"><?= e($flash['texto']) ?></div>
        <?php endif; ?>