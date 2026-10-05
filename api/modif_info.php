<?php
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('method');
}
csrf_check();
$userId = require_login($conn, '../connexion.php');

$lastName = clip_str((string) ($_POST['lastName'] ?? ''), 80);
$firstName = clip_str((string) ($_POST['firstName'] ?? ''), 80);
$genre = clip_str((string) ($_POST['genre'] ?? ''), 40);
$birthdate = trim((string) ($_POST['birthdate'] ?? ''));
$job = clip_str((string) ($_POST['job'] ?? ''), 120);
$workplace = clip_str((string) ($_POST['workplace'] ?? ''), 120);
$relationship = clip_str((string) ($_POST['relationshipStatus'] ?? ''), 80);

if ($lastName === '' || $firstName === '') {
    flash_set('Le nom et le prénom sont obligatoires.');
    redirect_back('../profile/profile.php');
}
if ($birthdate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthdate)) {
    $birthdate = '';
}

$sql = 'UPDATE users SET nom = ?, prenom = ?, genre = ?, date_nais = ?, travail = ?, Lieu_travail = ?, situation = ? WHERE id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssssi', $lastName, $firstName, $genre, $birthdate, $job, $workplace, $relationship, $userId);
$stmt->execute();
$stmt->close();
redirect_back('../chats/discussion.php');
