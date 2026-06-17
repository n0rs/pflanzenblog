<?php

function holeKommentare(mysqli $datenbankverbindung, int $beitrag_id): array
{
    $anweisung = $datenbankverbindung->prepare(
        "SELECT kommentare.inhalt,
                kommentare.datum,
                kommentare.id,
                kommentare.benutzer_id,
                kommentare.beitrag_id,
                kommentare.kom_id,
                benutzer.benutzername
         FROM kommentare
         LEFT JOIN benutzer ON kommentare.benutzer_id = benutzer.id
         WHERE kommentare.beitrag_id = ?
         ORDER BY kommentare.datum ASC"
    );

    $anweisung->bind_param('i', $beitrag_id);
    $anweisung->execute();

    $ergebnis = $anweisung->get_result();

    return $ergebnis ? $ergebnis->fetch_all(MYSQLI_ASSOC) : [];
}

function baueKommentarBaum(array $kommentare): array
{
    $nachId = [];
    $baum = [];

    foreach ($kommentare as $kommentar) {
        $kommentar['antworten'] = [];
        $nachId[$kommentar['id']] = $kommentar;
    }

    foreach ($nachId as &$kommentar) {
        if (!empty($kommentar['kom_id']) && isset($nachId[$kommentar['kom_id']])) {
            $nachId[$kommentar['kom_id']]['antworten'][] = &$kommentar;
        } else {
            $baum[] = &$kommentar;
        }
    }

    unset($kommentar);

    return $baum;
}

function zeigeKommentarMitAntworten(
    array $kommentar,
    array $beitrag,
    int|null $aktueller_benutzer_id,
    int $sicherheitsstufe,
    bool $istDetailseite,
    int $tiefe = 0
): void {
    $maxTiefe = 5;
    $cssKlasse = $tiefe > 0 ? 'kommentar antwort' : 'ausklappen-inhalt kommentar';

    ?>
    <div class="<?php echo $cssKlasse; ?>" id="kommentar-<?php echo (int)$kommentar['id']; ?>">
        <p><?php echo nl2br(e($kommentar['inhalt'])); ?></p>

        <small>
            <?php echo $tiefe > 0 ? 'Antwort von' : 'Von'; ?>
            <strong><?php echo e($kommentar['benutzername'] ?? 'Gast'); ?></strong>
            am <?php echo formatDate($kommentar['datum']); ?>
        </small>

        <?php if (istEigentuemer($kommentar, $aktueller_benutzer_id, $sicherheitsstufe)): ?>
            <div class="kommentar-aktionen">
                <details class="kommentar-bearbeiten-details-inline">
                    <summary title="Bearbeiten">
                        <?php echo inlineIcon('pencil.svg', ['class' => 'icon', 'role' => 'img', 'aria-label' => 'Bearbeiten', 'title' => 'Bearbeiten']); ?>
                    </summary>

                    <form action="<?php echo projektPfad('kommentare/kommentar_aktualisieren.php'); ?>"
                        method="POST"
                        class="kommentar-formular kommentar-bearbeiten-formular-inline">
                        <input type="hidden" name="kommentar_id" value="<?php echo (int)$kommentar['id']; ?>">

                        <textarea name="inhalt" required><?php echo e($kommentar['inhalt']); ?></textarea>

                        <button type="submit">Speichern</button>
                    </form>
                </details>

                <a href="<?php echo projektPfad('kommentare/kommentar_loeschen.php?id=' . (int)$kommentar['id']); ?>"
                onclick="return confirm('Kommentar wirklich löschen?');">
                    <?php echo inlineIcon('trash.svg', ['class' => 'icon', 'role' => 'img', 'aria-label' => 'Löschen', 'title' => 'Löschen']); ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($istDetailseite && $sicherheitsstufe >= 1): ?>
            <details class="antwort-details">
                <summary class="antwort-button">Antworten</summary>

                <form action="<?php echo projektPfad('kommentare/kommentar_erstellen.php?beitrag_id=' . (int)$beitrag['id']); ?>"
                    method="POST"
                    class="kommentar-formular antwort-formular">
                    <input type="hidden" name="kom_id" value="<?php echo (int)$kommentar['id']; ?>">

                    <label for="antwort_<?php echo (int)$kommentar['id']; ?>">
                        Antwort schreiben
                    </label>

                    <textarea id="antwort_<?php echo (int)$kommentar['id']; ?>" name="inhalt" required></textarea>
                    <div class="antwort_absenden">
                        <button type="submit" name="kommentar_absenden">
                            <?php echo inlineIcon('send.svg', ['class' => 'icon-button', 'role' => 'img', 'aria-label' => 'Senden', 'title' => 'Senden']); ?>
                            <span class="text-button">Antwort absenden</span>
                        </button>
                    </div>
                </form>
            </details>
        <?php endif; ?>

        <?php if (!empty($kommentar['antworten'])): ?>
            <div class="antworten tiefe-<?php echo min($tiefe + 1, $maxTiefe); ?>">
                <?php foreach ($kommentar['antworten'] as $antwort): ?>
                    <?php zeigeKommentarMitAntworten(
                        $antwort,
                        $beitrag,
                        $aktueller_benutzer_id,
                        $sicherheitsstufe,
                        $istDetailseite,
                        $tiefe + 1
                    ); ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

