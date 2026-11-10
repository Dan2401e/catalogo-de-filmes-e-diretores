
<?php

require_once __DIR__ . '/conexao.php';

$busca = trim($_GET['busca'] ?? '');
$genero = trim($_GET['genero'] ?? '');

$sql = "SELECT * FROM filmes WHERE 1=1";

$params = [];

if ($busca !== '') {

    $sql .= " AND (
        titulo LIKE :titulo
        OR diretor LIKE :diretor
    )";

    $params['titulo'] = "%$busca%";
    $params['diretor'] = "%$busca%";
}

if ($genero !== '') {

    $sql .= " AND genero = :genero";

    $params['genero'] = $genero;
}

$sql .= " ORDER BY titulo ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Resultados da pesquisa</title>

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header class="cabecalho">
    <div class="container">
        <h1>🎬 CineBiblioteca</h1>

        <nav>
            <a href="index.php">Início</a>
            <a href="biblioteca.php">Filmes</a>
        </nav>
    </div>
</header>

<main class="container">

    <section class="titulo-pagina">
        <h2>Resultados da pesquisa</h2>

        <p>
            <?= count($filmes) ?> filme(s) encontrado(s).
        </p>
    </section>

    <form action="buscar.php" method="GET" class="filtros">

        <input
            type="text"
            name="busca"
            placeholder="Filme ou diretor..."
            value="<?= htmlspecialchars($busca) ?>"
        >

        <select name="genero">
            <option value="">Todos os gêneros</option>

            <?php
            $generos = ['Ação', 'Terror', 'Drama', 'Comédia', 'Ficção'];

            foreach ($generos as $g):
            ?>

                <option
                    value="<?= htmlspecialchars($g) ?>"
                    <?= $genero === $g ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($g) ?>
                </option>

            <?php endforeach; ?>

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
            Nenhum filme encontrado. Tente outra pesquisa.
        </p>

    <?php endif; ?>

</main>

</body>
</html>
