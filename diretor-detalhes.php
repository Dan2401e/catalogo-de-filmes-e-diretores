<?php
require_once __DIR__ . '/includes/auth.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$tmdb = tmdb_client();
$person = $id ? $tmdb->get('person/' . $id, ['append_to_response' => 'movie_credits']) : null;
if (!$person) {
    $pageTitle = 'Diretor não encontrado';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-erro">Diretor não encontrado ou TMDB não configurado.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}
$filmes = $person['movie_credits']['crew'] ?? [];
$filmes = array_values(array_filter($filmes, fn($f) => ($f['job'] ?? '') === 'Director'));
usort($filmes, fn($a, $b) => strcmp((string) ($b['release_date'] ?? ''), (string) ($a['release_date'] ?? '')));
$pageTitle = $person['name'] ?? 'Diretor';
require_once __DIR__ . '/includes/header.php';
?>
<div class="person-hero">
    <img src="<?= e(TmdbClient::poster($person['profile_path'] ?? null, 'h632')) ?>" alt="Foto de <?= e($person['name'] ?? '') ?>">
    <div>
        <p class="eyebrow">DIRETOR</p><h1><?= e($person['name'] ?? '') ?></h1>
        <p class="muted"><?= e($person['place_of_birth'] ?? 'Local de nascimento não informado') ?><?php if (!empty($person['birthday'])): ?> · <?= e(date('d/m/Y', strtotime($person['birthday']))) ?><?php endif; ?></p>
        <h2>Biografia</h2>
        <p class="synopsis"><?= e($person['biography'] ?: 'Biografia não disponível em português.') ?></p>
    </div>
</div>
<section class="detail-section"><h2>Filmes dirigidos</h2><div class="tmdb-grid">
<?php foreach (array_slice($filmes, 0, 12) as $filme): ?>
    <article class="tmdb-card">
        <a href="<?= e(url('filme-detalhes.php?id=' . (int) $filme['id'])) ?>"><img src="<?= e(TmdbClient::poster($filme['poster_path'] ?? null)) ?>" alt="<?= e($filme['title'] ?? '') ?>"></a>
        <div class="tmdb-card-body"><h3><a href="<?= e(url('filme-detalhes.php?id=' . (int) $filme['id'])) ?>"><?= e($filme['title'] ?? '') ?></a></h3><p class="muted"><?= e(substr((string) ($filme['release_date'] ?? ''), 0, 4) ?: 'Ano não informado') ?></p></div>
    </article>
<?php endforeach; ?>
</div></section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
