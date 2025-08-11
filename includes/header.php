<link rel="stylesheet" href="../fontawesome/css/all.min.css">
<script defer src="../assets/js/color-thief.umd.js"></script>

<header>
    <a href="../home/index.php" id="logoW">
        <img src="../assets/img/WeLogo1.png" alt="Logo" id="webIco" class="ico">
        <span>Idem</span>
    </a>
    <div class="pro">
        <form action="" id="search-Ele">
            <label for="search">
                <i class="fa fa-search" aria-label="Rechercher"></i>
                <input type="text" name="search" id="search" class="fa fa-search" placeholder="Rechercher...">
                <button type="reset" class="fa fa-times"></button>
            </label>
        </form>
            <button class="notifi fab fa-facebook-messenger" aria-label="Messagerie"></button>
            <button class="notifi fa fa-bell" aria-label="Notifications"></button>
            <a href="profile.php">
                <span class="ti"><?php
                    if (isset($user_moi['nom']) && isset($user_moi['prenom'])) {
                        echo htmlspecialchars($user_moi['nom'] . " " . $user_moi['prenom']);
                    } else {
                        echo "Utilisateur non défini";
                    }
                    ?></span>
                <img id="pr" src="<?php
                if (isset($user_moi['profil']) && $user_moi['profil'] !== "") {
                    echo htmlspecialchars($user_moi['profil']);
                } else {
                    echo "../assets/img/profile.png";
                }
                ?>" crossorigin="anonymous" alt="Photo de profil">
            </a>
    </div>
</header>
<style>
    header .pro .ti {
        margin-right: -10px;
        padding: 10px 20px;
        border-radius: 10px 2px 2px 10px;
        transition: background 0.5s, color 0.5s;
    }
    body.sombre header .pro .ti {
        /* Ajoutez des styles pour le mode sombre si nécessaire */
    }
</style>
<script>
        document.querySelector(".ti").style.color = "white";
    window.addEventListener("load", () => {
        const img = document.getElementById("pr");
        const nameSpan = document.querySelector(".ti");

        if (!img || !nameSpan) {
            console.error("Élément #pr ou .ti introuvable");
            return;
        }

        function applyColor() {
            const colorThief = new ColorThief();
            try {
                const color = colorThief.getColor(img);
                if (document.body.classList.contains("sombre")) {
                    nameSpan.style.background = `rgba(${color[0]}, ${color[1]}, ${color[2]})`;
                }else {
                nameSpan.style.background = `rgba(${color[0]}, ${color[1]}, ${color[2]},0.5)`;

                }
                nameSpan.style.color = "white";
            } catch (e) {
                console.error("Erreur de détection de couleur :", e);
                nameSpan.style.background = "#f0f0f0"; // Couleur de secours
            }
        }

        if (img.complete) {
            applyColor();
        } else {
            img.addEventListener("load", applyColor);
            img.addEventListener("error", () => {
                console.error("Erreur de chargement de l'image");
                nameSpan.style.background = "#f0f0f0"; // Couleur de secours
            });
        }
    });
</script>