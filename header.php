<header class="site-header">
    <div class="header-brand">
        <h1>🌿 Pflanzenblog</h1>
        <?php if ($sicherheitsstufe >= 1): ?>
            <div class="user-status">
              <img src="icons/account.svg" alt="Account" class="icon stay" title="<?php echo e($_SESSION['benutzername'], ENT_QUOTES, 'UTF-8'); ?>">
               <p><?php echo e($_SESSION['benutzername'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <nav>
        <a href="index.php">
            <img src="icons/house.svg" alt="Home" class="icon-header" title="Übersicht">
            <span class="text-header" title="Übersicht">Übersicht</span>
        </a>
        <?php if ($sicherheitsstufe >= 1): ?>
            <a href="beitrag_erstellen.php">
                <img src="icons/new.svg" alt="Neuer Beitrag" class="icon-header" title="Neuer Beitrag">
                <span class="text-header" title="Neuer Beitrag">Neuer Beitrag</span>
            </a>
        <?php endif; ?>
        <?php if ($sicherheitsstufe == 0): ?>
            <a href="registrieren.php">
                <img src="icons/registrieren.svg" alt="Registrieren" class="icon-header" title="Registrieren">
                <span class="text-header" title="Registrieren">Registrieren</span>
            </a>
        <?php endif; ?>
        <a href="<?php echo ($sicherheitsstufe >= 1) ? 'logout.php' : 'login.php'; ?>">
            <img src="icons/<?php echo ($sicherheitsstufe >= 1) ? 'logout.svg' : 'login.svg'; ?>"
                 alt="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>"
                 class="icon-header" title="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>">

            <span class="text-header" title="<?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>">
        <?php echo ($sicherheitsstufe >= 1) ? 'Logout' : 'Login'; ?>
    </span>
        </a>
        <a href="ueber_uns.php">
            <img src="icons/question.svg" alt="Über uns" class="icon-header" title="Über uns">
            <span class="text-header" title="Über uns">Über uns</span>
        </a>
    </nav>
</header>
