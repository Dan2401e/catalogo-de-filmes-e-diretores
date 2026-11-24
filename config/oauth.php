<?php
require_once __DIR__ . '/funcoes.php';

function oauth_settings(): array
{
    static $settings = null;
    if (is_array($settings)) return $settings;

    $localFile = __DIR__ . '/local.php';
    $local = is_file($localFile) ? require $localFile : [];
    $settings = is_array($local) && is_array($local['oauth'] ?? null) ? $local['oauth'] : [];

    foreach (['google', 'github', 'microsoft', 'apple'] as $provider) {
        $settings[$provider] = is_array($settings[$provider] ?? null) ? $settings[$provider] : [];
        foreach (['client_id', 'client_secret', 'tenant', 'team_id', 'key_id', 'private_key_path'] as $key) {
            $environmentName = 'CINECATALOG_OAUTH_' . strtoupper($provider) . '_' . strtoupper($key);
            $environmentValue = getenv($environmentName);
            if (is_string($environmentValue) && $environmentValue !== '') {
                $settings[$provider][$key] = $environmentValue;
            }
        }
    }

    return $settings;
}

function oauth_base_url(): string
{
    $localFile = __DIR__ . '/local.php';
    $local = is_file($localFile) ? require $localFile : [];
    $baseUrl = is_array($local) ? (string) ($local['base_url'] ?? '') : '';
    $environmentValue = getenv('CINECATALOG_BASE_URL');
    if (is_string($environmentValue) && $environmentValue !== '') $baseUrl = $environmentValue;

    $parts = filter_var($baseUrl, FILTER_VALIDATE_URL) ? parse_url($baseUrl) : false;
    if (!is_array($parts)
        || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
        || empty($parts['host'])
        || isset($parts['user'])
        || isset($parts['pass'])
        || isset($parts['query'])
        || isset($parts['fragment'])) {
        return '';
    }

    return rtrim($baseUrl, '/') . '/';
}

function oauth_provider_ready(string $provider): bool
{
    $config = oauth_settings()[$provider] ?? [];
    if (oauth_base_url() === '') return false;
    if ($provider === 'apple') {
        return !empty($config['client_id'])
            && !empty($config['team_id'])
            && !empty($config['key_id'])
            && !empty($config['private_key_path'])
            && str_starts_with(oauth_base_url(), 'https://')
            && is_readable((string) $config['private_key_path']);
    }

    return in_array($provider, ['google', 'github', 'microsoft'], true)
        && !empty($config['client_id'])
        && !empty($config['client_secret']);
}

function oauth_ca_bundle(): string
{
    $localFile = __DIR__ . '/local.php';
    $local = is_file($localFile) ? require $localFile : [];
    $caBundle = is_array($local) ? (string) ($local['ca_bundle'] ?? '') : '';
    $environmentValue = getenv('CINECATALOG_CA_BUNDLE');
    if (is_string($environmentValue) && $environmentValue !== '') $caBundle = $environmentValue;
    if ($caBundle !== '' && !is_file($caBundle)) {
        throw new RuntimeException('O certificado CA configurado não foi encontrado.');
    }
    return $caBundle;
}

function oauth_http_json(string $url, string $method = 'GET', array $headers = [], ?string $body = null): array
{
    if (!function_exists('curl_init')) throw new RuntimeException('A extensão cURL do PHP não está habilitada.');

    $curl = curl_init($url);
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => array_merge(['Accept: application/json'], $headers),
        CURLOPT_USERAGENT => 'CineCatalog OAuth',
    ];
    $caBundle = oauth_ca_bundle();
    if ($caBundle !== '') $options[CURLOPT_CAINFO] = $caBundle;
    if ($body !== null) $options[CURLOPT_POSTFIELDS] = $body;
    curl_setopt_array($curl, $options);
    $response = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);
    curl_close($curl);

    if (!is_string($response) || $status < 200 || $status >= 300) {
        error_log('[CineCatalog] OAuth HTTP ' . $status . ': ' . $error);
        throw new RuntimeException('O provedor de login não respondeu corretamente.');
    }
    $data = json_decode($response, true);
    if (!is_array($data)) throw new RuntimeException('Resposta inválida do provedor de login.');
    return $data;
}

function oauth_oidc_client(string $provider): Jumbojett\OpenIDConnectClient
{
    $config = oauth_settings()[$provider] ?? [];
    if (!oauth_provider_ready($provider)) throw new RuntimeException('Este provedor ainda não está configurado.');
    if (!is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
        throw new RuntimeException('Execute composer install antes de usar login social.');
    }

    $issuer = match ($provider) {
        'google' => 'https://accounts.google.com',
        'microsoft' => 'https://login.microsoftonline.com/' . rawurlencode((string) ($config['tenant'] ?? 'common')) . '/v2.0',
        'apple' => 'https://appleid.apple.com',
        default => throw new RuntimeException('Provedor OpenID Connect inválido.'),
    };

    $clientSecret = (string) ($config['client_secret'] ?? '');
    if ($provider === 'apple') {
        $privateKeyPath = (string) $config['private_key_path'];
        $privateKey = file_get_contents($privateKeyPath);
        if (!is_string($privateKey) || $privateKey === '') {
            throw new RuntimeException('A chave privada do Sign in with Apple não pôde ser lida.');
        }
        $now = time();
        $clientSecret = Firebase\JWT\JWT::encode([
            'iss' => (string) $config['team_id'],
            'iat' => $now,
            'exp' => $now + 86400 * 180,
            'aud' => 'https://appleid.apple.com',
            'sub' => (string) $config['client_id'],
        ], $privateKey, 'ES256', (string) $config['key_id']);
    }

    $client = new Jumbojett\OpenIDConnectClient(
        $issuer,
        (string) $config['client_id'],
        $clientSecret
    );
    $client->setRedirectURL(oauth_base_url() . 'oauth-callback.php?provider=' . rawurlencode($provider));
    $client->addScope($provider === 'apple' ? ['email', 'name'] : ['profile', 'email']);
    $client->setCodeChallengeMethod('S256');

    $caBundle = oauth_ca_bundle();
    if ($caBundle !== '') $client->setCertPath($caBundle);

    if ($provider === 'microsoft') {
        $tenant = (string) ($config['tenant'] ?? 'common');
        $client->setIssuerValidator(static function (string $issuer) use ($tenant): bool {
            if (preg_match('/\A[0-9a-f-]{36}\z/i', $tenant)) {
                return hash_equals(
                    'https://login.microsoftonline.com/' . strtolower($tenant) . '/v2.0',
                    strtolower($issuer)
                );
            }
            return (bool) preg_match(
                '~\Ahttps://login\.microsoftonline\.com/[0-9a-f-]{36}/v2\.0\z~i',
                $issuer
            );
        });
    } elseif ($provider === 'apple') {
        $client->addAuthParam(['response_mode' => 'form_post']);
    }

    return $client;
}

function oauth_github_identity(string $code, string $redirectUri): array
{
    $config = oauth_settings()['github'] ?? [];
    $token = oauth_http_json(
        'https://github.com/login/oauth/access_token',
        'POST',
        ['Content-Type: application/x-www-form-urlencoded'],
        http_build_query([
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'code' => $code,
            'redirect_uri' => $redirectUri,
        ])
    );
    if (!is_string($token['access_token'] ?? null) || $token['access_token'] === '') {
        throw new RuntimeException('O GitHub não retornou um token de acesso.');
    }

    $headers = [
        'Authorization: Bearer ' . $token['access_token'],
        'X-GitHub-Api-Version: 2022-11-28',
    ];
    $profile = oauth_http_json('https://api.github.com/user', 'GET', $headers);
    $emails = oauth_http_json('https://api.github.com/user/emails', 'GET', $headers);
    $email = null;
    foreach ($emails as $entry) {
        if (!empty($entry['primary']) && !empty($entry['verified']) && is_string($entry['email'] ?? null)) {
            $email = $entry['email'];
            break;
        }
    }

    if (!isset($profile['id']) || !is_scalar($profile['id'])) {
        throw new RuntimeException('O GitHub não retornou um identificador de usuário válido.');
    }
    $displayName = !empty($profile['name']) ? (string) $profile['name'] : (string) ($profile['login'] ?? 'Usuário GitHub');
    return [
        'id' => (string) $profile['id'],
        'email' => $email,
        'name' => $displayName,
    ];
}

function oauth_login_identity(string $provider, string $providerUserId, ?string $email, string $displayName): void
{
    global $conexao;
    if (!in_array($provider, ['google', 'github', 'microsoft', 'apple'], true) || $providerUserId === '') {
        throw new RuntimeException('Identidade OAuth inválida.');
    }

    $email = $email && filter_var($email, FILTER_VALIDATE_EMAIL) ? mb_substr($email, 0, 254) : null;
    $displayName = mb_substr(trim($displayName), 0, 120);
    $conexao->begin_transaction();
    try {
        $stmt = $conexao->prepare(
            'SELECT a.user_id, u.usuario FROM oauth_accounts a
             INNER JOIN usuarios u ON u.id = a.user_id
             WHERE a.provider = ? AND a.provider_user_id = ? LIMIT 1 FOR UPDATE'
        );
        $stmt->bind_param('ss', $provider, $providerUserId);
        $stmt->execute();
        $account = $stmt->get_result()->fetch_assoc();

        if ($account) {
            $userId = (int) $account['user_id'];
            $username = (string) $account['usuario'];
            $stmt = $conexao->prepare(
                'UPDATE oauth_accounts SET email = ?, display_name = ?
                 WHERE provider = ? AND provider_user_id = ?'
            );
            $stmt->bind_param('ssss', $email, $displayName, $provider, $providerUserId);
            $stmt->execute();
        } else {
            $username = 'oauth_' . $provider . '_' . bin2hex(random_bytes(10));
            $passwordHash = password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT);
            $stmt = $conexao->prepare('INSERT INTO usuarios (usuario, senha) VALUES (?, ?)');
            $stmt->bind_param('ss', $username, $passwordHash);
            $stmt->execute();
            $userId = (int) $conexao->insert_id;

            $stmt = $conexao->prepare(
                'INSERT INTO oauth_accounts (user_id, provider, provider_user_id, email, display_name)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('issss', $userId, $provider, $providerUserId, $email, $displayName);
            $stmt->execute();
        }
        $conexao->commit();
    } catch (Throwable $e) {
        $conexao->rollback();
        if ($e instanceof mysqli_sql_exception && (int) $e->getCode() === 1062) {
            $stmt = $conexao->prepare(
                'SELECT a.user_id, u.usuario FROM oauth_accounts a
                 INNER JOIN usuarios u ON u.id = a.user_id
                 WHERE a.provider = ? AND a.provider_user_id = ? LIMIT 1'
            );
            $stmt->bind_param('ss', $provider, $providerUserId);
            $stmt->execute();
            $account = $stmt->get_result()->fetch_assoc();
            if ($account) {
                $userId = (int) $account['user_id'];
                $username = (string) $account['usuario'];
            } else {
                throw $e;
            }
        } else {
            throw $e;
        }
    }

    session_regenerate_id(true);
    $_SESSION['usuario_id'] = $userId;
    $_SESSION['usuario_nome'] = $displayName !== '' ? $displayName : $username;
}
