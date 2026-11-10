<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$busca = trim($_GET['busca'] ?? '');
$diretorId = filter_input(INPUT_GET, 'diretor_id', FILTER_VALIDATE_INT) ?: 0;
$genero = trim($_GET['genero'] ?? '');

$sql = 'SELECT f.*, d.nome AS diretor FROM filmes f INNER JOIN diretores d ON d.id=f.diretor_id WHERE 1=1';
$tipos = '';
$valores = [];
if ($busca !== '') {
    $sql .= ' AND (f.titulo LIKE ? OR d.nome LIKE ? OR f.sinopse LIKE ?)';
    $termo = '%' . $busca . '%';
    $tipos .= 'sss';
    array_push($valores, $termo, $termo, $termo);
}
if ($diretorId) {
    $sql .= ' AND f.diretor_id = ?';
    $tipos .= 'i';
    $valores[] = $diretorId;
}
if ($genero !== '') {
    $sql .= ' AND f.genero = ?';
    $tipos .= 's';
    $valores[] = $genero;
}
$sql .= ' ORDER BY f.titulo';

$stmt = $conexao->prepare($sql);
if ($tipos !== '') $stmt->bind_param($tipos, ...$valores);
$stmt->execute();
$filmes = $stmt->get_result();

$diretores = $conexao->query('SELECT id, nome FROM diretores ORDER BY nome');
$generos = $conexao->query('SELECT DISTINCT genero FROM filmes ORDER BY genero');
$pageTitle = 'Biblioteca';
require_once dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">EXPLORE O CATÁLOGO</p><h1>Biblioteca de filmes</h1><p class="muted">Encontre filmes por título, diretor ou gênero.</p></div></div>
<section class="panel filter-panel">
<form method="get" class="filter-form">
    <div class="field search-field"><label>Busca geral</label><input type="search" name="busca" placeholder="Digite título, diretor ou palavra-chave..." value="<?= e($busca) ?>"></div>
    <div class="field"><label>Diretor</label><select name="diretor_id"><option value="">Todos</option><?php while ($d = $diretores->fetch_assoc()): ?><option value="<?= (int)$d['id'] ?>" <?= $diretorId === (int)$d['id'] ? 'selected' : '' ?>><?= e($d['nome']) ?></option><?php endwhile; ?></select></div>
    <div class="field"><label>Gênero</label><select name="genero"><option value="">Todos</option><?php while ($g = $generos->fetch_assoc()): ?><option value="<?= e($g['genero']) ?>" <?= $genero === $g['genero'] ? 'selected' : '' ?>><?= e($g['genero']) ?></option><?php endwhile; ?></select></div>
    <div class="filter-actions"><button class="btn btn-primary" type="submit">Filtrar</button><a class="btn btn-outline" href="<?= e(url('biblioteca/index.php')) ?>">Limpar</a></div>
</form>
</section>
<p class="results-label"><?= $filmes->num_rows ?> resultado(s) encontrado(s)</p>
<?php if ($filmes->num_rows === 0): ?><div class="panel empty-state">Nenhum filme encontrado com esses filtros.</div><?php else: ?>
<div class="movie-grid">
<?php while ($f = $filmes->fetch_assoc()): ?>
<article class="movie-card">
    <img class="movie-poster" src="<?= e(imagem_url($f['capa'])) ?>" alt="Capa de <?= e($f['titulo']) ?>">
    <div class="movie-info"><span class="tag"><?= e($f['genero']) ?> · <?= e((string)$f['ano']) ?></span><h3><?= e($f['titulo']) ?></h3><p class="muted">Direção: <?= e($f['diretor']) ?></p><p><?= e(mb_strimwidth($f['sinopse'] ?? '', 0, 130, '...')) ?></p>
    <?php if ($f['trailer_link']): ?><a class="text-link" href="<?= e($f['trailer_link']) ?>" target="_blank" rel="noopener noreferrer">Assistir ao trailer ↗</a><?php endif; ?>
    </div>
</article>
<?php endwhile; ?>
</div><?php endif; ?>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
