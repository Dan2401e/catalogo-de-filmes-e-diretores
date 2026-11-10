<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conexao.php';

function e(?string $valor): string {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

function url(string $caminho = ''): string {
    return BASE_URL . ltrim($caminho, '/');
}

function redirecionar(string $caminho): never {
    header('Location: ' . url($caminho));
    exit;
}

function exigir_post(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Método não permitido.');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validar_csrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        exit('Solicitação inválida. Atualize a página e tente novamente.');
    }
}

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

function mensagem(string $tipo, string $texto): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function obter_mensagem(): ?array {
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Recebe uma imagem, valida o MIME real e salva com nome aleatório.
 * Retorna o nome relativo do arquivo ou null quando nenhum arquivo foi enviado.
 */
function salvar_imagem(string $campo, string $pasta): ?string {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $arquivo = $_FILES[$campo];
    if (!is_array($arquivo) || !isset($arquivo['tmp_name'], $arquivo['size'])) {
        throw new RuntimeException('Arquivo inválido.');
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha no envio da imagem.');
    }
    if ($arquivo['size'] > 3 * 1024 * 1024) {
        throw new RuntimeException('A imagem deve ter no máximo 3 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($arquivo['tmp_name']);
    $permitidos = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp'
    ];
    if (!is_string($mime) || !isset($permitidos[$mime])) {
        throw new RuntimeException('Formato inválido. Envie JPG, PNG ou WEBP.');
    }

    $destinoPasta = UPLOAD_DIR . $pasta . '/';
    if (!is_dir($destinoPasta) && !mkdir($destinoPasta, 0775, true)) {
        throw new RuntimeException('Não foi possível preparar a pasta de imagens.');
    }

    $nome = bin2hex(random_bytes(16)) . '.' . $permitidos[$mime];
    if (!move_uploaded_file($arquivo['tmp_name'], $destinoPasta . $nome)) {
        throw new RuntimeException('Não foi possível salvar a imagem.');
    }
    return $pasta . '/' . $nome;
}

function excluir_imagem(?string $caminho): void {
    if (!$caminho) return;
    $base = realpath(UPLOAD_DIR);
    $arquivo = realpath(UPLOAD_DIR . $caminho);
    if ($base && $arquivo && str_starts_with($arquivo, $base . DIRECTORY_SEPARATOR) && is_file($arquivo)) {
        unlink($arquivo);
    }
}

function imagem_url(?string $caminho): string {
    return $caminho ? url('uploads/' . $caminho) : url('assets/img/sem-imagem.svg');
}

function validar_link(?string $link): ?string {
    $link = trim((string)$link);
    if ($link === '') return null;
    if (!filter_var($link, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Informe um link válido para o trailer.');
    }
    $host = strtolower((string)parse_url($link, PHP_URL_HOST));
    if (!in_array($host, ['youtube.com', 'www.youtube.com', 'youtu.be', 'm.youtube.com'], true)) {
        throw new RuntimeException('O trailer deve ser um link do YouTube.');
    }
    return $link;
}
