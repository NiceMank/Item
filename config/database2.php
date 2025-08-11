<?php

    try { 
        $db = new PDO(
            "mysql:host=localhost;dbname=messagerie",
            "root",
            ""
        );
    } catch (PDOException $e) { 
        echo "Erreur : " . $e->getMessage(); 
    } 
    function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../index.php');
        exit;
    }
}

function getUserData($db, $user_id) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}
function getAllUsers($db) {
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function addNotification($db, $user_id, $message, $type = 'info') {
    $stmt = $db->prepare("INSERT INTO notifications (user_id, message, type, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)");
    return $stmt->execute([$user_id, $message, $type]);
}
?>