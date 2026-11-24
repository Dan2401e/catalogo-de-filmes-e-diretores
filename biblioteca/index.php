<?php
require_once dirname(__DIR__) . '/includes/auth.php';

// Recebe os filtros da busca da biblioteca.
$busca = is_string($_GET['busca'] ?? null) ? trim($_GET['busca']) : '';
$diretorId = filter_input(INPUT_GET, 'diretor_id', FILTER_VALIDATE_INT) ?: 0;
$genero = is_string($_GET['genero'] ?? null) ? trim($_GET['genero']) : '';
$ano = filter_input(INPUT_GET, 'ano', FILTER_VALIDATE_INT) ?: 0;

// Monta a query dinâmica com filtros combinados por busca, diretor e gênero.
$sql = 'SELECT f.*, d.nome AS diretor FROM filmes f INNER JOIN diretores d ON d.id = f.diretor_id WHERE 1=1';
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

if ($ano) {
    $sql .= ' AND f.ano = ?';
    $tipos .= 'i';
    $valores[] = $ano;
}

$sql .= ' ORDER BY f.titulo';
$retorno = 'biblioteca/index.php?' . http_build_query(array_filter([
    'busca' => $busca, 'diretor_id' => $diretorId ?: null, 'genero' => $genero, 'ano' => $ano ?: null,
], static fn($valor) => $valor !== null && $valor !== ''));

$stmt = $conexao->prepare($sql);
if ($tipos !== '') {
    $stmt->bind_param($tipos, ...$valores);
}
$stmt->execute();
$filmes = $stmt->get_result();

$diretores = $conexao->query('SELECT id, nome FROM diretores ORDER BY nome');
$generos = $conexao->query('SELECT DISTINCT genero FROM filmes ORDER BY genero');
$anos = $conexao->query('SELECT DISTINCT ano FROM filmes ORDER BY ano DESC');
$pageTitle = 'Biblioteca';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <p class="eyebrow">EXPLORE O CATÁLOGO</p>
        <h1>Biblioteca de filmes</h1>
        <p class="muted">Encontre filmes por título, diretor ou gênero.</p>
    </div>
</div>

<section class="panel filter-panel">
    <form method="get" class="filter-form">
        <div class="field search-field">
            <label for="busca">Busca geral</label>
            <input id="busca" type="search" name="busca" placeholder="Digite título, diretor ou palavra-chave..." value="<?= e($busca) ?>">
        </div>

        <div class="field">
            <label for="filtro-diretor">Diretor</label>
            <select id="filtro-diretor" name="diretor_id">
                <option value="">Todos</option>
                <?php while ($d = $diretores->fetch_assoc()): ?>
                    <option value="<?= (int) $d['id'] ?>" <?= $diretorId === (int) $d['id'] ? 'selected' : '' ?>>
                        <?= e($d['nome']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="field">
            <label for="filtro-genero">Gênero</label>
            <select id="filtro-genero" name="genero">
                <option value="">Todos</option>
                <?php while ($g = $generos->fetch_assoc()): ?>
                    <option value="<?= e($g['genero']) ?>" <?= $genero === $g['genero'] ? 'selected' : '' ?>>
                        <?= e($g['genero']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="field">
            <label for="filtro-ano">Ano</label>
            <select id="filtro-ano" name="ano">
                <option value="">Todos</option>
                <?php while ($a = $anos->fetch_assoc()): ?>
                    <option value="<?= (int) $a['ano'] ?>" <?= $ano === (int) $a['ano'] ? 'selected' : '' ?>>
                        <?= e((string) $a['ano']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="filter-actions">
            <button class="btn btn-primary" type="submit">Filtrar</button>
            <a class="btn btn-outline" href="<?= e(url('biblioteca/index.php')) ?>">Limpar</a>
        </div>
    </form>
</section>

<p class="results-label"><?= $filmes->num_rows ?> resultado(s) encontrado(s)</p>

<?php if ($filmes->num_rows === 0): ?>
    <div class="panel empty-state">Nenhum filme encontrado com esses filtros.</div>
<?php else: ?>
    <div class="table-wrap library-table-wrap">
        <table class="library-table">
            <thead>
                <tr>
                    <th scope="col">Filme</th>
                    <th scope="col">Diretor</th>
                    <th scope="col">Gênero</th>
                    <th scope="col">Ano</th>
                    <th scope="col">Trailer</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($f = $filmes->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div class="person-cell">
                                <img class="table-poster" src="<?= e(imagem_url($f['capa'])) ?>" alt="Capa de <?= e($f['titulo']) ?>">
                                <span><strong><?= e($f['titulo']) ?></strong><small><?= e(mb_strimwidth($f['sinopse'] ?? '', 0, 90, '...')) ?></small></span>
                            </div>
                        </td>
                        <td><?= e($f['diretor']) ?></td>
                        <td><?= e($f['genero']) ?></td>
                        <td><?= e((string) $f['ano']) ?></td>
                        <td>
                            <?php if ($f['trailer_link']): ?>
                                <a class="text-link" href="<?= e($f['trailer_link']) ?>" target="_blank" rel="noopener noreferrer">Assistir ↗</a>
                            <?php else: ?>
                                <span class="muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions table-actions">
                            <a class="btn btn-small btn-outline" href="<?= e(url('filmes/index.php?editar=' . (int) $f['id'] . '&retorno=' . rawurlencode($retorno))) ?>">Editar</a>
                            <form action="<?= e(url('filmes/excluir.php')) ?>" method="post" onsubmit="return confirm('Deseja excluir este filme?')">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                <input type="hidden" name="retorno" value="<?= e($retorno) ?>">
                                <button class="btn btn-small btn-danger" type="submit">Excluir</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
