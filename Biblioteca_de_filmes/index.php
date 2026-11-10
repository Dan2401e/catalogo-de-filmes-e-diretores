<?php
require_once __DIR__ . '/conexao.php';
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

<main>
    <section class="banner">
        <div class="banner-conteudo">
            <h2>Bem-vindo à Biblioteca de Filmes</h2>

            <p>
                Explore nossa coleção de filmes,
                encontre seus favoritos e descubra
                novas histórias.
            </p>

            <a href="biblioteca.php" class="botao">
                Explorar filmes
            </a>
        </div>
    </section>
</main>

<footer>
    <p>© 2026 CineTeca - Todos os direitos reservados.</p>
</footer>

</body>
</html>