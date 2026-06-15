<header class="kopfzeile">
    <div class="logo-bereich">
        <h1>🌿 Pflanzenblog</h1>
        <?php if ($sicherheitsstufe >= 1): ?>
            <div class="benutzer-status">
              <img src="icons/account.svg" alt="Account" class="icon stay" title="<?php echo e($_SESSION['benutzername']); ?>">
               <p><?php echo e($_SESSION['benutzername']); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <nav>
        <a href="index.php">
            <img src="icons/house.svg" alt="Home" class="kopfzeilen-icon" title="Übersicht">
            <span class="kopfzeilen-text" title="Übersicht">Übersicht</span>
        </a>
        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="beitrag_erstellen.php">
                <img src="icons/new.svg" alt="Neuer Beitrag" class="kopfzeilen-icon" title="Neuer Beitrag">
                <span class="kopfzeilen-text" title="Neuer Beitrag">Neuer Beitrag</span>
            </a>
        <?php endif; ?>

        <form action="suchergebnisse.php" method="get" class="header-search-form">
            <input type="text" name="suchbegriff" placeholder="Suchen..." id="suchleiste">
            <button type="submit" class="search-icon-button">
                <img src="icons/search.svg" alt="Suchen" class="icon stay">
            </button>
        </form>


        <?php if ($sicherheitsstufe == 0): ?>
            <a href="registrieren.php">
                <img src="icons/registrieren.svg" alt="Registrieren" class="kopfzeilen-icon" title="Registrieren">
                <span class="kopfzeilen-text" title="Registrieren">Registrieren</span>
            </a>
        <?php endif; ?>
        <a href="<?php echo ($sicherheitsstufe >= 1) ? 'logout.php' : 'login.php'; ?>">
            <img src="icons/<?php echo ($sicherheitsstufe >= 1) ? 'logout.svg' : 'login.svg'; ?>"
                 alt="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>"
                 class="kopfzeilen-icon" title="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>">

            <span class="kopfzeilen-text" title="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>">
        <?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>
    </span>
        </a>
        <a href="ueber_uns.php">
            <img src="icons/question.svg" alt="Ueber uns" class="kopfzeilen-icon" title="Über uns">
            <span class="kopfzeilen-text" title="Ueber uns">Über uns</span>
        </a>
    </nav>
</header>