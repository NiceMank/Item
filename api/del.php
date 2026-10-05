<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_api_user($conn);

try {
    $conn->begin_transaction();
    $queries = [
        'DELETE l FROM likes l INNER JOIN publications p ON p.id = l.id_pub WHERE p.id_user = ?' => 'i',
        'DELETE c FROM commentaires c INNER JOIN publications p ON p.id = c.id_pub WHERE p.id_user = ?' => 'i',
        'DELETE FROM likes WHERE id_user = ?' => 'i',
        'DELETE FROM commentaires WHERE id_user = ?' => 'i',
        'DELETE FROM publications WHERE id_user = ?' => 'i',
        'DELETE FROM storie WHERE id_uti = ?' => 'i',
        'DELETE FROM messages WHERE id_moi = ? OR id_autre = ?' => 'ii',
        'DELETE FROM discussion WHERE id_moi = ? OR id_autre = ?' => 'ii',
        'DELETE FROM notifications WHERE user_id = ?' => 'i',
        'DELETE FROM users WHERE id = ?' => 'i',
    ];
    foreach ($queries as $sql => $types) {
        $stmt = $conn->prepare($sql);
        if ($types === 'ii') {
            $stmt->bind_param('ii', $userId, $userId);
        } else {
            $stmt->bind_param('i', $userId);
        }
        $stmt->execute();
        $stmt->close();
    }
    $conn->commit();
} catch (Throwable $e) {
    $conn->rollback();
    http_response_code(500);
    exit('Suppression impossible');
}

forget_user();
if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
    header('Content-Type: text/plain; charset=utf-8');
    echo 'ok';
    exit;
}
header('Location: ../connexion.php');
exit;
