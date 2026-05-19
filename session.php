<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function istGast(): bool
{
    return !isset($_SESSION['benutzername']);
}

function istAngemeldet(): bool
{
    return isset($_SESSION['benutzername']);
}

function holeBenutzername(): string
{
    return $_SESSION['benutzername'] ?? 'Gast';
}

function holeAutorId(): ?int
{
    return isset($_SESSION['benuter_id']) ? (int) $_SESSION['benuter_id'] : null;
}

function setzeBenutzerInSession(string $benutzername, int $autorId): void
{
    $_SESSION['benutzername'] = $benutzername;
    $_SESSION['benuter_id'] = $autorId;
}

function loescheBenutzerSession(): void
{
    unset($_SESSION['benutzername'], $_SESSION['benuter_id']);
}

function sichereAusgabe(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
