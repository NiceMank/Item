<?php
require_once __DIR__ . '/../config/database.php';

require_api_user($conn);
$pubId = (int) ($_GET['pub_id'] ?? 0);
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-store');
if ($pubId <= 0) {
    exit;
}
$stmt = $conn->prepare('SELECT COUNT(*) AS c FROM likes WHERE id_pub = ?');
$stmt->bind_param('i', $pubId);
$stmt->execute();
$count = (int) ($stmt->get_result()->fetch_assoc()['c'] ?? 0);
$stmt->close();
echo $count > 0 ? (string) $count : '';
