<?php
require_once __DIR__ . '/../config/database.php';

$userId = require_api_user($conn);
$otherId = (int) ($_GET['autre_id'] ?? $_POST['autre_id'] ?? 0);
header('Content-Type: text/plain; charset=utf-8');
if ($otherId <= 0 || !discussion_exists($conn, $userId, $otherId)) {
    echo 'non';
    exit;
}
echo 'oui';
