<?php
require_once __DIR__ . '/includes/auth.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tmdb = tmdb_client();
$filme = $id ? $tmdb->get('movie/' . $id, ['append_to_response' => 'credits,videos']) : null;
if (!$filme) {
    $pageTitle = 'Filme não encontrado';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-erro">Filme não encontrado ou TMDB não configurado.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
$creditos = is_array($filme['credits'] ?? null) ? $filme['credits'] : [];
$diretor = null;
foreach ($creditos['crew'] ?? [] as $membro) {
    if (($membro['job'] ?? '') === 'Director') { $diretor = $membro; break; }
}
$trailer = null;
foreach ($filme['videos']['results'] ?? [] as $video) {
    if (($video['site'] ?? '') === 'YouTube' && in_array($video['type'] ?? '', ['Trailer', 'Teaser'], true)) {
        $trailer = $video['key'] ?? null;
        break;
    }
}
$stmt = $conexao->prepare('SELECT id FROM favoritos WHERE usuario_id = ? AND tmdb_movie_id = ? LIMIT 1');
$stmt->bind_param('ii', $_SESSION['usuario_id'], $id);
$stmt->execute();
$favorito = (bool) $stmt->get_result()->fetch_assoc();
$backdrop = TmdbClient::backdrop($filme['backdrop_path'] ?? null);
$pageTitle = $filme['title'] ?? 'Detalhes';
require_once __DIR__ . '/includes/header.php';
?>
<article class="tmdb-details">
    <?php if ($backdrop): ?><div class="details-backdrop" style="background-image:url('<?= e($backdrop) ?>')"></div><?php endif; ?>
    <div class="details-layout">
        <img class="details-poster" src="<?= e(TmdbClient::poster($filme['poster_path'] ?? null, 'w780')) ?>" alt="Pôster de <?= e($filme['title'] ?? '') ?>">
        <div class="details-content">
            <p class="eyebrow">FILME</p>
            <h1><?= e($filme['title'] ?? 'Sem título') ?></h1>
            <?php if (!empty($filme['tagline'])): ?><p class="tagline"><?= e($filme['tagline']) ?></p><?php endif; ?>
            <div class="details-meta">
                <span>★ <?= e(number_format((float) ($filme['vote_average'] ?? 0), 1, ',', '.')) ?> (<?= e((string) ($filme['vote_count'] ?? 0)) ?> votos)</span>
                <span><?= e(substr((string) ($filme['release_date'] ?? ''), 0, 4) ?: 'Ano não informado') ?></span>
                <?php if (!empty($filme['runtime'])): ?><span><?= (int) $filme['runtime'] ?> min</span><?php endif; ?>
            </div>
            <div class="genre-list"><?php foreach ($filme['genres'] ?? [] as $genero): ?><span><?= e($genero['name'] ?? '') ?></span><?php endforeach; ?></div>
            <h2>Sinopse</h2>
            <p class="synopsis"><?= e($filme['overview'] ?: 'Sinopse não disponível em português.') ?></p>
            <?php if ($diretor): ?><p><strong>Direção:</strong> <a class="text-link" href="<?= e(url('diretor-detalhes.php?id=' . (int) $diretor['id'])) ?>"><?= e($diretor['name']) ?></a></p><?php endif; ?>
            <form action="<?= e(url('favoritos.php')) ?>" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="acao" value="<?= $favorito ? 'remover' : 'adicionar' ?>">
                <input type="hidden" name="tmdb_id" value="<?= (int) $id ?>">
                <input type="hidden" name="titulo" value="<?= e($filme['title'] ?? '') ?>">
                <input type="hidden" name="poster" value="<?= e(is_string($filme['poster_path'] ?? null) ? $filme['poster_path'] : '') ?>">
                <input type="hidden" name="ano" value="<?= e(substr((string) ($filme['release_date'] ?? ''), 0, 4)) ?>">
                <input type="hidden" name="retorno" value="<?= e('filme-detalhes.php?id=' . (int) $id) ?>">
                <button class="btn <?= $favorito ? 'btn-danger' : 'btn-primary' ?>" type="submit"><?= $favorito ? 'Remover dos favoritos' : 'Adicionar aos favoritos' ?></button>
            </form>
        </div>
    </div>

    <?php if ($trailer): ?>
        <section class="detail-section"><h2>Trailer</h2><div class="video-wrap"><iframe src="https://www.youtube-nocookie.com/embed/<?= e($trailer) ?>" title="Trailer" allowfullscreen loading="lazy"></iframe></div></section>
    <?php endif; ?>

    <section class="detail-section">
        <h2>Elenco principal</h2>
        <div class="cast-grid">
        <?php foreach (array_slice($creditos['cast'] ?? [], 0, 12) as $ator): ?>
            <article class="cast-card">
                <img src="<?= e(TmdbClient::poster($ator['profile_path'] ?? null, 'w185')) ?>" alt="<?= e($ator['name'] ?? '') ?>">
                <strong><?= e($ator['name'] ?? '') ?></strong>
                <span><?= e($ator['character'] ?? '') ?></span>
            </article>
        <?php endforeach; ?>
        </div>
    </section>
    <p class="tmdb-credit">Dados fornecidos por TMDB. This product uses the TMDB API but is not endorsed or certified by TMDB.</p>
</article>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
