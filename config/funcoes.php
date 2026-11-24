<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conexao.php';

// Escapa strings para evitar injeção em HTML.
function e(?string $valor): string {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

// Monta URLs internas da aplicação.
function url(string $caminho = ''): string {
    return BASE_URL . ltrim($caminho, '/');
}

// Redireciona para uma página interna do sistema.
function redirecionar(string $caminho): never {
    header('Location: ' . url($caminho));
    exit;
}

// Aceita somente o retorno interno da biblioteca, sem URLs externas.
function retorno_biblioteca(?string $retorno): string {
    if ($retorno && str_starts_with($retorno, 'biblioteca/index.php?') && !str_contains($retorno, "\n") && !str_contains($retorno, "\r")) {
        return $retorno;
    }
    return 'filmes/index.php';
}

// Limita o processamento a requisições HTTP POST.
function exigir_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }
}

// Gera e reutiliza o token CSRF da sessão.
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

// Valida o token CSRF enviado no formulário.
function validar_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

// Valida datas no padrão AAAA-MM-DD.
function validar_data_br(string $valor): string {
    $valor = trim($valor);
    if ($valor === '') {
        return '';
    }

    $data = DateTimeImmutable::createFromFormat('!Y-m-d', $valor);
    if (!$data || $data->format('Y-m-d') !== $valor) {
        throw new RuntimeException('Data inválida. Use o formato AAAA-MM-DD.');
    }

    return $valor;
}

// Armazena uma mensagem de feedback na sessão.
function mensagem(string $tipo, string $texto): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

// Recupera e limpa a mensagem flash da sessão.
function obter_mensagem(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Valida, redimensiona com GD (máximo de 1600 × 1600) e salva a imagem.
 * Mantém proporção e transparência de PNG. Retorna caminho relativo.
 */
function salvar_imagem(string $campo, string $pasta): ?string {
    if (!isset($_FILES[$campo]) || ($_FILES[$campo]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $arquivo = $_FILES[$campo];
    if (!is_array($arquivo) || !isset($arquivo['tmp_name'], $arquivo['size'], $arquivo['error'])) {
        throw new RuntimeException('Arquivo inválido.');
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
        throw new RuntimeException('Falha no envio da imagem.');
    }
    if ((int) $arquivo['size'] <= 0 || (int) $arquivo['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('A imagem deve ter tamanho entre 1 byte e 3 MB.');
    }
    if (!extension_loaded('gd')) {
        throw new RuntimeException('A extensão GD do PHP está desativada. Ative a extensão GD no php.ini e reinicie o Apache.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);
    $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!is_string($mime) || !isset($permitidos[$mime])) {
        throw new RuntimeException('Formato inválido. Envie uma imagem JPG, PNG ou WEBP.');
    }

    $dimensoes = @getimagesize($arquivo['tmp_name']);
    if (!$dimensoes || !isset($dimensoes[0], $dimensoes[1], $dimensoes['mime']) || $dimensoes[0] < 1 || $dimensoes[1] < 1 || $dimensoes['mime'] !== $mime) {
        throw new RuntimeException('O arquivo enviado não contém uma imagem válida.');
    }
    [$larguraOriginal, $alturaOriginal] = $dimensoes;
    if ($larguraOriginal > 8000 || $alturaOriginal > 8000) {
        throw new RuntimeException('A imagem excede as dimensões máximas permitidas de 8000 × 8000 pixels.');
    }

    $criarImagem = match ($mime) {
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/webp' => 'imagecreatefromwebp',
    };
    $salvarImagem = match ($mime) {
        'image/jpeg' => 'imagejpeg',
        'image/png' => 'imagepng',
        'image/webp' => 'imagewebp',
    };
    if (!function_exists($criarImagem) || !function_exists($salvarImagem)) {
        throw new RuntimeException('O GD instalado não oferece suporte ao formato de imagem enviado.');
    }

    $origem = @$criarImagem($arquivo['tmp_name']);
    if (!$origem) {
        throw new RuntimeException('Não foi possível processar a imagem enviada.');
    }

    $limite = 1600;
    $escala = min(1, $limite / max($larguraOriginal, $alturaOriginal));
    $larguraFinal = max(1, (int) round($larguraOriginal * $escala));
    $alturaFinal = max(1, (int) round($alturaOriginal * $escala));
    $destino = imagecreatetruecolor($larguraFinal, $alturaFinal);

    if ($mime === 'image/png' || $mime === 'image/webp') {
        imagealphablending($destino, false);
        imagesavealpha($destino, true);
        $transparente = imagecolorallocatealpha($destino, 0, 0, 0, 127);
        imagefilledrectangle($destino, 0, 0, $larguraFinal, $alturaFinal, $transparente);
    }

    if (!imagecopyresampled($destino, $origem, 0, 0, 0, 0, $larguraFinal, $alturaFinal, $larguraOriginal, $alturaOriginal)) {
        imagedestroy($origem);
        imagedestroy($destino);
        throw new RuntimeException('Não foi possível redimensionar a imagem.');
    }

    $destinoPasta = UPLOAD_DIR . $pasta . '/';
    if (!is_dir($destinoPasta) && !mkdir($destinoPasta, 0775, true) && !is_dir($destinoPasta)) {
        imagedestroy($origem);
        imagedestroy($destino);
        throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    }

    $nome = bin2hex(random_bytes(16)) . '.' . $permitidos[$mime];
    $arquivoFinal = $destinoPasta . $nome;
    $salvou = match ($mime) {
        'image/jpeg' => $salvarImagem($destino, $arquivoFinal, 86),
        'image/png' => $salvarImagem($destino, $arquivoFinal, 8),
        'image/webp' => $salvarImagem($destino, $arquivoFinal, 82),
    };

    imagedestroy($origem);
    imagedestroy($destino);

    if (!$salvou) {
        if (is_file($arquivoFinal)) unlink($arquivoFinal);
        throw new RuntimeException('Não foi possível salvar a imagem processada.');
    }

    return $pasta . '/' . $nome;
}

// Remove uma imagem antiga quando o cadastro é atualizado ou excluído.
function excluir_imagem(?string $caminho): void {
    if (!$caminho) {
        return;
    }

    $base = realpath(UPLOAD_DIR);
    $arquivo = realpath(UPLOAD_DIR . $caminho);

    if ($base && $arquivo && str_starts_with($arquivo, $base . DIRECTORY_SEPARATOR) && is_file($arquivo)) {
        unlink($arquivo);
    }
}

// Retorna a URL pública da imagem ou o placeholder padrão.
function imagem_url(?string $caminho): string {
    return $caminho ? url('uploads/' . $caminho) : url('assets/img/sem-imagem.svg');
}

// Cria o cliente TMDB usando somente configuração local ou variável de ambiente.
function tmdb_client(): TmdbClient {
    static $cliente = null;
    if ($cliente instanceof TmdbClient) return $cliente;

    $arquivoLocal = __DIR__ . '/local.php';
    $local = is_file($arquivoLocal) ? require $arquivoLocal : [];
    if (!is_array($local)) $local = [];
    $token = getenv('CINECATALOG_TMDB_TOKEN');
    if (!is_string($token) || trim($token) === '') {
        $token = (string) ($local['tmdb_token'] ?? '');
    }
    require_once __DIR__ . '/tmdb.php';
    $caBundle = getenv('CINECATALOG_CA_BUNDLE');
    if (!is_string($caBundle) || trim($caBundle) === '') {
        $caBundle = (string) ($local['ca_bundle'] ?? '');
    }
    $cliente = new TmdbClient((string) $token, (string) $caBundle);
    return $cliente;
}

// Valida se o trailer é um link do YouTube.
function validar_link(?string $link): ?string {
    $link = trim((string) $link);
    if ($link === '') {
        return null;
    }

    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Informe um link válido para o trailer.');
    }

    $host = strtolower((string) parse_url($link, PHP_URL_HOST));
    $caminho = (string) parse_url($link, PHP_URL_PATH);
    $idVideo = '';

    if ($host === 'youtu.be') {
        $idVideo = trim($caminho, '/');
    } elseif (in_array($host, ['youtube.com', 'www.youtube.com', 'm.youtube.com'], true)) {
        parse_str((string) parse_url($link, PHP_URL_QUERY), $consulta);
        $idVideo = (string) ($consulta['v'] ?? '');
        if ($idVideo === '' && preg_match('~^/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})/?$~', $caminho, $partes)) {
            $idVideo = $partes[1];
        }
    }

    if (!preg_match('/\A[A-Za-z0-9_-]{11}\z/', $idVideo)) {
        throw new RuntimeException('Informe um link válido de vídeo do YouTube.');
    }

    return $link;
}
