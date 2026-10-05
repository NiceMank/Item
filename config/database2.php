<?php

require_once __DIR__ . '/database.php';

function pdo_db(): PDO
{
    static $db = null;
    if ($db instanceof PDO) {
        return $db;
    }
    $cfg = db_config();
    $dsn = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['name'] . ';charset=utf8mb4';
    $db = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $db;
}

function sanitize($data)
{
    return htmlspecialchars(strip_tags(trim((string) $data)), ENT_QUOTES, 'UTF-8');
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function getUserData(PDO $db, $user_id)
{
    $stmt = $db->prepare('SELECT id, nom, prenom, email, profil, theme, genre, date_nais, travail, Lieu_travail, situation, etat_compte FROM users WHERE id = ?');
    $stmt->execute([(int) $user_id]);
    return $stmt->fetch();
}

function getAllUsers(PDO $db)
{
    $stmt = $db->query('SELECT id, nom, prenom, email, profil, theme, etat_compte FROM users ORDER BY nom, prenom');
    return $stmt->fetchAll();
}

function addNotification(PDO $db, $user_id, $message, $type = 'info')
{
    $stmt = $db->prepare('INSERT INTO notifications (user_id, message, type, created_at) VALUES (?, ?, ?, CURRENT_TIMESTAMP)');
    return $stmt->execute([(int) $user_id, (string) $message, (string) $type]);
}
