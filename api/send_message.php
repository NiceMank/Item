<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);
$receiverId = (int) ($_POST['receiver_id'] ?? 0);
$message = clip_str((string) ($_POST['message'] ?? ''), 2000);
if ($receiverId <= 0 || $receiverId === $userId || $message === '') {
    http_response_code(400);
    exit('message');
}
$receiver = fetch_user($conn, $receiverId);
if (!$receiver) {
    http_response_code(404);
    exit('destinataire');
}

ensure_discussion($conn, $userId, $receiverId);
$stmt = $conn->prepare('INSERT INTO messages (id_moi, id_autre, message) VALUES (?, ?, ?)');
$stmt->bind_param('iis', $userId, $receiverId, $message);
$stmt->execute();
$stmt->close();

$me = fetch_user($conn, $userId);
$name = $me ? display_name($me) : 'Quelqu\'un';
notify($conn, $receiverId, $name . ' vous a envoyé un message', 'message');
echo 'ok';
