<?php
require_once __DIR__ . '/config/oauth.php';

$provider = is_string($_GET['provider'] ?? null) ? $_GET['provider'] : '';
if (!in_array($provider, ['google', 'github', 'microsoft', 'apple'], true) || !oauth_provider_ready($provider)) {
    mensagem('erro', 'Não foi possível validar esse login social.');
    redirecionar('login.php');
}

try {
    if ($provider === 'github') {
        $state = is_string($_GET['state'] ?? null) ? $_GET['state'] : '';
        $states = $_SESSION['github_oauth_states'] ?? [];
        $createdAt = $states[$state] ?? null;
        unset($_SESSION['github_oauth_states'][$state]);
        if (empty($_SESSION['github_oauth_states'])) unset($_SESSION['github_oauth_states']);
        if (!is_int($createdAt) || $createdAt < time() - 600) {
            throw new RuntimeException('Estado OAuth do GitHub ausente ou expirado.');
        }
        if (isset($_GET['error'])) throw new RuntimeException('O usuário cancelou o login pelo GitHub.');

        $code = is_string($_GET['code'] ?? null) ? $_GET['code'] : '';
        if ($code === '') throw new RuntimeException('Código de autorização do GitHub ausente.');
        $callback = oauth_base_url() . 'oauth-callback.php?provider=github';
        $identity = oauth_github_identity($code, $callback);
    } else {
        require_once __DIR__ . '/vendor/autoload.php';
        $client = oauth_oidc_client($provider);
        if (!$client->authenticate()) throw new RuntimeException('A autenticação OpenID não foi concluída.');
        $claims = $client->getVerifiedClaims();
        if (!is_object($claims) || !is_string($claims->sub ?? null) || $claims->sub === '') {
            throw new RuntimeException('O provedor não retornou uma identidade válida.');
        }

        $displayName = '';
        foreach (['name', 'preferred_username', 'email'] as $claimName) {
            if (is_string($claims->{$claimName} ?? null) && $claims->{$claimName} !== '') {
                $displayName = $claims->{$claimName};
                break;
            }
        }

        if ($provider === 'apple' && is_string($_POST['user'] ?? null)) {
            $appleUser = json_decode($_POST['user'], true);
            if (is_array($appleUser) && is_array($appleUser['name'] ?? null)) {
                $firstName = is_string($appleUser['name']['firstName'] ?? null) ? $appleUser['name']['firstName'] : '';
                $lastName = is_string($appleUser['name']['lastName'] ?? null) ? $appleUser['name']['lastName'] : '';
                $displayName = trim($firstName . ' ' . $lastName) ?: $displayName;
            }
        }

        $identity = [
            'id' => $claims->sub,
            'email' => is_string($claims->email ?? null) ? $claims->email : null,
            'name' => $displayName !== '' ? $displayName : 'Usuário ' . ucfirst($provider),
        ];
    }

    oauth_login_identity($provider, $identity['id'], $identity['email'], $identity['name']);
    redirecionar('index.php');
} catch (Throwable $e) {
    error_log('[CineCatalog] OAuth callback (' . $provider . '): ' . $e->getMessage());
    mensagem('erro', 'Não foi possível concluir o login social. Tente novamente ou entre com usuário e senha.');
    redirecionar('login.php');
}
