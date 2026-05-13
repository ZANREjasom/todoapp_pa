<?php
// ============================================================
//  db.php – pripojenie k databáze
//  Tento súbor includujeme na každej stránke kde treba DB
// ============================================================

$host     = "localhost";   // adresa MySQL servera (XAMPP = localhost)
$uzivatel = "root";        // MySQL používateľ (XAMPP default = root)
$heslo    = "root";            // MySQL heslo (XAMPP default = prázdne)
$databaza = "todoapp_pa";     // názov databázy

// Vytvorí pripojenie pomocou mysqli
$conn = mysqli_connect($host, $uzivatel, $heslo, $databaza);

// Skontroluje či sa pripojenie podarilo
if (!$conn) {
    // Ak nie, zastaví skript a ukáže chybu
    die("Chyba pripojenia k databáze: " . mysqli_connect_error());
}

// Nastavíme kódovanie na UTF-8 (kvôli slovenčine)
mysqli_set_charset($conn, "utf8mb4");
?>
