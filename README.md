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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
