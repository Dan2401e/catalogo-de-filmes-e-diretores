<?php
require_once __DIR__ . '/funcoes.php';

class TmdbClient
{
    private const API_BASE = 'https://api.themoviedb.org/3';
    private const IMAGE_BASE = 'https://image.tmdb.org/t/p/';
    private string $token;
    private string $caBundle;

    public function __construct(string $token, string $caBundle = '')
    {
        $this->token = trim($token);
        $this->caBundle = trim($caBundle);
    }

    public function configurado(): bool
    {
        return $this->token !== '';
    }

    public function get(string $endpoint, array $params = []): ?array
    {
        if (!$this->configurado()) return null;
        ksort($params);
        $cacheKey = 'tmdb:' . hash('sha256', $endpoint . '?' . http_build_query($params));
        $stmt = $GLOBALS['conexao']->prepare('SELECT conteudo, expira_em FROM tmdb_cache WHERE cache_key = ? LIMIT 1');
        $stmt->bind_param('s', $cacheKey);
        $stmt->execute();
        $cache = $stmt->get_result()->fetch_assoc();
        if ($cache && strtotime((string) $cache['expira_em']) > time()) {
            $data = json_decode((string) $cache['conteudo'], true);
            return is_array($data) ? $data : null;
        }

        $query = array_merge(['language' => 'pt-BR', 'include_adult' => 'false'], $params);
        $url = self::API_BASE . '/' . ltrim($endpoint, '/') . '?' . http_build_query($query);
        if (!function_exists('curl_init')) return null;
        if ($this->caBundle !== '' && !is_file($this->caBundle)) {
            error_log('[CineCatalog] O arquivo de certificados CA configurado não foi encontrado.');
            return null;
        }
        $ch = curl_init($url);
        $opcoesCurl = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Authorization: Bearer ' . $this->token],
        ];
        $caBundle = $this->caBundle !== '' ? $this->caBundle : (string) ini_get('curl.cainfo');
        if ($caBundle !== '' && is_file($caBundle)) {
            $opcoesCurl[CURLOPT_CAINFO] = $caBundle;
        }
        curl_setopt_array($ch, $opcoesCurl);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        if (!is_string($body) || $status < 200 || $status >= 300) {
            error_log('[CineCatalog] Falha TMDB (HTTP ' . $status . '): ' . $curlError);
            return null;
        }
        $data = json_decode($body, true);
        if (!is_array($data)) return null;

        $expires = date('Y-m-d H:i:s', time() + 6 * 60 * 60);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $stmt = $GLOBALS['conexao']->prepare(
            'INSERT INTO tmdb_cache (cache_key, conteudo, expira_em) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE conteudo = VALUES(conteudo), expira_em = VALUES(expira_em)'
        );
        $stmt->bind_param('sss', $cacheKey, $json, $expires);
        $stmt->execute();
        return $data;
    }

    public static function poster(?string $path, string $size = 'w500'): string
    {
        if (!is_string($path) || !preg_match('~\A/[A-Za-z0-9._-]+\z~', $path)) return url('assets/img/sem-imagem.svg');
        return self::IMAGE_BASE . $size . $path;
    }

    public static function backdrop(?string $path, string $size = 'w1280'): string
    {
        if (!is_string($path) || !preg_match('~\A/[A-Za-z0-9._-]+\z~', $path)) return '';
        return self::IMAGE_BASE . $size . $path;
    }
}
