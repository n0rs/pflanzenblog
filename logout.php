<?php
require_once __DIR__ . '/funktionen/laden.php';
session_start();
session_destroy();
session_start();
sendeToast("Logout erfolgreich.");
header('Location: ' . projektPfad('index.php'));
exit;
?>
