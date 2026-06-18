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

<a href="#" id="nach-oben-button" aria-label="Nach oben scrollen" title="Nach oben">
    ⬆
</a>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('nach-oben-button');

        // Button ein- und ausblenden basierend auf der Scroll-Position
        window.addEventListener('scroll', function() {
            if (window.scrollY > 200) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        });

        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });
</script>

