
<?php
require_once __DIR__ . '/conexao.php';

$sql = "SELECT * FROM filmes ORDER BY titulo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Biblioteca de Filmes</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="cabecalho">
    <div class="container">
        <h1>🎬 CineTeca</h1>

        <nav>
            <a href="index.php">Início</a>
            <a href="biblioteca.php">Filmes</a>
        </nav>
    </div>
</header>

<main class="container">

    <section class="titulo-pagina">
        <h2>Biblioteca de Filmes</h2>
        <p>Encontre seu próximo filme favorito.</p>
    </section>

    <form action="buscar.php" method="GET" class="filtros">

        <input
            type="text"
            name="busca"
            placeholder="Pesquisar por filme ou diretor..."
        >

        <select name="genero">
            <option value="">Todos os gêneros</option>
            <option value="Ação">Ação</option>
            <option value="Terror">Terror</option>
            <option value="Drama">Drama</option>
            <option value="Comédia">Comédia</option>
            <option value="Ficção">Ficção</option>
        </select>

        <button type="submit" class="botao">
            Pesquisar
        </button>

        <a href="biblioteca.php" class="botao-secundario">
            Limpar
        </a>

    </form>

    <section class="grade-filmes">

        <?php foreach ($filmes as $filme): ?>

            <article class="card-filme">

                <div class="capa-filme">

                    <?php if (!empty($filme['imagem'])): ?>

                        <img
                            src="<?= htmlspecialchars($filme['imagem']) ?>"
                            alt="<?= htmlspecialchars($filme['titulo']) ?>"
                        >

                    <?php else: ?>

                        <span>🎬</span>

                    <?php endif; ?>

                </div>

                <div class="info-filme">

                    <h3>
                        <?= htmlspecialchars($filme['titulo']) ?>
                    </h3>

                    <p>
                        Diretor:
                        <?= htmlspecialchars($filme['diretor']) ?>
                    </p>

                    <p>
                        Gênero:
                        <?= htmlspecialchars($filme['genero']) ?>
                    </p>

                    <p>
                        Ano:
                        <?= htmlspecialchars($filme['ano']) ?>
                    </p>

                    <a
                        href="detalhes.php?id=<?= (int)$filme['id'] ?>"
                        class="botao"
                    >
                        Ver detalhes
                    </a>

                </div>

            </article>

        <?php endforeach; ?>

    </section>

    <?php if (empty($filmes)): ?>

        <p class="mensagem-vazia">
            Nenhum filme cadastrado.
        </p>

    <?php endif; ?>

</main>

<footer>
    <p>© 2026 CineBiblioteca</p>
</footer>

</body>
</html>
