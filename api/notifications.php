<?php
require_once __DIR__ . '/../config/database.php';

$userId = require_api_user($conn);
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

$stmt = $conn->prepare('SELECT message, type, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC, id DESC LIMIT 20');
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$mark = $conn->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0');
$mark->bind_param('i', $userId);
$mark->execute();
$mark->close();

if (!$rows) {
    echo '<p class="notif-item">Aucune notification</p>';
    exit;
}
foreach ($rows as $row) {
    $when = '';
    if (!empty($row['created_at'])) {
        $ts = strtotime((string) $row['created_at']);
        if ($ts) {
            $when = date('d/m H:i', $ts);
        }
    }
    echo '<div class="notif-item"><span>' . h($row['message']) . '</span>';
    if ($when !== '') {
        echo '<small>' . h($when) . '</small>';
    }
    echo '</div>';
}
