<?php
require_once dirname(__DIR__) . '/includes/auth.php';

// Carrega a edição do filme quando o parâmetro editar estiver presente.
$retorno = retorno_biblioteca(is_string($_GET['retorno'] ?? null) ? $_GET['retorno'] : null);
$edicao = null;
if (isset($_GET['editar'])) {
    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);

    if ($id) {
        $stmt = $conexao->prepare('SELECT * FROM filmes WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $edicao = $stmt->get_result()->fetch_assoc();
    }
}

// Lista os diretores para seleção e todos os filmes cadastrados.
$diretores = $conexao->query('SELECT id, nome FROM diretores ORDER BY nome');
$filmes = $conexao->query('SELECT f.*, d.nome AS diretor FROM filmes f INNER JOIN diretores d ON d.id = f.diretor_id ORDER BY f.id DESC');
$pageTitle = 'Filmes';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="page-heading">
    <div>
        <p class="eyebrow">GERENCIAMENTO</p>
        <h1>Filmes</h1>
        <p class="muted">Cadastre títulos e vincule cada filme ao seu diretor.</p>
    </div>
</div>

<div class="content-grid">
    <section class="panel">
        <h2><?= $edicao ? 'Editar filme' : 'Novo filme' ?></h2>

        <?php if ($diretores->num_rows === 0): ?>
            <div class="alert alert-erro">Cadastre pelo menos um diretor antes de cadastrar filmes.</div>
        <?php else: ?>
            <form action="<?= e(url('filmes/salvar.php')) ?>" method="post" enctype="multipart/form-data" class="form-grid">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int) ($edicao['id'] ?? 0) ?>">
                <input type="hidden" name="retorno" value="<?= e($retorno) ?>">

                <div class="field full">
                    <label for="titulo">Título *</label>
                    <input id="titulo" name="titulo" required maxlength="150" value="<?= e($edicao['titulo'] ?? '') ?>">
                </div>

                <div class="field">
                    <label for="diretor_id">Diretor *</label>
                    <select id="diretor_id" name="diretor_id" required>
                        <option value="">Selecione</option>
                        <?php while ($d = $diretores->fetch_assoc()): ?>
                            <option value="<?= (int) $d['id'] ?>" <?= (int) ($edicao['diretor_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>>
                                <?= e($d['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="ano">Ano *</label>
                    <input id="ano" type="number" name="ano" min="1888" max="<?= date('Y') + 5 ?>" required value="<?= e((string) ($edicao['ano'] ?? date('Y'))) ?>">
                </div>

                <div class="field">
                    <label for="genero">Gênero *</label>
                    <select id="genero" name="genero" required>
                        <option value="">Selecione</option>
                        <?php foreach (['Ação', 'Aventura', 'Animação', 'Comédia', 'Drama', 'Fantasia', 'Ficção científica', 'Suspense', 'Terror', 'Romance', 'Documentário', 'Outro'] as $g): ?>
                            <option value="<?= e($g) ?>" <?= ($edicao['genero'] ?? '') === $g ? 'selected' : '' ?>>
                                <?= e($g) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field full">
                    <label for="sinopse">Sinopse</label>
                    <textarea id="sinopse" name="sinopse" rows="4"><?= e($edicao['sinopse'] ?? '') ?></textarea>
                </div>

                <div class="field full">
                    <label for="trailer_link">Link do trailer no YouTube</label>
                    <input id="trailer_link" type="url" name="trailer_link" placeholder="https://www.youtube.com/watch?v=..." value="<?= e($edicao['trailer_link'] ?? '') ?>">
                </div>

                <div class="field full">
                    <label for="capa">Capa (JPG, PNG ou WEBP · até 3 MB)</label>
                    <input id="capa" type="file" name="capa" accept="image/jpeg,image/png,image/webp">
                </div>

                <?php if (!empty($edicao['capa'])): ?>
                    <div class="field full">
                        <img class="preview-image poster-preview" src="<?= e(imagem_url($edicao['capa'])) ?>" alt="Capa atual">
                    </div>
                <?php endif; ?>

                <div class="field full form-actions">
                    <button class="btn btn-primary" type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar filme' ?></button>

                    <?php if ($edicao): ?>
                        <a class="btn btn-outline" href="<?= e(url($retorno)) ?>">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <div>
                <h2>Filmes cadastrados</h2>
                <p class="muted">Capas, informações e ações do catálogo.</p>
            </div>
            <span class="counter"><?= $filmes->num_rows ?></span>
        </div>

        <?php if ($filmes->num_rows === 0): ?>
            <p class="empty-state">Nenhum filme cadastrado ainda.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th scope="col">Filme</th><th scope="col">Diretor</th><th scope="col">Gênero</th><th scope="col">Ano</th><th scope="col">Trailer</th><th scope="col">Ações</th></tr></thead>
                    <tbody>
                        <?php while ($f = $filmes->fetch_assoc()): ?>
                            <tr>
                                <td><div class="person-cell"><img class="table-poster" src="<?= e(imagem_url($f['capa'])) ?>" alt="Capa de <?= e($f['titulo']) ?>"><span><strong><?= e($f['titulo']) ?></strong><small><?= e(mb_strimwidth($f['sinopse'] ?? '', 0, 80, '...')) ?></small></span></div></td>
                                <td><?= e($f['diretor']) ?></td><td><?= e($f['genero']) ?></td><td><?= e((string) $f['ano']) ?></td>
                                <td><?php if ($f['trailer_link']): ?><a class="text-link" href="<?= e($f['trailer_link']) ?>" target="_blank" rel="noopener noreferrer">Assistir ↗</a><?php else: ?>—<?php endif; ?></td>
                                <td class="actions"><a class="btn btn-small btn-outline" href="<?= e(url('filmes/index.php?editar=' . (int) $f['id'])) ?>">Editar</a><form action="<?= e(url('filmes/excluir.php')) ?>" method="post" onsubmit="return confirm('Deseja excluir este filme?')"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int) $f['id'] ?>"><button class="btn btn-small btn-danger" type="submit">Excluir</button></form></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
