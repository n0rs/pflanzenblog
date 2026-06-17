<header class="kopfzeile">
    <div class="logo-bereich">
        <?php if ($sicherheitsstufe >= 1): ?>
            <div class="benutzer-status" id="benutzerStatusTrigger">
                 <?php echo inlineIcon('account.svg', ['class' => 'icon stay', 'role' => 'img', 'aria-label' => 'Account', 'title' => $_SESSION['benutzername']]); ?>
                 <p style="margin: 0; display: inline;"><?php echo e($_SESSION['benutzername']); ?></p>

                 <div id="benutzerDropdown">
                    <a href="<?php echo projektPfad('benutzer_loeschen.php?id=' . (int)$_SESSION['benutzer_id']); ?>" id="dropdown-optionen" onclick="return confirm('Konto unwiderruflich löschen?');">Konto löschen</a>
                    <a href="<?php echo projektPfad('logout.php'); ?>" id="dropdown-optionen">Logout</a>
                 </div>
            </div>
         <?php endif; ?>
        <h1>🌿 Pflanzenblog</h1>
        <h2>🌿 Pflanzenblog</h2>
    </div>
    <nav>
        <a href="<?php echo projektPfad('index.php'); ?>">
            <?php echo inlineIcon('house.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Home', 'title' => 'Übersicht']); ?>
            <span class="kopfzeilen-text" title="Übersicht">Übersicht</span>
        </a>

        <a href="<?php echo projektPfad('ueber_uns.php'); ?>">
           <?php echo inlineIcon('question.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Ueber uns', 'title' => 'Über uns']); ?>
           <span class="kopfzeilen-text" title="Ueber uns">Über uns</span>
        </a>

        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="<?php echo projektPfad('beitraege/beitrag_erstellen.php'); ?>">
                <?php echo inlineIcon('new.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Neuer Beitrag', 'title' => 'Neuer Beitrag']); ?>
                <span class="kopfzeilen-text" title="Neuer Beitrag">Neuer Beitrag</span>
            </a>
        <?php endif; ?>

        <?php if ($sicherheitsstufe == 0): ?>
            <a href="<?php echo projektPfad('registrieren.php'); ?>">
                <?php echo inlineIcon('registrieren.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Registrieren', 'title' => 'Registrieren']); ?>
                <span class="kopfzeilen-text" title="Registrieren">Registrieren</span>
            </a>

        <a href="<?php echo projektPfad('login.php'); ?>">
            <?php echo inlineIcon('login.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' =>'Login', 'title' => 'Login']); ?>
            <span class="kopfzeilen-text" title="Login">Login</span>
        </a>
        <?php endif; ?>
        <div class="kopfzeile-suche-box">
            <form action="<?php echo projektPfad('beitraege/suchergebnisse.php'); ?>" method="get" class="kopfzeile-suche-formular">
                 <input type="text" name="suchbegriff" placeholder="Suchen..." id="kopfzeile-suchleiste">
                 <button type="submit" class="kopfzeile-suche-icon-button">
                     <?php echo inlineIcon('search.svg', ['class' => 'icon stay', 'role' => 'img', 'aria-label' => 'Suchen', 'title' => 'Suchen']); ?>
                </button>
            </form>
        </div>
    </nav>
</header>


<script>
    const trigger = document.getElementById('benutzerStatusTrigger');
    const dropdown = document.getElementById('benutzerDropdown');

    if (trigger && dropdown) {
        trigger.addEventListener('click', function(event) {
            event.stopPropagation();

            if (dropdown.style.display === 'none') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        });

        document.addEventListener('click', function() {
            dropdown.style.display = 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Holt genau deine Suchbox anhand der Klasse
        const sucheBox = document.querySelector('.kopfzeile-suche-box');

        // Lauscht auf jeden Klick/Tipp auf der gesamten Webseite
        document.addEventListener('click', function(event) {
            if (sucheBox) {
                // Hat der Nutzer IN die Suchbox (oder auf die Lupe) geklickt?
                if (sucheBox.contains(event.target)) {
                    sucheBox.classList.add('offen'); // Klappt die Suche auf (für Handys)
                }
                // Hat der Nutzer irgendwo AUSSERHALB der Suchbox geklickt?
                else {
                    sucheBox.classList.remove('offen'); // Klappt die Suche wieder zu
                }
            }
        });
    });
</script>
