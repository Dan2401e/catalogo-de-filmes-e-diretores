<?php
require_once __DIR__ . '/includes/auth.php';

$stmt = $conexao->prepare('SELECT tmdb_movie_id, titulo, poster_path, ano FROM favoritos WHERE usuario_id = ? ORDER BY criado_em DESC');
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$favoritos = $stmt->get_result();
$pageTitle = 'Meus favoritos';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">MINHA LISTA</p><h1>Filmes favoritos</h1><p class="muted">Sua seleção pessoal do TMDB.</p></div>
    <a class="btn btn-outline" href="<?= e(url('biblioteca-tmdb.php')) ?>">Explorar filmes</a>
</div>
<?php if ($favoritos->num_rows === 0): ?>
    <div class="panel empty-state">Você ainda não adicionou filmes aos favoritos.</div>
<?php else: ?>
    <div class="tmdb-grid">
    <?php while ($f = $favoritos->fetch_assoc()):
        $path = is_string($f['poster_path']) ? $f['poster_path'] : null;
        $poster = $path && preg_match('~\A/[A-Za-z0-9._-]+\z~', $path) ? 'https://image.tmdb.org/t/p/w500' . $path : url('assets/img/sem-imagem.svg');
    ?>
        <article class="tmdb-card">
            <a href="<?= e(url('filme-detalhes.php?id=' . (int) $f['tmdb_movie_id'])) ?>">
                <img src="<?= e($poster) ?>" alt="Pôster de <?= e($f['titulo']) ?>">
            </a>
            <div class="tmdb-card-body">
                <h3><a href="<?= e(url('filme-detalhes.php?id=' . (int) $f['tmdb_movie_id'])) ?>"><?= e($f['titulo']) ?></a></h3>
                <p class="muted"><?= e((string) ($f['ano'] ?: 'Ano não informado')) ?></p>
                <form action="<?= e(url('favoritos.php')) ?>" method="post" onsubmit="return confirm('Remover dos favoritos?')">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="acao" value="remover">
                    <input type="hidden" name="tmdb_id" value="<?= (int) $f['tmdb_movie_id'] ?>">
                    <input type="hidden" name="retorno" value="meus-favoritos.php">
                    <button class="btn btn-small btn-danger btn-full" type="submit">Remover</button>
                </form>
            </div>
        </article>
    <?php endwhile; ?>
    </div>
<?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
