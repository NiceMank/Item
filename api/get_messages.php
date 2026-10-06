<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/icons.php';

$userId = require_api_user($conn);
$receiverId = (int) ($_GET['receiver_id'] ?? 0);
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
if ($receiverId <= 0) {
    exit;
}

$receiver = fetch_user($conn, $receiverId);
$title = $receiver ? display_name($receiver) : 'Utilisateur';

$sql = 'SELECT * FROM messages
        WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?)
        ORDER BY created_at ASC, id ASC';
$stmt = $conn->prepare($sql);
$stmt->bind_param('iiii', $userId, $receiverId, $receiverId, $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $mine = (int) $row['id_moi'] === $userId;
    $messageClass = $mine ? 'moi' : 'autre';
    $who = $mine ? 'Vous' : $title;
    echo '<div class="mess ' . $messageClass . '" title="' . h($who) . '">';
    echo '<span>' . nl2br(h($row['message'])) . '</span>';
    if ($mine) {
        echo '<span class="ticks">' . icon('check') . icon('check') . '</span>';
    }
    echo '</div>';
}
echo '<div id="desc"></div>';
$stmt->close();
