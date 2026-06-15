<header class="kopfzeile">
    <div class="logo-bereich">
        <h1>🌿 Pflanzenblog</h1>
        <?php if ($sicherheitsstufe >= 1): ?>
            <div class="benutzer-status">
                <?php echo inlineIcon('account.svg', ['class' => 'icon stay', 'role' => 'img', 'aria-label' => 'Account', 'title' => $_SESSION['benutzername']]); ?>
                <p><?php echo e($_SESSION['benutzername']); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <nav>
        <a href="index.php">
            <?php echo inlineIcon('house.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Home', 'title' => 'Übersicht']); ?>
            <span class="kopfzeilen-text" title="Übersicht">Übersicht</span>
        </a>

        <a href="ueber_uns.php">
           <?php echo inlineIcon('question.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Ueber uns', 'title' => 'Über uns']); ?>
           <span class="kopfzeilen-text" title="Ueber uns">Über uns</span>
        </a>

        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="beitrag_erstellen.php">
                <?php echo inlineIcon('new.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Neuer Beitrag', 'title' => 'Neuer Beitrag']); ?>
                <span class="kopfzeilen-text" title="Neuer Beitrag">Neuer Beitrag</span>
            </a>
        <?php endif; ?>

        <?php if ($sicherheitsstufe == 0): ?>
            <a href="registrieren.php">
                <?php echo inlineIcon('registrieren.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => 'Registrieren', 'title' => 'Registrieren']); ?>
                <span class="kopfzeilen-text" title="Registrieren">Registrieren</span>
            </a>
        <?php endif; ?>

        <a href="<?php echo ($sicherheitsstufe >= 1) ? 'logout.php' : 'login.php'; ?>">
            <?php echo inlineIcon(($sicherheitsstufe >= 1) ? 'logout.svg' : 'login.svg', ['class' => 'kopfzeilen-icon', 'role' => 'img', 'aria-label' => ($sicherheitsstufe >= 1) ? 'Logout' : 'Login', 'title' => ($sicherheitsstufe >= 1) ? 'Logout' : 'Login']); ?>
            <span class="kopfzeilen-text" title="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>">
                <?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>
            </span>
        </a>

        <form action="suchergebnisse.php" method="get" class="header-search-form">
             <input type="text" name="suchbegriff" placeholder="Suchen..." id="suchleiste">
             <button type="submit" class="search-icon-button">
                 <?php echo inlineIcon('search.svg', ['class' => 'icon stay', 'role' => 'img', 'aria-label' => 'Suchen', 'title' => 'Suchen']); ?>
            </button>
        </form>
    </nav>
</header>
