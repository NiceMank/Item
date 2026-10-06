<?php
require_once __DIR__ . '/../config/database.php';

$userId = require_login($conn, '../connexion.php');
$user_moi = fetch_user($conn, $userId);
if (!$user_moi) {
    forget_user();
    header('Location: ../connexion.php');
    exit;
}

$q = clip_str((string) ($_GET['q'] ?? $_GET['search'] ?? ''), 80);
$users = [];
$posts = [];
if ($q !== '') {
    $pattern = like_pattern($q);
    $userStmt = $conn->prepare('SELECT id, nom, prenom, profil FROM users WHERE nom LIKE ? ESCAPE \'\\\\\' OR prenom LIKE ? ESCAPE \'\\\\\' OR email LIKE ? ESCAPE \'\\\\\' ORDER BY nom, prenom LIMIT 20');
    $userStmt->bind_param('sss', $pattern, $pattern, $pattern);
    $userStmt->execute();
    $userRows = $userStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $userStmt->close();
    foreach ($userRows as $row) {
        $users[] = [
            'id' => (int) $row['id'],
            'name' => display_name($row),
            'profil' => profile_src($row['profil'] ?? ''),
        ];
    }

    $postStmt = $conn->prepare('SELECT p.id, p.pub_text, u.nom, u.prenom FROM publications p LEFT JOIN users u ON u.id = p.id_user WHERE p.pub_text LIKE ? ESCAPE \'\\\\\' ORDER BY p.date DESC LIMIT 10');
    $postStmt->bind_param('s', $pattern);
    $postStmt->execute();
    $postRows = $postStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $postStmt->close();
    foreach ($postRows as $row) {
        $text = trim((string) ($row['pub_text'] ?? ''));
        if ($text === '') {
            continue;
        }
        $author = trim((string) ($row['prenom'] ?? '') . ' ' . (string) ($row['nom'] ?? ''));
        $posts[] = [
            'id' => (int) $row['id'],
            'text' => function_exists('mb_strimwidth') ? mb_strimwidth($text, 0, 80, '...') : substr($text, 0, 80),
            'author' => $author !== '' ? $author : 'Utilisateur',
        ];
    }
}

$wantsJson = (($_GET['format'] ?? '') === 'json') || str_contains((string) ($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json');
if ($wantsJson) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(['users' => $users, 'posts' => $posts], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche</title>
    <link rel="stylesheet" href="../assets/css/accueil.css">
</head>
<body class="<?php echo h(theme_class($user_moi['theme'] ?? '')); ?>" data-profil="<?php echo h(profile_src($user_moi['profil'] ?? '')); ?>">
<?php include __DIR__ . '/../includes/header.php'; ?>
<section id="corps">
    <h2>Recherche <?php echo $q !== '' ? '« ' . h($q) . ' »' : ''; ?></h2>
    <?php if ($q === ''): ?>
        <p>Saisissez un nom, un prénom ou un extrait de publication.</p>
    <?php elseif (!$users && !$posts): ?>
        <p>Aucun résultat.</p>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <p><a href="../profile/profile.php?id=<?php echo (int) $user['id']; ?>"><?php echo h($user['name']); ?></a></p>
        <?php endforeach; ?>
        <?php foreach ($posts as $post): ?>
            <p><a href="../home/index.php#pub-<?php echo (int) $post['id']; ?>"><?php echo h($post['author'] . ' : ' . $post['text']); ?></a></p>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
</body>
</html>
