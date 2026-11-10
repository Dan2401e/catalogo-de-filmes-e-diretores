<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$edicao = null;
if (isset($_GET['editar'])) {
    $id = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $conexao->prepare('SELECT * FROM diretores WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $edicao = $stmt->get_result()->fetch_assoc();
    }
}
$resultado = $conexao->query('SELECT d.*, (SELECT COUNT(*) FROM filmes f WHERE f.diretor_id = d.id) AS total_filmes FROM diretores d ORDER BY d.nome');
$pageTitle = 'Diretores';
require_once dirname(__DIR__) . '/includes/header.php';
?>
<div class="page-heading"><div><p class="eyebrow">GERENCIAMENTO</p><h1>Diretores</h1><p class="muted">Cadastre e organize os responsáveis pelas obras.</p></div></div>
<div class="content-grid">
<section class="panel">
    <h2><?= $edicao ? 'Editar diretor' : 'Novo diretor' ?></h2>
    <form action="<?= e(url('diretores/salvar.php')) ?>" method="post" enctype="multipart/form-data" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)($edicao['id'] ?? 0) ?>">
        <div class="field full"><label>Nome completo *</label><input name="nome" required maxlength="100" value="<?= e($edicao['nome'] ?? '') ?>"></div>
        <div class="field"><label>Nacionalidade</label><input name="nacionalidade" maxlength="60" value="<?= e($edicao['nacionalidade'] ?? '') ?>"></div>
        <div class="field"><label>Data de nascimento</label><input type="date" name="data_nascimento" value="<?= e($edicao['data_nascimento'] ?? '') ?>"></div>
        <div class="field full"><label>Biografia</label><textarea name="biografia" rows="4"><?= e($edicao['biografia'] ?? '') ?></textarea></div>
        <div class="field full"><label>Foto (JPG, PNG ou WEBP · até 3 MB)</label><input type="file" name="foto" accept="image/jpeg,image/png,image/webp"></div>
        <?php if (!empty($edicao['foto'])): ?><div class="field full"><img class="preview-image" src="<?= e(imagem_url($edicao['foto'])) ?>" alt="Foto atual"></div><?php endif; ?>
        <div class="field full form-actions"><button class="btn btn-primary" type="submit"><?= $edicao ? 'Salvar alterações' : 'Cadastrar diretor' ?></button><?php if ($edicao): ?><a class="btn btn-outline" href="<?= e(url('diretores/index.php')) ?>">Cancelar</a><?php endif; ?></div>
    </form>
</section>
<section class="panel">
    <div class="panel-heading"><h2>Diretores cadastrados</h2><span class="counter"><?= $resultado->num_rows ?></span></div>
    <?php if ($resultado->num_rows === 0): ?><p class="empty-state">Nenhum diretor cadastrado ainda.</p><?php else: ?>
    <div class="table-wrap"><table><thead><tr><th>Diretor</th><th>Filmes</th><th>Ações</th></tr></thead><tbody>
    <?php while ($d = $resultado->fetch_assoc()): ?>
    <tr>
        <td><div class="person-cell"><img src="<?= e(imagem_url($d['foto'])) ?>" alt=""><div><strong><?= e($d['nome']) ?></strong><small><?= e($d['nacionalidade'] ?? '') ?></small></div></div></td>
        <td><?= (int)$d['total_filmes'] ?></td>
        <td class="actions"><a href="<?= e(url('diretores/index.php?editar=' . $d['id'])) ?>">Editar</a>
        <form action="<?= e(url('diretores/excluir.php')) ?>" method="post" onsubmit="return confirm('Deseja realmente excluir este diretor?')">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$d['id'] ?>">
            <button class="link-danger" type="submit">Excluir</button>
        </form></td>
    </tr>
    <?php endwhile; ?>
    </tbody></table></div><?php endif; ?>
</section>
</div>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
