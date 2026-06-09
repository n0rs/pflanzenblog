<header class="site-header">
    <div class="header-brand">
        <h1>🌿 Pflanzenblog</h1>
        <?php if ($sicherheitsstufe >= 1): ?>
            <p class="user-status">Eingeloggt als <?php echo htmlspecialchars($_SESSION['benutzername'] ?? 'Gast', ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </div>
    <nav>
        <a href="index.php">Übersicht</a>
        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="beitrag_erstellen.php">Neuer Beitrag</a>
        <?php endif; ?>
        <?php if ($sicherheitsstufe == 0): ?>
            <a href="registrieren.php">Registrieren</a>
        <?php endif; ?>
        <a href="<?php echo ($sicherheitsstufe >= 1) ? 'logout.php' : 'login.php'; ?>">
            <?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>
        </a>
        <a href="ueber_uns.php">Über uns</a>
    </nav>
</header>
