<?php
require_once __DIR__ . '/includes/auth.php';
$tmdb = tmdb_client();
$busca = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
$genero = filter_input(INPUT_GET, 'genero', FILTER_VALIDATE_INT) ?: 0;
$diretorId = $busca === '' ? (filter_input(INPUT_GET, 'diretor_id', FILTER_VALIDATE_INT) ?: 0) : 0;
$pagina = max(1, filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT) ?: 1);
$colecao = in_array($_GET['colecao'] ?? '', ['popular', 'now_playing', 'top_rated', 'upcoming'], true)
    ? $_GET['colecao']
    : 'popular';

$generosData = $tmdb->get('genre/movie/list');
$generos = is_array($generosData['genres'] ?? null) ? $generosData['genres'] : [];
$diretorSelecionado = null;
if ($diretorId) {
    $pessoa = $tmdb->get('person/' . $diretorId);
    if ($pessoa && isset($pessoa['name'])) {
        $diretorSelecionado = ['id' => $diretorId, 'name' => (string) ($pessoa['name'] ?? '')];
    } else {
        $diretorId = 0;
    }
}
if ($busca !== '') {
    $filmesPorTitulo = $tmdb->get('search/movie', ['query' => $busca, 'page' => $pagina]);
    $pessoas = $tmdb->get('search/person', ['query' => $busca, 'page' => 1]);
    if (!$filmesPorTitulo && !$pessoas) {
        $resultado = null;
    } else {
        $filmesEncontrados = [];

        foreach ($filmesPorTitulo['results'] ?? [] as $filme) {
            $filmesEncontrados[(int) $filme['id']] = $filme;
        }

        foreach (array_slice($pessoas['results'] ?? [], 0, 5) as $pessoa) {
            $pessoaId = filter_var($pessoa['id'] ?? null, FILTER_VALIDATE_INT);
            if (!$pessoaId) continue;

            $creditos = $tmdb->get('person/' . $pessoaId . '/movie_credits');
            $filmesDirigidos = array_values(array_filter(
                $creditos['crew'] ?? [],
                static fn(array $filme): bool => ($filme['job'] ?? '') === 'Director'
            ));
            usort($filmesDirigidos, static fn(array $a, array $b): int => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));
            foreach (array_slice($filmesDirigidos, 0, 20) as $filme) {
                $filmesEncontrados[(int) $filme['id']] = $filme;
            }
        }

        $filmesEncontrados = array_values(array_filter(
            $filmesEncontrados,
            static fn(array $filme): bool => !$genero || in_array($genero, $filme['genre_ids'] ?? [], true)
        ));
        usort($filmesEncontrados, static fn(array $a, array $b): int => ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0));

        $totalPaginasBusca = max(1, (int) ceil(count($filmesEncontrados) / 20));
        $resultado = [
            'results' => array_slice($filmesEncontrados, ($pagina - 1) * 20, 20),
            'total_pages' => $totalPaginasBusca,
        ];
    }
} elseif ($diretorId) {
    $creditos = $tmdb->get('person/' . $diretorId . '/movie_credits');
    if ($creditos) {
        $filmesDiretor = array_values(array_filter(
            $creditos['crew'] ?? [],
            static function (array $filme) use ($genero, $colecao): bool {
                if (($filme['job'] ?? '') !== 'Director') return false;
                if ($genero && !in_array($genero, $filme['genre_ids'] ?? [], true)) return false;

                $data = (string) ($filme['release_date'] ?? '');
                $hoje = date('Y-m-d');
                if ($colecao === 'now_playing' && ($data < date('Y-m-d', strtotime('-45 days')) || $data > $hoje)) return false;
                if ($colecao === 'upcoming' && ($data < $hoje || $data === '')) return false;
                if ($colecao === 'top_rated' && (int) ($filme['vote_count'] ?? 0) < 200) return false;
                return true;
            }
        ));
        usort($filmesDiretor, static function (array $a, array $b) use ($colecao): int {
            if ($colecao === 'top_rated') return ($b['vote_average'] ?? 0) <=> ($a['vote_average'] ?? 0);
            if ($colecao === 'now_playing' || $colecao === 'upcoming') {
                if ($colecao === 'upcoming') return strcmp((string) ($a['release_date'] ?? ''), (string) ($b['release_date'] ?? ''));
                return strcmp((string) ($b['release_date'] ?? ''), (string) ($a['release_date'] ?? ''));
            }
            return ($b['popularity'] ?? 0) <=> ($a['popularity'] ?? 0);
        });
        $totalPaginasDiretor = max(1, (int) ceil(count($filmesDiretor) / 20));
        $resultado = [
            'results' => array_slice($filmesDiretor, ($pagina - 1) * 20, 20),
            'total_pages' => $totalPaginasDiretor,
        ];
    } else {
        $resultado = null;
    }
} else {
    $filtros = [
        'page' => $pagina,
        'with_genres' => $genero ?: null,
        'sort_by' => 'popularity.desc',
        'region' => 'BR',
    ];
    if ($colecao === 'now_playing') {
        $filtros['primary_release_date.gte'] = date('Y-m-d', strtotime('-45 days'));
        $filtros['primary_release_date.lte'] = date('Y-m-d');
        $filtros['sort_by'] = 'primary_release_date.desc';
    } elseif ($colecao === 'top_rated') {
        $filtros['sort_by'] = 'vote_average.desc';
        $filtros['vote_count.gte'] = 200;
    } elseif ($colecao === 'upcoming') {
        $filtros['primary_release_date.gte'] = date('Y-m-d');
        $filtros['sort_by'] = 'primary_release_date.asc';
    }
    $resultado = $tmdb->get('discover/movie', $filtros);
}
$filmes = is_array($resultado['results'] ?? null) ? $resultado['results'] : [];
$totalPaginas = min(500, (int) ($resultado['total_pages'] ?? 1));

$pageTitle = 'Descobrir filmes';
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-heading">
    <div><p class="eyebrow">CATÁLOGO TMDB</p><h1>Explorar filmes</h1><p class="muted">Pesquise, filtre e descubra novas histórias.</p></div>
    <a class="btn btn-outline" href="<?= e(url('meus-favoritos.php')) ?>">Meus favoritos</a>
</div>

<?php if (!$tmdb->configurado()): ?>
    <div class="alert alert-erro">Configure o token TMDB em <strong>config/local.php</strong> ou na variável de ambiente <strong>CINECATALOG_TMDB_TOKEN</strong>.</div>
<?php elseif (!$resultado): ?>
    <div class="alert alert-erro">Não foi possível carregar o catálogo do TMDB agora. Confira sua conexão e tente novamente.</div>
<?php endif; ?>

<section class="panel filter-panel">
    <form method="get" class="filter-form tmdb-filter-form">
        <div class="field search-field"><label for="q">Pesquisar filme ou diretor</label><input id="q" type="search" name="q" value="<?= e($busca) ?>" placeholder="Título do filme ou nome do diretor"></div>
        <div class="field"><label for="genero">Gênero</label><select id="genero" name="genero"><option value="">Todos</option><?php foreach ($generos as $g): ?><option value="<?= (int) $g['id'] ?>" <?= $genero === (int) $g['id'] ? 'selected' : '' ?>><?= e($g['name']) ?></option><?php endforeach; ?></select></div>
        <div class="field"><label for="director-id">Diretor popular</label><select id="director-id" name="diretor_id" data-popular-url="<?= e(url('diretores-populares.php')) ?>" data-genres-url="<?= e(url('generos-diretor.php')) ?>" data-selected-name="<?= e($diretorSelecionado['name'] ?? '') ?>" <?= $busca !== '' ? 'disabled' : '' ?>><option value=""><?= $diretorSelecionado ? 'Todos os diretores' : 'Carregando diretores...' ?></option><?php if ($diretorSelecionado): ?><option value="<?= (int) $diretorSelecionado['id'] ?>" selected><?= e($diretorSelecionado['name']) ?></option><?php endif; ?></select><small id="director-status" class="filter-hint" aria-live="polite">Selecione um diretor para ver os gêneros dos filmes dele.</small></div>
        <div class="field"><label for="colecao">Categoria</label><select id="colecao" name="colecao" <?= $busca !== '' ? 'disabled' : '' ?>><option value="popular" <?= $colecao === 'popular' ? 'selected' : '' ?>>Populares</option><option value="now_playing" <?= $colecao === 'now_playing' ? 'selected' : '' ?>>Em cartaz</option><option value="top_rated" <?= $colecao === 'top_rated' ? 'selected' : '' ?>>Mais bem avaliados</option><option value="upcoming" <?= $colecao === 'upcoming' ? 'selected' : '' ?>>Próximos lançamentos</option></select></div>
        <div class="filter-actions"><button class="btn btn-primary" type="submit">Buscar</button><a class="btn btn-outline" href="<?= e(url('biblioteca-tmdb.php')) ?>">Limpar</a></div>
    </form>
    <?php if ($busca !== ''): ?><p class="filter-hint">Resultados por título e por filmes dirigidos por pessoas com esse nome. Limpe a pesquisa para usar os filtros de diretor popular e categoria.</p><?php endif; ?>
</section>

<?php if (!$tmdb->configurado()): ?>
    <div class="panel empty-state">A busca ficará disponível após a configuração da API.</div>
<?php elseif (!$resultado): ?>
    <div class="panel empty-state">Os filmes não puderam ser carregados. Tente novamente em alguns instantes.</div>
<?php elseif (!$filmes): ?>
    <div class="panel empty-state">Nenhum filme encontrado para esta pesquisa.</div>
<?php else: ?>
    <div class="tmdb-grid">
    <?php foreach ($filmes as $filme): ?>
        <article class="tmdb-card">
            <a href="<?= e(url('filme-detalhes.php?id=' . (int) $filme['id'])) ?>">
                <img src="<?= e(TmdbClient::poster($filme['poster_path'] ?? null)) ?>" alt="Pôster de <?= e($filme['title'] ?? 'filme') ?>">
            </a>
            <div class="tmdb-card-body">
                <span class="tmdb-rating">★ <?= e(number_format((float) ($filme['vote_average'] ?? 0), 1, ',', '.')) ?></span>
                <h3><a href="<?= e(url('filme-detalhes.php?id=' . (int) $filme['id'])) ?>"><?= e($filme['title'] ?? 'Sem título') ?></a></h3>
                <p class="muted"><?= e(substr((string) ($filme['release_date'] ?? ''), 0, 4) ?: 'Ano não informado') ?></p>
                <form action="<?= e(url('favoritos.php')) ?>" method="post">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="acao" value="adicionar">
                    <input type="hidden" name="tmdb_id" value="<?= (int) $filme['id'] ?>">
                    <input type="hidden" name="titulo" value="<?= e($filme['title'] ?? '') ?>">
                    <input type="hidden" name="poster" value="<?= e(is_string($filme['poster_path'] ?? null) ? $filme['poster_path'] : '') ?>">
                    <input type="hidden" name="ano" value="<?= e(substr((string) ($filme['release_date'] ?? ''), 0, 4)) ?>">
                    <input type="hidden" name="retorno" value="<?= e('biblioteca-tmdb.php?' . http_build_query(['q' => $busca, 'genero' => $genero ?: null, 'diretor_id' => $diretorId ?: null, 'colecao' => $colecao, 'pagina' => $pagina])) ?>">
                    <button class="btn btn-small btn-outline btn-full" type="submit">+ Favorito</button>
                </form>
            </div>
        </article>
    <?php endforeach; ?>
    </div>
    <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <?php if ($pagina > 1): ?><a class="btn btn-outline" href="<?= e(url('biblioteca-tmdb.php?' . http_build_query(['q' => $busca, 'genero' => $genero ?: null, 'diretor_id' => $diretorId ?: null, 'colecao' => $colecao, 'pagina' => $pagina - 1]))) ?>">← Anterior</a><?php endif; ?>
            <span>Página <?= $pagina ?> de <?= $totalPaginas ?></span>
            <?php if ($pagina < $totalPaginas): ?><a class="btn btn-outline" href="<?= e(url('biblioteca-tmdb.php?' . http_build_query(['q' => $busca, 'genero' => $genero ?: null, 'diretor_id' => $diretorId ?: null, 'colecao' => $colecao, 'pagina' => $pagina + 1]))) ?>">Próxima →</a><?php endif; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
<script src="<?= e(url('assets/js/director-filter.js')) ?>" defer></script>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
