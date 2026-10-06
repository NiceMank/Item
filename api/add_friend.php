<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);
$otherId = (int) ($_POST['autre_id'] ?? 0);
header('Content-Type: text/plain; charset=utf-8');
if ($otherId <= 0 || $otherId === $userId || !fetch_user($conn, $otherId)) {
    http_response_code(400);
    echo 'non';
    exit;
}

if (discussion_exists($conn, $userId, $otherId)) {
    $stmt = $conn->prepare('DELETE FROM discussion WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)');
    $stmt->bind_param('iiii', $userId, $otherId, $otherId, $userId);
    $stmt->execute();
    $stmt->close();
    echo 'non';
    exit;
}

ensure_discussion($conn, $userId, $otherId);
$me = fetch_user($conn, $userId);
$name = $me ? display_name($me) : 'Quelqu\'un';
notify($conn, $otherId, $name . ' vous a ajouté à ses discussions', 'friend');
echo 'oui';
