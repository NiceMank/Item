<?php
include('config.php');
session_start();

// Fonction d'affichage du temps
function format_time_ago($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return "À l’instant";
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "Il y a $minutes min";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "Il y a $hours h";
    } elseif ($diff < 172800) {
        return "Hier à " . date("H:i", $timestamp);
    } else {
        return date("d/m/Y à H:i", $timestamp);
    }
}

$amis = $conn

// Récupère l'ID utilisateur depuis le cookie
$user_id = $_COOKIE['user_id'];

// Préparer la requête pour récupérer les autres utilisateurs
$stmt = $conn->prepare("SELECT * FROM users WHERE id != ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>

<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): ?>
        <li class="user" onclick="me('<?php echo $user['id']; ?>',
                                     '<?php echo htmlspecialchars($user['prenom']) . ' ' . htmlspecialchars($user['nom']); ?>',
                                     '<?php echo $user['profil']; ?>',
                                     '<?php echo $user['date_nais']; ?>',
                                     '<?php echo $user['travail']; ?>',
                                     '<?php echo $user['Lieu_travail']; ?>',
                                     '<?php echo $user['genre']; ?>',
                                     '<?php echo $user['situation']; ?>',
                                     '<?php echo $user['etat_compte']; ?>')">

            <?php
            // Requête pour le dernier message
            $app = $conn->prepare("
                SELECT * FROM messages 
                WHERE (id_moi = ? AND id_autre = ?) OR (id_autre = ? AND id_moi = ?) 
                ORDER BY created_at DESC
            ");
            $app->bind_param("iiii", $_SESSION['user_id'], $user['id'], $user['id'], $_SESSION['user_id']);
            $app->execute();
            $appp = $app->get_result();
            $dernier_msg = $appp->fetch_assoc();
            ?>

            <!-- Photo de profil -->
            <img id="pro_prin" src="<?php 
                echo $user['profil'] == "" ? "img/profile.png" : htmlspecialchars($user['profil']); 
            ?>" alt="Profile Picture">

            <!-- Indicateur de statut -->
            <?php
                if ($user['etat_compte'] == '1') {
                    echo "<span class='op vert'></span>";
                } else {
                    echo "<span class='op orange'></span>";
                }
            ?>

            <!-- Nom + Message + Date -->
            <div class="info" style="position: relative;">
                <span id="nom_pri"><?php echo htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']); ?></span>
                
                <span id="der_mess" class="apercu" style="display: flex; flex-direction: column;width:100%;">
                    <?php 
                        if ($dernier_msg) {
                            $prefix = ($dernier_msg["id_moi"] == $_SESSION["user_id"]) ? "Vous : " : "";
                            $message = $prefix . $dernier_msg["message"];
                            echo htmlspecialchars(mb_strimwidth($message, 0, 30, '...'));
                        } else {
                            echo "Aucun message";
                        }
                    ?>
                <?php if ($dernier_msg): ?>
                    <span class="date_msg" style="
                        text-align: right;
                        font-size: 15px;
                        color: gray;
                        position: relative;
                        width: 200px;
                        ">
                        <?php echo format_time_ago($dernier_msg['created_at']); ?>
                    </span>
                <?php endif; ?>
                </span>

            </div>
        </li>
    <?php endforeach; ?>
<?php else: ?>
    <br>
    <p class="no">Aucun utilisateur trouvé.</p>
<?php endif; ?>