<?php
require_once 'funktionen.php';
session_start();
session_destroy();
session_start();
sendeToast("Logout erfolgreich.");
header("Location: index.php");
exit;
?>