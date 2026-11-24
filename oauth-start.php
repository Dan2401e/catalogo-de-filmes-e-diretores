<?php
require_once __DIR__ . '/config/oauth.php';

$provider = is_string($_GET['provider'] ?? null) ? $_GET['provider'] : '';
if (!in_array($provider, ['google', 'github', 'microsoft', 'apple'], true) || !oauth_provider_ready($provider)) {
    mensagem('erro', 'Esse login social ainda não está configurado.');
    redirecionar('login.php');
}

try {
    if ($provider === 'github') {
        $state = bin2hex(random_bytes(32));
        $_SESSION['github_oauth_states'] = array_filter(
            $_SESSION['github_oauth_states'] ?? [],
            static fn ($createdAt): bool => is_int($createdAt) && $createdAt > time() - 600
        );
        $_SESSION['github_oauth_states'][$state] = time();
        $settings = oauth_settings()['github'];
        $callback = oauth_base_url() . 'oauth-callback.php?provider=github';
        $authorizationUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
            'client_id' => $settings['client_id'],
            'redirect_uri' => $callback,
            'scope' => 'read:user user:email',
            'state' => $state,
        ]);
        header('Location: ' . $authorizationUrl);
        exit;
    }

    require_once __DIR__ . '/vendor/autoload.php';
    oauth_oidc_client($provider)->authenticate();
    exit;
} catch (Throwable $e) {
    error_log('[CineCatalog] OAuth start (' . $provider . '): ' . $e->getMessage());
    mensagem('erro', 'Não foi possível iniciar o login. Verifique a configuração e tente novamente.');
    redirecionar('login.php');
}
