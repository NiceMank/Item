<?php
require_once __DIR__ . '/../config/database.php';

require_api_user($conn);
$pubId = (int) ($_GET['pub_id'] ?? 0);
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
?>
<span class="title">Tous les commentaires</span>
<?php
if ($pubId <= 0) {
    echo '<span class="error">Publication introuvable</span>';
    exit;
}

$stmt = $conn->prepare('SELECT c.commentaire, c.date, u.nom, u.prenom, u.profil
    FROM commentaires c
    LEFT JOIN users u ON u.id = c.id_user
    WHERE c.id_pub = ?
    ORDER BY c.date ASC, c.id ASC');
$stmt->bind_param('i', $pubId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    echo '<span class="error">Aucun commentaire pour l\'instant,<br>soyez le premier à commenter</span>';
    $stmt->close();
    exit;
}

while ($row = $result->fetch_assoc()) {
    $author = trim((string) ($row['prenom'] ?? '') . ' ' . (string) ($row['nom'] ?? ''));
    if ($author === '') {
        $author = 'Utilisateur';
    }
    $profil = profile_src($row['profil'] ?? '');
    ?>
    <div class="com">
        <img src="<?php echo h($profil); ?>" alt="">
        <div class="inf">
            <span class="name"><?php echo h($author); ?></span>
            <span class="comm"><?php echo nl2br(h($row['commentaire'])); ?></span>
        </div>
    </div>
    <?php
}
$stmt->close();
