<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);
$theme = ($_POST['theme'] ?? '') === 'sombre' ? 'sombre' : 'clair';
$stmt = $conn->prepare('UPDATE users SET theme = ? WHERE id = ?');
$stmt->bind_param('si', $theme, $userId);
$stmt->execute();
$stmt->close();
header('Content-Type: text/plain; charset=utf-8');
echo 'ok';
