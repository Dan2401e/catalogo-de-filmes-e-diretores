<?php
require_once __DIR__ . '/includes/auth.php';

$totalDiretores = (int) $conexao->query('SELECT COUNT(*) total FROM diretores')->fetch_assoc()['total'];
$totalFilmes = (int) $conexao->query('SELECT COUNT(*) total FROM filmes')->fetch_assoc()['total'];
$recentes = $conexao->query(
    'SELECT f.id, f.titulo, f.ano, f.genero, f.capa, d.nome AS diretor
     FROM filmes f INNER JOIN diretores d ON d.id = f.diretor_id
     ORDER BY f.id DESC LIMIT 4'
);

$pageTitle = 'Painel inicial';
require_once __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">PAINEL DE CONTROLE · CINECATALOG</p>
        <h1>Olá, <?= e($_SESSION['usuario_nome'] ?? 'cinéfilo') ?>.</h1>
        <p class="hero-title">Histórias incríveis começam por aqui.</p>
        <p class="hero-text">Organize filmes, conheça seus diretores e mantenha toda a sua coleção cinematográfica em um só lugar.</p>
        <a class="btn btn-primary" href="<?= e(url('biblioteca/index.php')) ?>">Explorar biblioteca <span aria-hidden="true">→</span></a>
    </div>
    <div class="hero-decoration" aria-hidden="true">YOUR<br><span>CINEMA</span></div>
</section>

<section class="stats-grid" aria-label="Resumo do catálogo">
    <article class="stat-card">
        <div class="stat-top"><span class="stat-icon" aria-hidden="true">🎬</span><span class="counter">Acervo</span></div>
        <p>Filmes cadastrados</p>
        <strong><?= $totalFilmes ?></strong>
        <a href="<?= e(url('filmes/index.php')) ?>">Gerenciar filmes →</a>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span class="stat-icon" aria-hidden="true">🎥</span><span class="counter">Criadores</span></div>
        <p>Diretores cadastrados</p>
        <strong><?= $totalDiretores ?></strong>
        <a href="<?= e(url('diretores/index.php')) ?>">Ver diretores →</a>
    </article>
</section>

<section class="section-heading">
    <div><p class="eyebrow">ATALHOS</p><h2>O que deseja fazer?</h2></div>
</section>
<div class="shortcut-grid">
    <a class="shortcut-card" href="<?= e(url('diretores/index.php')) ?>"><span aria-hidden="true">🎥</span><h3>Gerenciar diretores</h3><p>Adicione, edite e organize os perfis dos profissionais.</p></a>
    <a class="shortcut-card" href="<?= e(url('filmes/index.php')) ?>"><span aria-hidden="true">🎞️</span><h3>Gerenciar filmes</h3><p>Registre novas obras e associe cada uma ao seu diretor.</p></a>
    <a class="shortcut-card" href="<?= e(url('biblioteca/index.php')) ?>"><span aria-hidden="true">⌕</span><h3>Explorar biblioteca</h3><p>Encontre títulos usando busca e filtros combinados.</p></a>
</div>

<section class="section-heading">
    <div><p class="eyebrow">ATUALIZAÇÕES</p><h2>Adicionados recentemente</h2></div>
    <a class="text-link" href="<?= e(url('biblioteca/index.php')) ?>">Ver catálogo completo →</a>
</section>
<?php if ($recentes->num_rows === 0): ?>
    <div class="panel empty-state">Seu catálogo ainda está vazio. Comece cadastrando um diretor e depois adicione o primeiro filme.</div>
<?php else: ?>
    <div class="movie-grid">
        <?php while ($filme = $recentes->fetch_assoc()): ?>
            <article class="movie-card">
                <img class="movie-poster" src="<?= e(imagem_url($filme['capa'])) ?>" alt="Pôster de <?= e($filme['titulo']) ?>">
                <div class="movie-info">
                    <span class="tag"><?= e($filme['genero']) ?> · <?= e((string) $filme['ano']) ?></span>
                    <h3><?= e($filme['titulo']) ?></h3>
                    <p class="muted">Direção: <?= e($filme['diretor']) ?></p>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>