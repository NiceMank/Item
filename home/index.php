<?php
session_start();

if (isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
} else {
    header("Location: ../connexion.php");
}

$user_id =  $_COOKIE['user_id'];
include('../config/database.php');
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idem | Accueil</title>
    <link rel="shortcut icon" href="../assets/img/WeLogo1.png" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/accueil.css">
</head>
<body class="<?php echo $user_moi['theme'] ?>">
    <?php
        include('../includes/header.php');
    ?>
    <section id="corps">
        <section id="stories">
            <div id="ajout" class="storie ajout">
                <img src="<?php
                    if ($user_moi['profil'] == "") {
                        echo "../assets/../assets/img/profile.png";
                    }else {
                        echo htmlspecialchars($user_moi['profil']);
                    }
                ?>" alt="">
                <span class="add"><i class="fas fa-add"></i><br><br><b>Creer une Nouvelle story</b></span>
                
            </div>
            <?php
                $id = $user_moi['id'];
                $stor = $conn->query("SELECT * FROM storie WHERE id_uti = '$id'");
                while ($sto = $stor->fetch_assoc()) {
                    if ($sto['story_text'] !== "") {
                        ?>
                        <div class="storie">
                            
                            <span class="st">
                            <?php
                                echo $sto['story_text'];
                            ?>
                            </span>
                            <div class="info">
                                <img src="
                                <?php
                                    if ($user_moi['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($user_moi['profil']);
                                    }
                                ?>
                                ">
                                <span class="uti">
                                    Vous
                                </span>
                            </div>
                        </div>
                        <?php
                    }else{
                        ?>
                        <div class="storie">
                            <img src="
                            <?php
                                echo $sto['story_image'];
                            ?>
                            ">
                            <div class="info">
                            <img src="
                                <?php
                                    if ($user_moi['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($user_moi['profil']);
                                    }
                                ?>
                                ">
                                <span class="uti">
                                    Vous
                                </span>
                            </div>
                        </div>
                    <?php
                    }
                }
                // die();
                $storys = $conn->query("SELECT * FROM storie WHERE id_uti != '$id' LIMIT 10");
                while ($story = $storys->fetch_assoc()) {
                    $idd = $story['id_uti'];
                    $app = $conn->query("SELECT * FROM users WHERE id = '$idd'");
                    $utile = $app->fetch_assoc();
                    if ($story['story_text'] !== "") {
                        ?>
                        <div class="storie">
                            
                            <span class="st">
                            <?php
                                echo $story['story_text'];
                            ?>
                            </span>
                            <div class="info">
                                <img src="
                                <?php
                                    if ($utile['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($utile['profil']);
                                    }
                                ?>
                                ">
                                <span class="uti">
                                    <?php
                                        echo $utile['prenom'] ." ". $utile['nom'];
                                    ?>
                                </span>
                            </div>
                        </div>
                    <?php
                        
                    }else{
                    ?>
                        <div class="storie">
                            <img src="
                            <?php
                                echo $story['story_image'];
                            ?>
                            ">
                            <div class="info">
                            <img src="
                                <?php
                                    if ($utile['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($utile['profil']);
                                    }
                                ?>
                                ">
                                <span class="uti">
                                    <?php
                                        echo $utile['prenom'] . " " . $utile['nom'];
                                    ?>
                                </span>
                            </div>
                        </div>
                    <?php
                    }
                }
            ?>
            
            
        </section>
        <section id="publication">
            <div class="new_pub">
                <div class="ajou">
                    <img src="<?php 
                    if ($user_moi['profil'] == "") {
                        echo "../assets/img/profile.png";
                    }else {
                        echo htmlspecialchars($user_moi['profil']);
                    }
                ?>">
                    <span id="neuf">Quoi de neuf <?php echo htmlspecialchars($user_moi['prenom']) ?> ? ...</span>
                </div>
                <div class="imag">
                    <i class="fas fa-image"></i>
                    <span id="ig">Publier une image ...</span>
                </div>
            </div>
            <div class="pubs">
                <?php
                $pubs = $conn->query("SELECT * FROM publications ORDER BY date DESC LIMIT 15");
                while ($pub = $pubs->fetch_assoc()) {
                    $idd = $pub['id_user'];
                    $app = $conn->query("SELECT * FROM users WHERE id = '$idd'");
                    $utile = $app->fetch_assoc();
                    if ($pub['pub_text'] !== "") {
                        ?>
                        <div class="pub">
                            <div class="prop">
                                <img src="
                                <?php 
                                    if ($utile['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($utile['profil']);
                                    }
                                ?>
                                " alt="">
                                <span class="name">
                                    <?php
                                        echo $utile['prenom'] . " " . $utile['nom'];
                                    ?>
                                </span>
                            </div>
                            <span class="publ">
                                <?php
                                    echo $pub['pub_text'];
                                ?>
                            </span>
                            <div class="notes">
                                <?php
                                    $id_pub = $pub['id'];
                                    $likes = $conn->query("SELECT * FROM likes WHERE id_pub = '$id_pub'");
                                    $nbr = $likes->num_rows;
                                    $come = $conn->query("SELECT * FROM commentaires WHERE id_pub = '$id_pub'");
                                    $nbr_com = $come->num_rows;
                                ?>
                                <div class="like <?php 
                                    $user_name = $user_moi['prenom'] . " " . $user_moi['nom'];
                                    $tru = $conn->query("SELECT * FROM likes WHERE id_pub = '$id_pub' and user_name = '$user_name'");
                                    $nbr_tru = $tru->num_rows;
                                    if ($nbr_tru == 1) {
                                        echo 'true';
                                    }
                                ?>"
                                data-id="<?php echo $pub['id'] ?>"
                                data-nom="<?php echo $user_moi['prenom'] . " " . $user_moi['nom'] ?>">
                                    <span class="nbr_like"><?php if ($nbr !== 0) {
                                            echo $nbr;
                                        } ?></span>
                                    <i class="fa-solid fa-heart"></i>
                                    <span class="t">J'adore</span>
                                </div>
                                <div class="comment" 
                                data-id="<?php echo $pub['id'] ?>" 
                                data-nom="<?php echo $utile['prenom'] . " " . $utile['nom'] ?>" 
                                data-profil="<?php echo $utile['profil'] ?>" 
                                data-img="<?php echo $pub['pub_image'] ?>" 
                                data-text="<?php echo $pub['pub_text'] ?>">
                                    <?php 
                                        if ($nbr_com !== 0) {
                                            echo $nbr_com;
                                        }
                                    ?>
                                    <i class="fa-solid fa-comment"></i>
                                    <span>Commenter</span>
                                </div>
                            </div>
                        </div>
                    <?php
                        
                    }else{
                    ?>
                        <div class="pub">
                            <div class="prop">
                                <img src="
                                <?php 
                                    if ($utile['profil'] == "") {
                                        echo "../assets/img/profile.png";
                                    }else {
                                        echo htmlspecialchars($utile['profil']);
                                    }
                                ?>
                                " alt="">
                                <span class="name">
                                    <?php
                                        echo $utile['prenom'] . " " . $utile['nom'];
                                    ?>
                                </span>
                            </div>
                            <span class="publ">
                                <img src="
                                <?php
                                    echo $pub['pub_image'];
                                ?>
                                ">
                            </span>
                            <div class="notes">
                                <?php
                                    $id_pub = $pub['id'];
                                    $likes = $conn->query("SELECT * FROM likes WHERE id_pub = '$id_pub'");
                                    $nbr = $likes->num_rows;
                                    $come = $conn->query("SELECT * FROM commentaires WHERE id_pub = '$id_pub'");
                                    $nbr_com = $come->num_rows;
                                ?>
                                <div class="like <?php 
                                    $user_name = $user_moi['prenom'] . " " . $user_moi['nom'];
                                    $tru = $conn->query("SELECT * FROM likes WHERE id_pub = '$id_pub' and user_name = '$user_name'");
                                    $nbr_tru = $tru->num_rows;
                                    if ($nbr_tru !== 0) {
                                        echo "true";
                                    }
                                ?>"
                                data-id="<?php echo $pub['id'] ?>"
                                data-nom="<?php echo $user_moi['prenom'] . " " . $user_moi['nom'] ?>">
                                    <span class="nbr_like"><?php if ($nbr !== 0) {
                                            echo $nbr;
                                        } ?></span>
                                    <i class="fa-solid fa-heart"></i>
                                    <span class="t">J'adore</span>
                                </div>
                                <div class="comment" 
                                data-id="<?php echo $pub['id'] ?>" 
                                data-nom="<?php echo $utile['prenom'] . " " . $utile['nom'] ?>" 
                                data-profil="<?php echo $utile['profil'] ?>" 
                                data-img="<?php echo $pub['pub_image'] ?>" 
                                data-text="<?php echo $pub['pub_text'] ?>">
                                    <?php 
                                        if ($nbr_com !== 0) {
                                            echo $nbr_com;
                                        }
                                    ?>
                                    <i class="fa-solid fa-comment"></i>
                                    <span>Commenter</span>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                }
            ?>
            <script>
                
            </script>
            </div>
        </section>
        <section id="texte" class="public">
            <div class="publica">
                <i id="close_pub1" class="fa-solid fa-close"></i>
                <div class="entete">
                    Creer une publication
                </div>
                <form action="publication.php" method="post">
                    <textarea name="pub" id="textarea" required placeholder="Ecrivez quelque chose ..."></textarea>
                    <button type="submit">Publier</button>
                </form>
            </div>
        </section>
        <section id="images" class="public">
            <div class="publica">
                <i id="close_pub2" class="fa-solid fa-close"></i>
                <div class="entete">
                    Publier une image
                </div>
                <form action="publication.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="pub_img" id="imge" required accept="image/*">
                    <span class="sel">
                        Choisir une image pour votre Publication <i class="fas fa-image"></i>
                    </span>
                    <img id="preview2" src="">
                    <i id="Icon" class="fa fa-pencil edit-button"></i>
                    <button type="submit" id="publier">Publier</button>
                </form>
            </div>
        </section>
        <section id="comment">
            <div class="commenter">
                <div class="publication">
                    <i id="commm" class="fas fa-close"></i>
                    <div class="prop">
                        <img id="pub_im" src="../img/Snapchat-914527070.jpg" alt="">
                        <span id="pub_nom" class="name">
                            Farel AHIDEDJI
                        </span>
                    </div>
                    <span id="pub_texte" class="publ">
                        
                    </span>
                    <span class="publ">
                        <img id="pub_imge" src="" alt="">
                    </span>
                </div>
                <div class="commentaires">
                    <form id="send_comm" action="" method="post">
                        <img id="pub_im2" src="../img/Snapchat-914527070.jpg" alt="">
                        <input type="text" name="comment" id="comm" required placeholder="Commenter la publication ...">
                        <button class="fas fa-paper-plane"></button>
                    </form>
                    <script>
                        let user = null;
                        let messageInterval = null;
                        let aj = document.getElementById('com_like');
                        document.addEventListener("DOMContentLoaded", function() {
                            document.querySelectorAll('.comment').forEach(function(element) {
                                element.addEventListener('click', function() {
                                    let userId = this.getAttribute('data-id');
                                    let userName = this.getAttribute('data-nom');
                                    let userProfil = this.getAttribute('data-profil');
                                    let pub_image = this.getAttribute('data-img');
                                    let pub_text = this.getAttribute('data-text');
                                    if (userProfil == "") {
                                        userProfil = "assets/img/profile.png";
                                    }
                                    user = userId;
                                    loadMessages(userId);
                                    
                                    
                                    updateDiscussionHeader(userName, userProfil, pub_image, pub_text);
                                });
                            });
                        });
                        function loadMessages(userId) {
                            updateMessages(userId);
                            // Configurer le rafraîchissement automatique
                            if (messageInterval) clearInterval(messageInterval);
                            messageInterval = setInterval(updateMessages, 2000); // 2000 ms = 2 secondes
                        }

                        
                        

                        function updateDiscussionHeader(userName, userProfil, pub_image, pub_text) {
                            document.getElementById('pub_im').src = userProfil;
                            document.getElementById('pub_im2').src = userProfil;
                            document.getElementById('pub_nom').innerText = userName;
                            document.getElementById('pub_imge').src = pub_image;
                            document.getElementById('pub_texte').innerText = pub_text;
                        }
                        function updateMessages() {
                            const xhr = new XMLHttpRequest();
                            xhr.open('GET', `../api/get_comment.php?pub_id=${user}`, true);
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    document.getElementById('comss').innerHTML = xhr.responseText;
                                }
                            };
                            xhr.send();
                        }
                        const env = document.getElementById('send_comm');
                        env.onsubmit = function(event) {
                            event.preventDefault(); // Empêche l'envoi immédiat du formulaire
                            const commentaire = document.getElementById('comm').value;
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', '../api/send_comment.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    document.getElementById('comm').value = "";
                                    updateMessages();
                                }
                            };
                            xhr.send(`PubId=${user}&comment=${encodeURIComponent(commentaire.trim())}`);
                        }
                    </script>
                    <div id="comss" class="coms"></div>
                </div>
            </div>
        </section>
        <section id="stor">
            <div class="sto">
                <i id="close_stor" class="fas fa-close"></i>
                <div id="part_1" class="par part1">
                    <div id="type_text" class="type text">
                        Creer une story avec du texte <i class="fas fa-font"></i>
                    </div>
                    <div id="type_img" class="type img">
                        Creer une story avec une photo <i class="fas fa-image"></i>
                    </div>
                </div>
                <div id="part_2" class="par part2">
                    <form id="story_text" action="story.php" method="post">
                        <textarea name="story" id="text" placeholder="Commencez à ecrire" required></textarea>
                        <button id="submitBtn"><i  class="fas fa-check"></i></button>
                        <div class="loader" id="loader">
                            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="40px" height="40px" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                                <path fill="#000" d="M25.251,6.461c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615V6.461z">
                                    <animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25" to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
                                </path>
                            </svg>
                        </div>
                    </form>
                </div>
                <div id="part_3" class="par part3">
                    <form id="story_img" action="story.php" method="post" enctype="multipart/form-data">
                        <input type="file" name="story_img" id="img" required accept="image/*">
                        <span class="sel">
                            Choisir une image pour votre story <i class="fas fa-image"></i>
                        </span>
                        <img id="preview" src="">
                        <i id="uploadIcon" class="fa fa-pencil edit-button"></i>
                        <button type="submit" id="submit"><i  class="fas fa-check"></i></button>
                    </form>
                </div>
            </div>
        </section>
    </section>
    <footer>
        <ul>
            <li>
                <a href="index.php">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li>
                <a href="../chats/discussion.php">
                    <i class="fas fa-comments"></i>
                </a>
            </li>
            <li>
                <a href="">
                    <i class="fas fa-bell"></i>
                </a>
            </li>
            <li>
                <a href=""> 
                    <i class="fas fa-user"></i>
                </a>
            </li>
        </ul>
    </footer>
    <script>
        const ajout = document.getElementById('ajout');
        const new_story = document.getElementById('stor');
        const close_stor = document.getElementById('close_stor');
        const type_text = document.getElementById('type_text');
        const type_img = document.getElementById('type_img');
        const part_1= document.getElementById('part_1');
        const part_2 = document.getElementById('part_2');
        const comm = document.querySelectorAll('.comment');
        const part_3 = document.getElementById('part_3');
        const likes = document.querySelectorAll('.like');
        ajout.onclick = function () {
            new_story.classList.add('active');
        }
        type_text.onclick = function () {
            part_2.classList.add('active');
            part_1.classList.add('none');
        }
        type_img.onclick = function () {
            part_3.classList.add('active');
            part_1.classList.add('none');
        }
        close_stor.onclick = function () {
            new_story.classList.remove('active');
            part_1.classList.remove('none');
            part_2.classList.remove('active');
            part_3.classList.remove('active');
        }
        document.getElementById('story_text').addEventListener('submit', function(event) {
            event.preventDefault(); // Empêche l'envoi immédiat du formulaire
            const submitBtn = document.getElementById('submitBtn');
            const loader = document.getElementById('loader');
            submitBtn.style.visibility = 'hidden';
            loader.style.display = 'block';
            setTimeout(function() {
                document.getElementById('story_text').submit();
            }, 1500);
        });
        let like = null;
        
        likes.forEach(like => {
            like.onclick = function () {
                let userId = this.getAttribute('data-id');
                let userName = this.getAttribute('data-nom');
                like.classList.toggle('true');
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '../api/like_add.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('GET', `../api/get_like.php?pub_id=${userId}`, true);
                        xhr2.onload = function() {
                            if (xhr2.status === 200) {
                                like.querySelector('.nbr_like').textContent = xhr2.responseText;
                            }
                        };
                        xhr2.send();
                    }
                };
                xhr.send(`PubId=${userId}&name=${userName}`);
            }
        })
        comm.forEach(like => {
            like.onclick = function () {
                document.getElementById('comment').classList.add('active');
            }
        })
        document.getElementById('close_pub1').onclick = function() {
            document.getElementById('texte').classList.remove('active');
        };
        document.getElementById('commm').onclick = function() {
            document.getElementById('comment').classList.remove('active');
        };
        document.getElementById('close_pub2').onclick = function() {
            document.getElementById('images').classList.remove('active');
        };
        document.getElementById('neuf').onclick = function() {
            document.getElementById('texte').classList.add('active');
        };
        document.getElementById('ig').onclick = function() {
            document.getElementById('images').classList.add('active');
        };
        document.getElementById('uploadIcon').onclick = function() {
            document.getElementById('img').click();
        };
        document.getElementById('Icon').onclick = function() {
            document.getElementById('imge').click();
        };
        document.getElementById('imge').onchange = function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview2');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('Icon').style.display = "block";
                    document.getElementById('publier').style.display = "block";
                };
                
                reader.readAsDataURL(file);
            }
        };
        document.getElementById('img').onchange = function(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    document.getElementById('uploadIcon').style.display = "block";
                    document.getElementById('submit').style.display = "block";
                };
                
                reader.readAsDataURL(file);
            }
        };
    </script>
</body>
</html>