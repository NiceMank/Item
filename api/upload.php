<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_login($conn, '../connexion.php');
$path = save_image_upload($_FILES['image'] ?? []);
if ($path === null) {
    flash_set('Image invalide. Formats acceptés : JPG, PNG, GIF, WEBP (5 Mo max).');
    redirect_back('../chats/discussion.php');
}
$stmt = $conn->prepare('UPDATE users SET profil = ? WHERE id = ?');
$stmt->bind_param('si', $path, $userId);
$stmt->execute();
$stmt->close();
redirect_back('../chats/discussion.php');
