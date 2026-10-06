<?php require_once __DIR__ . '/icons.php'; ?>
<section id="comment">
    <div class="commenter">
        <button type="button" id="commm" class="sheet-x" aria-label="Fermer"><?php echo icon('close'); ?></button>
        <div class="publication">
            <div class="prop">
                <img id="pub_im" src="../assets/img/profile.png" alt="">
                <span id="pub_nom" class="name"></span>
            </div>
            <span id="pub_texte" class="publ"></span>
            <span class="publ">
                <img id="pub_imge" alt="" style="display:none">
            </span>
        </div>
        <div class="commentaires">
            <div id="comss" class="coms"></div>
            <form id="send_comm" action="../api/send_comment.php" method="post">
                <img id="pub_im2" src="../assets/img/profile.png" alt="">
                <input type="text" name="comment" id="comm" required placeholder="Commentaire" maxlength="1000">
                <button type="submit" aria-label="Envoyer"><?php echo icon('send'); ?></button>
            </form>
        </div>
    </div>
</section>
