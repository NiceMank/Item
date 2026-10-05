<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../connexion.php');
    exit;
}
csrf_check();
$userId = current_user_id($conn);
if ($userId) {
    $offline = '0';
    $stmt = $conn->prepare('UPDATE users SET etat_compte = ?, remember_token = NULL WHERE id = ?');
    $stmt->bind_param('si', $offline, $userId);
    $stmt->execute();
    $stmt->close();
}
forget_user();
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'ok';
    exit;
}
header('Location: ../connexion.php');
exit;
