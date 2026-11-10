<?php
require_once __DIR__ . '/includes/auth.php';

$totalDiretores = (int)$conexao->query('SELECT COUNT(*) total FROM diretores')->fetch_assoc()['total'];
$totalFilmes = (int)$conexao->query('SELECT COUNT(*) total FROM filmes')->fetch_assoc()['total'];
$pageTitle = 'Painel inicial';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div>
        <p class="eyebrow">PAINEL DE CONTROLE</p>
        <h1>Seu universo do cinema, organizado.</h1>
        <p class="hero-text">Cadastre diretores, organize filmografias e encontre seus filmes favoritos em um só lugar.</p>
        <a class="btn btn-primary" href="<?= e(url('filmes/index.php')) ?>">Gerenciar filmes</a>
    </div>
    <div class="hero-decoration">CINEMA<br><span>IS STORIES</span></div>
</section>
<section class="stats-grid">
    <article class="stat-card"><span class="stat-icon">🎬</span><p>Filmes cadastrados</p><strong><?= $totalFilmes ?></strong><a href="<?= e(url('biblioteca/index.php')) ?>">Ver biblioteca →</a></article>
    <article class="stat-card"><span class="stat-icon">🎥</span><p>Diretores cadastrados</p><strong><?= $totalDiretores ?></strong><a href="<?= e(url('diretores/index.php')) ?>">Ver diretores →</a></article>
</section>
<section class="section-heading"><div><p class="eyebrow">ATALHOS</p><h2>O que deseja fazer?</h2></div></section>
<div class="shortcut-grid">
    <a class="shortcut-card" href="<?= e(url('diretores/index.php')) ?>"><span>🎥</span><h3>Cadastrar diretor</h3><p>Adicione e atualize os perfis dos diretores.</p></a>
    <a class="shortcut-card" href="<?= e(url('filmes/index.php')) ?>"><span>🎞️</span><h3>Cadastrar filme</h3><p>Registre títulos e vincule cada obra ao diretor.</p></a>
    <a class="shortcut-card" href="<?= e(url('biblioteca/index.php')) ?>"><span>🔎</span><h3>Explorar biblioteca</h3><p>Pesquise e filtre todos os filmes cadastrados.</p></a>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
