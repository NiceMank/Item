<section id="comment">
    <div class="commenter">
        <div class="publication">
            <i id="commm" class="fas fa-close"></i>
            <div class="prop">
                <img id="pub_im" src="../assets/img/profile.png" alt="">
                <span id="pub_nom" class="name"></span>
            </div>
            <span id="pub_texte" class="publ"></span>
            <span class="publ">
                <img id="pub_imge" src="" alt="" style="display:none">
            </span>
        </div>
        <div class="commentaires">
            <form id="send_comm" action="../api/send_comment.php" method="post">
                <img id="pub_im2" src="../assets/img/profile.png" alt="">
                <input type="text" name="comment" id="comm" required placeholder="Commenter la publication ..." maxlength="1000">
                <button class="fas fa-paper-plane" type="submit"></button>
            </form>
            <div id="comss" class="coms"></div>
        </div>
    </div>
</section>
