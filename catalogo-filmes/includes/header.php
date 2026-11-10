<?php
require_once dirname(__DIR__) . '/config/funcoes.php';

$pageTitle = $pageTitle ?? 'Catálogo de Filmes';
$flash = obter_mensagem();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | CineCatalog</title>
    <link rel="stylesheet" href="<?= e(url('assets/css/style.css')) ?>" type="text/css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="<?= e(url('index.php')) ?>">
            <span class="brand-mark">▶</span> CineCatalog
        </a>

        <nav>
            <a href="<?= e(url('index.php')) ?>">Início</a>
            <a href="<?= e(url('diretores/index.php')) ?>">Diretores</a>
            <a href="<?= e(url('filmes/index.php')) ?>">Filmes</a>
            <a href="<?= e(url('biblioteca/index.php')) ?>">Biblioteca</a>
        </nav>

        <div class="user-area">
            <span><?= e($_SESSION['usuario_nome'] ?? '') ?></span>
            <a class="btn btn-small btn-outline" href="<?= e(url('logout.php')) ?>">Sair</a>
        </div>
    </header>

    <main class="container">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['tipo']) ?>"><?= e($flash['texto']) ?></div>
        <?php endif; ?>
