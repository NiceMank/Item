<?php

require_once __DIR__ . '/env.php';

if (!extension_loaded('mysqli')) {
    http_response_code(500);
    exit('Extension PHP mysqli manquante.');
}

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($conn) || !($conn instanceof mysqli)) {
    $cfg = db_config();
    try {
        $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);
        $conn->set_charset('utf8mb4');
    } catch (mysqli_sql_exception $e) {
        http_response_code(500);
        $message = 'Connexion à la base impossible. Créez-la avec `php config/setup.php` et renseignez DB_HOST, DB_USER, DB_PASS, DB_NAME.';
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (str_contains($script, '/api/')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => $message], JSON_UNESCAPED_UNICODE);
        } else {
            echo $message;
        }
        exit;
    }
}

date_default_timezone_set('Africa/Porto-Novo');

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function clip_str(string $value, int $max): string
{
    $value = trim($value);
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $max);
    }
    return substr($value, 0, $max);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf']) || !is_string($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    $token = $_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($token) || $token === '' || empty($_SESSION['csrf'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf'], $token);
}

function csrf_check(): void
{
    if (!csrf_ok()) {
        http_response_code(403);
        exit('Requête refusée (jeton de sécurité manquant ou expiré). Rechargez la page.');
    }
}

function theme_class(?string $theme): string
{
    return $theme === 'sombre' ? 'sombre' : 'clair';
}

function profile_src(?string $path): string
{
    $path = trim((string) $path);
    if ($path !== '' && preg_match('#^\.\./upload/[A-Za-z0-9._-]+$#', $path)) {
        return $path;
    }
    return '../assets/img/profile.png';
}

function media_src(?string $path): string
{
    $path = trim((string) $path);
    if ($path !== '' && preg_match('#^\.\./upload/[A-Za-z0-9._-]+$#', $path)) {
        return $path;
    }
    return '';
}

function like_pattern(string $query): string
{
    $query = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query);
    return '%' . $query . '%';
}

function flash_set(string $message): void
{
    $_SESSION['flash'] = $message;
}

function flash_get(): ?string
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $message = (string) $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $message;
}

function redirect_back(string $fallback): void
{
    $target = $_POST['redirect'] ?? '';
    $allowed = [
        '../home/index.php',
        '../chats/discussion.php',
        '../profile/profile.php',
        'index.php',
        'loading.php',
        'connexion.php',
    ];
    if (is_string($target) && in_array($target, $allowed, true)) {
        header('Location: ' . $target);
        exit;
    }
    header('Location: ' . $fallback);
    exit;
}

function current_user_id(mysqli $conn): ?int
{
    if (!empty($_SESSION['user_id'])) {
        return (int) $_SESSION['user_id'];
    }
    $token = $_COOKIE['remember'] ?? '';
    if (!is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
        return null;
    }
    $hash = hash('sha256', $token);
    $stmt = $conn->prepare('SELECT id FROM users WHERE remember_token = ?');
    $stmt->bind_param('s', $hash);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$row) {
        return null;
    }
    $_SESSION['user_id'] = (int) $row['id'];
    return (int) $row['id'];
}

function remember_user(mysqli $conn, int $id): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
    $token = bin2hex(random_bytes(32));
    $hash = hash('sha256', $token);
    $online = '1';
    $stmt = $conn->prepare('UPDATE users SET remember_token = ?, etat_compte = ? WHERE id = ?');
    $stmt->bind_param('ssi', $hash, $online, $id);
    $stmt->execute();
    $stmt->close();
    setcookie('remember', $token, [
        'expires' => time() + 86400 * 7,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    setcookie('user_id', '', [
        'expires' => time() - 3600,
        'path' => '/',
    ]);
}

function forget_user(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'] ?: '/', $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
    setcookie('remember', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    setcookie('user_id', '', [
        'expires' => time() - 3600,
        'path' => '/',
    ]);
}

function require_login(mysqli $conn, string $loginPath = '../connexion.php'): int
{
    $id = current_user_id($conn);
    if (!$id) {
        header('Location: ' . $loginPath);
        exit;
    }
    return $id;
}

function require_api_user(mysqli $conn): int
{
    $id = current_user_id($conn);
    if (!$id) {
        http_response_code(401);
        header('Content-Type: text/plain; charset=utf-8');
        exit('auth');
    }
    return $id;
}

function fetch_user(mysqli $conn, int $id): ?array
{
    $stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $row ?: null;
}

function display_name(array $user): string
{
    $name = trim((string) ($user['prenom'] ?? '') . ' ' . (string) ($user['nom'] ?? ''));
    return $name !== '' ? $name : 'Utilisateur';
}

function notify(mysqli $conn, int $userId, string $message, string $type = 'info'): void
{
    if ($userId <= 0) {
        return;
    }
    $message = clip_str($message, 240);
    $type = clip_str($type, 32);
    if ($message === '') {
        return;
    }
    $stmt = $conn->prepare('INSERT INTO notifications (user_id, message, type) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $userId, $message, $type);
    $stmt->execute();
    $stmt->close();
}

function discussion_exists(mysqli $conn, int $a, int $b): bool
{
    $sql = 'SELECT id FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?) LIMIT 1';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiii', $a, $b, $b, $a);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return (bool) $row;
}

function ensure_discussion(mysqli $conn, int $a, int $b): void
{
    if ($a <= 0 || $b <= 0 || $a === $b || discussion_exists($conn, $a, $b)) {
        return;
    }
    $stmt = $conn->prepare('INSERT INTO discussion (id_moi, id_autre) VALUES (?, ?)');
    $stmt->bind_param('ii', $a, $b);
    $stmt->execute();
    $stmt->close();
}

function save_image_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return null;
    }
    $tmp = $file['tmp_name'] ?? '';
    if (!is_string($tmp) || !is_uploaded_file($tmp)) {
        return null;
    }
    $info = @getimagesize($tmp);
    if ($info === false) {
        return null;
    }
    $allowed = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG => 'png',
        IMAGETYPE_GIF => 'gif',
    ];
    if (defined('IMAGETYPE_WEBP')) {
        $allowed[IMAGETYPE_WEBP] = 'webp';
    }
    $type = $info[2] ?? 0;
    if (!isset($allowed[$type])) {
        return null;
    }
    $dir = dirname(__DIR__) . '/upload';
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return null;
    }
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$type];
    if (!move_uploaded_file($tmp, $dir . '/' . $name)) {
        return null;
    }
    return '../upload/' . $name;
}

function fetch_publications(mysqli $conn, int $viewerId, ?int $authorId = null, int $limit = 30): array
{
    $limit = max(1, min(50, $limit));
    $sql = 'SELECT p.id, p.id_user, p.pub_text, p.pub_image, p.date,
                   u.nom, u.prenom, u.profil,
                   (SELECT COUNT(*) FROM likes l WHERE l.id_pub = p.id) AS like_count,
                   (SELECT COUNT(*) FROM commentaires c WHERE c.id_pub = p.id) AS comment_count,
                   (SELECT COUNT(*) FROM likes l2 WHERE l2.id_pub = p.id AND l2.id_user = ?) AS liked
            FROM publications p
            LEFT JOIN users u ON u.id = p.id_user';
    if ($authorId) {
        $sql .= ' WHERE p.id_user = ? ORDER BY p.date DESC, p.id DESC LIMIT ' . $limit;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $viewerId, $authorId);
    } else {
        $sql .= ' ORDER BY p.date DESC, p.id DESC LIMIT ' . $limit;
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $viewerId);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}
