<?php
require_once __DIR__ . '/config/database.php';

$userId = current_user_id($conn);
if ($userId && fetch_user($conn, $userId)) {
    header('Location: home/index.php');
    exit;
}
forget_user();
header('Location: connexion.php');
exit;
