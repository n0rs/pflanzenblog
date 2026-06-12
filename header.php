<header class="site-header">
    <div class="header-brand">
        <h1>🌿 Pflanzenblog</h1>
        <?php if ($sicherheitsstufe >= 1): ?>
            <p class="user-status">Eingeloggt als <?php echo e($_SESSION['benutzername'] ?? 'Gast', ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
    <nav>
        <a href="index.php">
            <img src="icons/house.svg" alt="Home" class="icon-header">
            <span class="text-header">Übersicht</span>
        </a>
        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="beitrag_erstellen.php">
                <img src="icons/new.svg" alt="Neuer Beitrag" class="icon-header">
                <span class="text-header">Neuer Beitrag</span>
            </a>
        <?php endif; ?>
        <?php if ($sicherheitsstufe == 0): ?>
            <a href="registrieren.php">
                <img src="icons/registrieren.svg" alt="Registrieren" class="icon-header">
                <span class="text-header">Registrieren</span>
            </a>
        <?php endif; ?>
        <a href="<?php echo ($sicherheitsstufe >= 1) ? 'logout.php' : 'login.php'; ?>">
            <img src="icons/<?php echo ($sicherheitsstufe >= 1) ? 'logout.svg' : 'login.svg'; ?>"
                 alt="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>"
                 class="icon-header">

            <span class="text-header">
        <?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>
    </span>
        </a>
        <a href="ueber_uns.php">
            <img src="icons/question.svg" alt="Über uns" class="icon-header">
            <span class="text-header">Über uns</span>
        </a>
    </nav>
</header>
