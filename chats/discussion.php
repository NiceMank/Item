<?php
session_start();

if (isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
} else {
    header("Location: ../connexion.php");
}
$user_id =  $_COOKIE['user_id'];
include('config.php');
// Préparer la requête pour récupérer les utilisateurs sauf l'utilisateur connecté

$aff = $conn->prepare("SELECT * FROM users WHERE id = ?");
$aff->bind_param("i", $user_id);
$aff->execute();
$result = $aff->get_result();

$user_moi = $result->fetch_assoc();
$isChecked = ($user_moi['theme'] === 'sombre') ? 'checked' : '';
if ($user_moi['id'] == "") {
    header('Location: ../connexion.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FaroChat | Messagerie</title>
    <link rel="stylesheet" href="../assets/css/users.css">
    <link rel="shortcut icon" href="../assets/img/img.png" type="image/x-icon">
</head>

<body class="<?php echo $user_moi['theme'] ?>">
    <?php
        include('../includes/header.php');
    ?>

    <section id="farochat">
        <section id="ut" class="utilisateurs">
            <i class="fas fa-cog para" id="openModal"><img src="<?php 
                if ($user_moi['profil'] == "") {
                    echo "../assets/img/profile.png"; 
                }else {
                    echo htmlspecialchars($user_moi['profil']);
                }
            ?>"></i>
            <h1>Discussion <i class="fas fa-sync"></i></h1>
            <i id="ajoute" class="fas fa-edit"></i>
            <!-- <form class="cherche" action="" method="post">
                <input type="search" name="recherche" id="searchBar" placeholder="Rechercher..." onkeyup="searchUsers()">
            </form> -->
            
            <div class="liste" id="list_user">
                <ul id="aff">
                    
                </ul>
                
            </div>

        </section>
        <section id="disc" class="discussion">
            <div class="entete">
                <i id="retour1" class="fas fa-chevron-left"></i>
                <img id="profilePic" src="" alt="Profile Picture">
                <div class="info">
                    <span id="username"></span>
                    <span  id="con1" class="apercu">Connecté</span>
                </div>
                <i id="in" class="fas fa-ellipsis-v"></i>
            </div>
            <div class="messages" id="messages">
                <div class="inform">
                    <img id="pro2" class="inf-img" src="">
                    <span id="nom2" class="inf-nom"></span>
                    <span class="secu">Commencez à écrire !!</span>
                    
                </div>
                <hr>
                <div id="messa">
                    
                    </div>
                </div> 
                <a href="#desc" class="fas fa-arrow-down" id="desc_but"></a>
            <form class="envoie" action="" id="me_send" method="post">
                <textarea name="message" id="messageInput" required placeholder="Écrivez un message ..."></textarea>
                <button type="submit" class="fa-solid fa-paper-plane"></button>
            </form>
        </section>
        <section class="infor">
            <div class="part1">
            <i id="retour2" class="fas fa-chevron-left"></i>
                <img id="pro" class="inf-img" src="">
                <span id="nom" class="inf-nom"></span>
                <span id="con2" class="apercu"></span>
                <i class="fa-so"></i>
            </div>
            <div class="part2">
                <span id="date"><b>Date de Naissance :</b> 12/06/2003</span>
                <span id="occup"><b>Occupation :</b> Programmation</span>
                <span id="lieu"><b>Lieu de Travail :</b> Maison </span>
                <span id="genre"><b>Genre :</b> Homme</span>
                <span id="situ"><b>Situation Amoureuse :</b> En couple</span>
            </div>
            <div class="part3">
                <button class="ferm">Fermer la discusion</button>
            </div>
        </section>
    <span class="aucun"><b>Selectionnez un contact</b></span>
    

        
        
    </section>
    <div id="settingsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Paramètres</h2>
            <form id="profilePicForm" action="../api/upload.php" method="POST" enctype="multipart/form-data">
                <!-- Modifier photo de profil -->
                <div class="profile-photo-container">
                    <!-- <img id="imagePreview" src="<?php 
                    if ($user_moi['profil'] == "") {
                        echo "../assets/img/profile.png";
                    }else {
                        echo htmlspecialchars($user_moi['profil']);
                    }
                    ?>"> -->
                    <br>
                    <img id="imagePreview" src="<?php 
                    if ($user_moi['profil'] == "") {
                        echo "../assets/img/profile.png";
                    }else {
                        echo htmlspecialchars($user_moi['profil']);
                    }
                    ?>" alt="Aperçu de l'image">
                    <i id="uploadIcon" class="fa fa-pencil edit-button"></i>
                    <button type="submit" class="fas  fa-circle-check edit-button deux" id="send"></button>
                    <input name="image" type="file" id="imageInput" accept="image/*" style="display:none">
                </div>
                <br>
                <span class="nom">
                    <?php
                        echo htmlspecialchars($user_moi['prenom'])." ".htmlspecialchars($user_moi['nom']);
                    ?>
                </span>
                <br><br>
                <button type="button" id="editPersonalInfoBtn">Modifier informations personnelles</button>
                <br><br>
    
                <!-- Changer le thème -->
                <label class="switch">
                    <input type="checkbox" id="themeSwitch" <?php echo $isChecked; ?> onclick="toggleTheme()">
                    <span class="slider round"></span>
                </label>
                <br><br>
                <!-- Se déconnecter -->
                <button type="button" id="logoutBtn">Se déconnecter</button>
                <br><br>
    
                <!-- Supprimer le compte -->
                <button type="button" id="deleteAccountBtn" class="danger">Supprimer le compte</button>
                <br><br>
    
                </form>
        </div>
    </div>
    <div id="personalInfoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Modifier informations personnelles</h2>
            <form id="personalInfoForm" method="post" action="modif_info.php">
                <label for="lastName">Nom :(Obligatoire)</label>
                <input type="text" id="lastName" name="lastName" required placeholder="Votre nom" value="<?php echo $user_moi['nom'] ?>">
                
                <label for="firstName">Prénom :(Obligatoire)</label>
                <input type="text" id="firstName" name="firstName" required placeholder="Votre prénom"  value="<?php echo $user_moi['prenom'] ?>">

                <label for="genre">Sexe :</label>
                <input type="text" id="birthdate" name="genre" placeholder="Votre sexe" value="<?php echo $user_moi['genre'] ?>">

                <label for="birthdate">Date de naissance :</label>
                <input type="date" id="birthdate" name="birthdate" placeholder="Date de naissance" value="<?php echo $user_moi['date_nais'] ?>">

                <label for="job">Occupation :</label>
                <input type="text" id="job" name="job" placeholder="Occupation" value="<?php echo $user_moi['travail'] ?>">

                <label for="workplace">Lieu de travail :</label>
                <input type="text" id="workplace" name="workplace" placeholder="Lieu de Travail" value="<?php echo $user_moi['Lieu_travail'] ?>">

                <label for="relationshipStatus">Situation amoureuse :</label>
                <input type="text" id="relationshipStatus" name="relationshipStatus" placeholder="Celibataire ou non" value="<?php echo $user_moi['situation'] ?>">
                <button type="submit">Enregistrer</button>
            </form>
        </div>
    </div>

    <?php
        $user_id =  $_COOKIE['user_id'];
        // Préparer la requête pour récupérer les utilisateurs sauf l'utilisateur connecté
        $stmt = $conn->prepare("SELECT * FROM users WHERE id != ? ORDER BY nom ASC");
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
    <section id="new_friend">
            <div class="new">
                <div class="tittle">
                    <span>Nouvelle discussion</span>
                </div>
                <i id="clo_new" class="fas fa-close"></i>
                <form id="rech_new" action="">
                    <input type="search" name="new_uti" id="new_uti" placeholder="Rechercher un(e) ami(e)">
                    <button type="submit" class="fas fa-paper-plane"></button>
                </form>
                <div class="resu">
                    <!-- <span class="err">
                        Aucun utilisateur trouvé
                    </span> -->

                    <?php
                    if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <div class="user" >
                            <?php
                                $sql = "SELECT * FROM messages WHERE (id_moi = ? AND id_autre = ?) OR (id_moi = ? AND id_autre = ?) ORDER BY created_at DESC";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("iiii", $_SESSION['user_id'], $user['id'], $user['id'], $_SESSION['user_id']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $re = $result->fetch_assoc();
                            ?>
                                <img id="pro_prin" src="<?php 
                                    if ($user['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    } else {
                                        echo htmlspecialchars($user['profil']);
                                    }
                                    ?>" alt="Profile Picture">
                                <span><?php 
                                echo $user['nom'] . " " . $user['prenom'];
                                ?></span>
                                <span class="aj av" data-id="<?php echo $user['id'] ?>">+</span>
                                
                                </div>
                            
                        <?php endforeach; ?>
                <?php else: ?>
                    <br>
                    <p class="no">Aucun utilisateur trouvé.</p>
                <?php endif; ?>
<!--                 
                    <div class="user">
                        <img src="upload/billy3.jpeg" alt="">
                        <span>Farel Kant</span>
                    </div>
                    <div class="user">
                        <img src="upload/f863ca10580b56276b7b2e319f717c29.jpg" alt="">
                        <span>Osj Trouble</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/FB_IMG_16528329555314158.jpg" alt="">
                        <span>Samuel</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/billy3.jpeg" alt="">
                        <span>Farel Kant</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/f863ca10580b56276b7b2e319f717c29.jpg" alt="">
                        <span>Osj Trouble</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/FB_IMG_16528329555314158.jpg" alt="">
                        <span>Samuel</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/billy3.jpeg" alt="">
                        <span>Farel Kant</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/f863ca10580b56276b7b2e319f717c29.jpg" alt="">
                        <span>Osj Trouble</span>
                        <span class="aj av">+</span>
                    </div>
                    <div class="user">
                        <img src="upload/FB_IMG_16528329555314158.jpg" alt="">
                        <span>Samuel</span>
                        <span class="aj av"><span class="ico">+</span></span>
                    </div> -->
                </div>
            </div>
    </section>
    <footer>
        <ul>
            <li>
                <a href="../home/index.php">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li>
                <a href="discussion.php">
                    <i class="fas fa-comments"></i>
                </a>
            </li>
            <li>
                <a href="">
                    <i class="fas fa-bell"></i>
                </a>
            </li>
            
        </ul>
        <li class="pro">
            <a href="../profile/profile.php">
                <img src="<?php 
                if ($user_moi['profil'] == "") {
                    echo "../assets/img/profile.png"; 
                }else {
                    echo htmlspecialchars($user_moi['profil']);
                }
            ?>">
            </a>
            <br>
        </li>
    </footer>
    <script src="../assets/js/discussion.js"></script>
</body>

</html>