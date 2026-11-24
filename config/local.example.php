<?php
// Copie este arquivo para config/local.php e ajuste os dados do seu ambiente.
// O arquivo local.php e as credenciais reais devem permanecer fora do Git.
return [
    'host' => '127.0.0.1',
    'usuario' => 'root',
    'senha' => '',
    'banco' => 'catalogo_filmes',
    'porta' => 3306,
    // Bearer token Read Access Token do TMDB. Nunca use uma API Key v3 aqui.
    'tmdb_token' => '',
    // Opcional se o Apache/PHP não encontrar certificados CA no sistema.
    'ca_bundle' => '',
];
