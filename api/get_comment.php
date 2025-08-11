<?php
// Démarrer la session
session_start();

// Configuration de la base de données
include('../config/database.php');

$currentUserId = $_SESSION['user_id'];
$pub_id = $_GET['pub_id'];

// Récupérer les messages entre l'utilisateur actuel et le destinataire
$sql = "SELECT * FROM commentaires WHERE id_pub = ? ORDER BY date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pub_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<span class="title">
    Tous les commentaires
</span>
<?php


if ($result->num_rows < 1) {
    ?>
        <span class="error">
            Aucun commentaire pour l'instant , <br>Soyez le premier à commenter 🫵
        </span>
    <?php
}else{

    while ($row = $result->fetch_assoc()) {
        $sqle = "SELECT * FROM users WHERE id = ?";
        $stm = $conn->prepare($sqle);
        $id_co = $row['id_user'];
        $stm->bind_param("i",$id_co);
        $stm->execute();
        $resulte = $stm->get_result();
        $resul = $resulte->fetch_assoc();
        if ($resul['profil'] == "") {
            $profil = "../assets/img/profile.png";
        }else {
            $profil = $resul['profil'];
        }
        ?>
            <div class="com">
                <img src="<?php echo $profil ?>" alt="">
                <div class="inf">
                    <span class="name">
                        <?php echo $resul['prenom'] . " " . $resul['nom']?>
                    </span>
                    <span class="comm">
                        <?php echo $row['commentaire'] ?>
                    </span>
                </div>
            </div>
        <?php
    }
}

// $_SESSION['statut_autre'] = null;

// Fermeture de la connexion
$stmt->close();
$conn->close();
?>
