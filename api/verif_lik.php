<?php
require_once __DIR__ . '/../config/database.php';

$userId = require_api_user($conn);
$pubId = (int) ($_GET['pub_id'] ?? 0);
header('Content-Type: text/plain; charset=utf-8');
if ($pubId <= 0) {
    exit;
}
$stmt = $conn->prepare('SELECT id FROM likes WHERE id_pub = ? AND id_user = ?');
$stmt->bind_param('ii', $pubId, $userId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
if ($row) {
    echo 'true';
}
