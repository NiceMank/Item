<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);
$pubId = (int) ($_POST['PubId'] ?? 0);
$comment = clip_str((string) ($_POST['comment'] ?? ''), 1000);
if ($pubId <= 0 || $comment === '') {
    http_response_code(400);
    exit('commentaire');
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

$stmt = $conn->prepare('INSERT INTO commentaires (id_user, id_pub, commentaire) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $userId, $pubId, $comment);
$stmt->execute();
$stmt->close();

$me = fetch_user($conn, $userId);
$name = $me ? display_name($me) : 'Quelqu\'un';
$ownerId = (int) $publication['id_user'];
if ($ownerId !== $userId) {
    notify($conn, $ownerId, $name . ' a commenté votre publication', 'comment');
}
echo 'ok';
