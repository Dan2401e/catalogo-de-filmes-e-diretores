<?php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

$directorId = filter_input(INPUT_GET, 'diretor_id', FILTER_VALIDATE_INT);
if (!$directorId || $directorId < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'Diretor inválido.']);
    exit;
}

$tmdb = tmdb_client();
$person = $tmdb->get('person/' . $directorId);
$credits = $tmdb->get('person/' . $directorId . '/movie_credits');
$genresData = $tmdb->get('genre/movie/list');
if (!$person || !$credits || !$genresData) {
    http_response_code(502);
    echo json_encode(['error' => 'Não foi possível carregar os gêneros deste diretor.']);
    exit;
}

$ids = [];
foreach ($credits['crew'] ?? [] as $movie) {
    if (($movie['job'] ?? '') === 'Director') {
        foreach ($movie['genre_ids'] ?? [] as $genreId) $ids[(int) $genreId] = true;
    }
}

$genres = array_values(array_filter(
    $genresData['genres'] ?? [],
    static fn(array $genre): bool => isset($ids[(int) $genre['id']])
));
echo json_encode(['results' => $genres], JSON_UNESCAPED_UNICODE);
