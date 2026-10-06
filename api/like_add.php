<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);
$pubId = (int) ($_POST['PubId'] ?? 0);
if ($pubId <= 0) {
    http_response_code(400);
    exit('publication');
}

$pubStmt = $conn->prepare('SELECT id, id_user FROM publications WHERE id = ?');
$pubStmt->bind_param('i', $pubId);
$pubStmt->execute();
$publication = $pubStmt->get_result()->fetch_assoc();
$pubStmt->close();
if (!$publication) {
    http_response_code(404);
    exit('publication');
}

$me = fetch_user($conn, $userId);
$name = $me ? display_name($me) : 'Utilisateur';

$check = $conn->prepare('SELECT id FROM likes WHERE id_user = ? AND id_pub = ?');
$check->bind_param('ii', $userId, $pubId);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
$check->close();

if ($existing) {
    $del = $conn->prepare('DELETE FROM likes WHERE id_user = ? AND id_pub = ?');
    $del->bind_param('ii', $userId, $pubId);
    $del->execute();
    $del->close();
    echo 'removed';
    exit;
}

$ins = $conn->prepare('INSERT INTO likes (id_user, id_pub, user_name) VALUES (?, ?, ?)');
$ins->bind_param('iis', $userId, $pubId, $name);
$ins->execute();
$ins->close();

$ownerId = (int) $publication['id_user'];
if ($ownerId !== $userId) {
    notify($conn, $ownerId, $name . ' a aimé votre publication', 'like');
}
echo 'added';
