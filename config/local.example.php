<?php
// Copie este arquivo para config/local.php e ajuste os dados do seu ambiente.
// O arquivo local.php e as credenciais reais devem permanecer fora do Git.
return [
    // URL base pública exata usada nos callbacks OAuth, incluindo o caminho do projeto.
    'base_url' => 'http://localhost/catalogo-de-filmes-e-diretores/',
    'host' => '127.0.0.1',
    'usuario' => 'root',
    'senha' => '',
    'banco' => 'catalogo_filmes',
    'porta' => 3306,
    // Bearer token Read Access Token do TMDB. Nunca use uma API Key v3 aqui.
    'tmdb_token' => '',
    // Opcional se o Apache/PHP não encontrar certificados CA no sistema.
    'ca_bundle' => '',
    // Credenciais OAuth: configure em config/local.php, nunca no Git.
    'oauth' => [
        'google' => ['client_id' => '', 'client_secret' => ''],
        'github' => ['client_id' => '', 'client_secret' => ''],
        'microsoft' => ['client_id' => '', 'client_secret' => '', 'tenant' => 'common'],
        'apple' => [
            'client_id' => '',
            'team_id' => '',
            'key_id' => '',
            'private_key_path' => '',
        ],
    ],
];
