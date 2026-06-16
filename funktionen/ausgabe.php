<?php

function e($text)
{
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
}

function projektPfad(string $pfad = ''): string
{
    $scriptOrdner = basename(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')));
    $prefix = in_array($scriptOrdner, ['beitraege', 'kommentare'], true) ? '../' : '';

    return $prefix . ltrim($pfad, '/');
}

function inlineIcon(string $filename, array $attributes = []): string
{
    $path = dirname(__DIR__) . '/icons/' . $filename;
    if (!is_file($path)) {
        return '';
    }

    $svg = file_get_contents($path);
    if ($svg === false) {
        return '';
    }

    $attributeString = '';
    foreach ($attributes as $name => $value) {
        if ($value === null || $value === '') {
            continue;
        }
        $attributeString .= ' ' . e((string)$name) . '="' . e((string)$value) . '"';
    }

    if (strpos($svg, '<svg') !== false) {
        $svg = preg_replace('/<svg\b([^>]*)>/', '<svg$1' . $attributeString . '>', $svg, 1);
    }

    return $svg;
}

function pleskAssetUrl(string $pfad): string
{
    // Erkennt automatisch, ob es https oder http ist
    $protokoll = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';

    // Holt die aktuelle Domain (z.B. deine-domain.de oder eine Plesk-Vorschau-URL)
    $domain = $_SERVER['HTTP_HOST'] ?? '';

    // Ermittelt das Unterverzeichnis, falls das Projekt in einem Unterordner liegt
    $projektOrdner = dirname($_SERVER['SCRIPT_NAME'] ?? '');

    // Falls wir uns in 'beitraege' oder 'kommentare' befinden, schneiden wir das für die URL ab
    $projektOrdner = str_replace(['/beitraege', '/kommentare'], '', $projektOrdner);
    $projektOrdner = rtrim($projektOrdner, '/');

    // Gibt die perfekte, absolute URL zurück
    return $protokoll . $domain . $projektOrdner . '/' . ltrim($pfad, '/');
}

function formatDate(string $datum): string
{
    try {
        $date = new DateTimeImmutable($datum);
        return $date->format('d.m.Y H:i');
    } catch (Exception $e) {
        return e($datum);
    }
}

function sendeToast(string $nachricht): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['toast_nachricht'] = $nachricht;
}

function leereSuche($suchbegriff)
{
    ?>
        <div class="container">
            <label class="icon-box">
                <?php echo inlineIcon('search.svg', ['class' => 'gross-icon', 'role' => 'img', 'aria-label' => 'Lupe', 'title' => 'Suche']); ?>
                <span >Keine Ergebnisse zu "<?php echo $suchbegriff ?>" gefunden.</span>
            </label>
        </div>

    <?php
}
