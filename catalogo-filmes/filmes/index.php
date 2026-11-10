<?php
require_once dirname(__DIR__) . '/includes/auth.php';

// Carrega a edição do filme quando o parâmetro editar estiver presente.
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

                <div class="field full">
                    <label>Título *</label>
                    <input name="titulo" required maxlength="150" value="<?= e($edicao['titulo'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Diretor *</label>
                    <select name="diretor_id" required>
                        <option value="">Selecione</option>
                        <?php while ($d = $diretores->fetch_assoc()): ?>
                            <option value="<?= (int) $d['id'] ?>" <?= (int) ($edicao['diretor_id'] ?? 0) === (int) $d['id'] ? 'selected' : '' ?>>
                                <?= e($d['nome']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Ano *</label>
                    <input type="number" name="ano" min="1888" max="<?= date('Y') + 5 ?>" required value="<?= e((string) ($edicao['ano'] ?? date('Y'))) ?>">
                </div>

                <div class="field">
                    <label>Gênero *</label>
                    <select name="genero" required>
                        <option value="">Selecione</option>
                        <?php foreach (['Ação', 'Aventura', 'Animação', 'Comédia', 'Drama', 'Fantasia', 'Ficção científica', 'Suspense', 'Terror', 'Romance', 'Documentário', 'Outro'] as $g): ?>
                            <option value="<?= e($g) ?>" <?= ($edicao['genero'] ?? '') === $g ? 'selected' : '' ?>>
                                <?= e($g) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field full">
                    <label>Sinopse</label>
                    <textarea name="sinopse" rows="4"><?= e($edicao['sinopse'] ?? '') ?></textarea>
                </div>

                <div class="field full">
                    <label>Link do trailer no YouTube</label>
                    <input type="url" name="trailer_link" placeholder="https://www.youtube.com/watch?v=..." value="<?= e($edicao['trailer_link'] ?? '') ?>">
                </div>

                <div class="field full">
                    <label>Capa (JPG, PNG ou WEBP · até 3 MB)</label>
                    <input type="file" name="capa" accept="image/jpeg,image/png,image/webp">
                </div>

                <?php if (!empty($edicao['capa'])): ?>
                    <div class="field full">
                        <img class="preview-image poster-preview" src="<?= e(imagem_url($edicao['capa'])) ?>" alt="Capa atual">
                    </div>
                <?php endif; ?>

                <div class="field full form-actions">
                    <button class="btn btn-primary" type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar filme' ?></button>

                    <?php if ($edicao): ?>
                        <a class="btn btn-outline" href="<?= e(url('filmes/index.php')) ?>">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        <?php endif; ?>
    </section>

    <section class="panel">
        <div class="panel-heading">
            <h2>Filmes cadastrados</h2>
            <span class="counter"><?= $filmes->num_rows ?></span>
        </div>

        <?php if ($filmes->num_rows === 0): ?>
            <p class="empty-state">Nenhum filme cadastrado ainda.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Filme</th>
                            <th>Diretor</th>
                            <th>Ano</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($f = $filmes->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="person-cell">
                                        <img src="<?= e(imagem_url($f['capa'])) ?>" alt="">
                                        <div>
                                            <strong><?= e($f['titulo']) ?></strong>
                                            <small><?= e($f['genero']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td><?= e($f['diretor']) ?></td>
                                <td><?= e((string) $f['ano']) ?></td>
                                <td class="actions">
                                    <a href="<?= e(url('filmes/index.php?editar=' . $f['id'])) ?>">Editar</a>

                                    <form action="<?= e(url('filmes/excluir.php')) ?>" method="post" onsubmit="return confirm('Deseja excluir este filme?')">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int) $f['id'] ?>">
                                        <button class="link-danger" type="submit">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
