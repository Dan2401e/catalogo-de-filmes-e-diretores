<?php

require_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    exit("Filme não encontrado.");
}

$sql = "SELECT * FROM filmes WHERE id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $id]);

$filme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$filme) {
    exit("Filme não encontrado.");
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Filme</title>

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

    <section class="detalhes-filme">

        <div class="capa-detalhes">
            🎬
        </div>

        <div class="info-detalhes">

            <h2>
                <?= htmlspecialchars($filme['titulo']) ?>
            </h2>

            <p>
                <strong>Diretor:</strong>
                <?= htmlspecialchars($filme['diretor']) ?>
            </p>

            <p>
                <strong>Gênero:</strong>
                <?= htmlspecialchars($filme['genero']) ?>
            </p>

            <p>
                <strong>Ano de lançamento:</strong>
                <?= htmlspecialchars($filme['ano']) ?>
            </p>

            <p>
                <strong>Duração:</strong>
                <?= htmlspecialchars($filme['duracao']) ?> minutos
            </p>

            <a href="biblioteca.php" class="botao">
                Voltar à biblioteca
            </a>

        </div>

    </section>

</main>

</body>
</html>