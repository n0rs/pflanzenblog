<?php if (!empty($_SESSION['toast_nachricht'])): ?>

    <div id="toast-notification" class="toast-box">
        <span class="toast-text"><?php echo e($_SESSION['toast_nachricht']); ?></span>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('show');
                }, 100);

                setTimeout(() => {
                    toast.classList.remove('show');
                }, 4000);
            }
        });
    </script>
    <?php
    unset($_SESSION['toast_nachricht']);
endif;
?>


<footer class="site-footer">
    <p><a href="<?php echo projektPfad('impressum.php'); ?>" title="Impressum">Impressum</a></p>
    <p>© 2026 Pflanzenblog - Alle Rechte vorbehalten </p>
</footer>

