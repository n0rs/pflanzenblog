CREATE TABLE IF NOT EXISTS `beitraege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titel` varchar(266) NOT NULL,
  `inhalt` text NOT NULL,
  `bild` varchar(255) DEFAULT NULL,
  `botanischer_name` varchar(255) DEFAULT NULL,
  `standort` varchar(255) DEFAULT NULL,
  `bewasserung` varchar(50) DEFAULT NULL,
  `lichtmenge` varchar(50) DEFAULT NULL,
  `winterhart` varchar(255) DEFAULT NULL,
  `schwierigkeitsgrad` varchar(50) DEFAULT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `benutzer_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `benutzer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzername` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `sicherheitsstufe` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `benutzername` (`benutzername`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `kommentare` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `benutzer_id` int(11) NOT NULL,
  `inhalt` text NOT NULL,
  `datum` timestamp NOT NULL DEFAULT current_timestamp(),
  `beitrag_id` int(11) NOT NULL,
  `kom_id` int(11) DEFAULT NULL AFTER beitrag_id,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


### Beispielblgoposts
INSERT INTO beitraege 
(titel, inhalt, botanischer_name, standort, bewasserung, lichtmenge, winterhart, schwierigkeitsgrad, benutzer_id)
VALUES
(
    'Monstera pflegen: So bleibt das Fensterblatt gesund',
    'Die Monstera ist eine der beliebtesten Zimmerpflanzen und überzeugt mit ihren großen, geschlitzten Blättern. Sie bevorzugt einen hellen Standort ohne direkte Mittagssonne. Gegossen wird erst, wenn die obere Erdschicht leicht angetrocknet ist. Besonders wohl fühlt sie sich bei hoher Luftfeuchtigkeit. Wer regelmäßig die Blätter abwischt und der Pflanze eine Rankhilfe gibt, wird lange Freude an ihr haben.',
    'Monstera deliciosa',
    'Heller Standort ohne direkte Mittagssonne',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'einfach',
    1
),
(
    'Lavendel im Garten: Duftender Klassiker für sonnige Beete',
    'Lavendel bringt mediterranes Flair in den Garten und ist gleichzeitig sehr pflegeleicht. Die Pflanze liebt volle Sonne, durchlässigen Boden und eher wenig Wasser. Staunässe sollte unbedingt vermieden werden. Nach der Blüte empfiehlt sich ein Rückschnitt, damit der Lavendel kompakt bleibt und im nächsten Jahr wieder kräftig austreibt.',
    'Lavandula angustifolia',
    'Sonniges Beet oder Kübel',
    'wenig',
    'viel',
    'Winterhart',
    'einfach',
    1
),
(
    'Calathea: Die anspruchsvolle Schönheit mit gemusterten Blättern',
    'Calatheas sind echte Hingucker, benötigen aber etwas mehr Aufmerksamkeit. Sie mögen warme Temperaturen, hohe Luftfeuchtigkeit und gleichmäßig feuchte Erde. Direktes Sonnenlicht kann die empfindlichen Blätter beschädigen. Am besten eignet sich kalkarmes Wasser zum Gießen. Braune Blattränder sind oft ein Zeichen für zu trockene Luft oder unregelmäßige Pflege.',
    'Calathea orbifolia',
    'Warm, hell bis halbschattig, ohne direkte Sonne',
    'mittel',
    'mittel',
    'Nicht winterhart',
    'anspruchsvoll',
    1
),
(
    'Tomaten auf dem Balkon anbauen',
    'Tomaten lassen sich hervorragend auf dem Balkon kultivieren. Wichtig sind ein großer Topf, nährstoffreiche Erde und ein sonniger Platz. Die Pflanzen brauchen regelmäßig Wasser, sollten aber nicht über die Blätter gegossen werden. Eine Rankhilfe sorgt für Stabilität. Wer regelmäßig ausgeizt und reife Früchte erntet, kann den ganzen Sommer über frische Tomaten genießen.',
    'Solanum lycopersicum',
    'Sonniger Balkon, windgeschützt',
    'viel',
    'viel',
    'Nicht winterhart',
    'mittel',
    1
),
(
    'Bogenhanf: Die perfekte Pflanze für Anfänger',
    'Der Bogenhanf ist extrem robust und eignet sich ideal für Anfänger. Er kommt mit wenig Wasser aus und verträgt sowohl helle als auch halbschattige Standorte. Staunässe sollte vermieden werden, da die Wurzeln sonst faulen können. Besonders praktisch: Der Bogenhanf wächst langsam und muss nur selten umgetopft werden.',
    'Sansevieria trifasciata',
    'Hell bis halbschattig',
    'wenig',
    'mittel',
    'Nicht winterhart',
    'einfach',
    1
);