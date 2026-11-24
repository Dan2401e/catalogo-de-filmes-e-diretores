<?php
require_once __DIR__ . '/includes/auth.php';
header('Content-Type: application/json; charset=utf-8');

$tmdb = tmdb_client();
$popular = $tmdb->get('movie/popular', ['page' => 1]);
if (!$popular) {
    http_response_code(502);
    echo json_encode(['error' => 'Não foi possível carregar diretores populares do TMDB.']);
    exit;
}

$diretores = [];
foreach (array_slice($popular['results'] ?? [], 0, 10) as $filme) {
    $filmeId = filter_var($filme['id'] ?? null, FILTER_VALIDATE_INT);
    if (!$filmeId) continue;

    $creditos = $tmdb->get('movie/' . $filmeId . '/credits');
    foreach ($creditos['crew'] ?? [] as $pessoa) {
        if (($pessoa['job'] ?? '') !== 'Director') continue;
        $id = filter_var($pessoa['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id || isset($diretores[$id])) continue;
        $diretores[$id] = ['id' => $id, 'name' => (string) ($pessoa['name'] ?? '')];
    }
}

echo json_encode(['results' => array_values($diretores)], JSON_UNESCAPED_UNICODE);
