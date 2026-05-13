<?php
// ============================================================
//  zmazat.php – Zmazanie úlohy (DELETE operácia)
//  URL: zmazat.php?id=5  →  zmaže úlohu s ID 5
//  Táto stránka nevykresľuje HTML – len spracuje akciu a presmeruje
// ============================================================

include "db.php";

// Načítame ID z URL
$id = (int)($_GET['id'] ?? 0);

// Bezpečnostná kontrola – ID musí byť kladné číslo
if ($id > 0) {
    // Overíme že úloha naozaj existuje (ochrana pred neplatnými ID)
    $res = mysqli_query($conn, "SELECT id FROM ulohy WHERE id = $id");

    if (mysqli_num_rows($res) > 0) {
        // Vykonáme DELETE
        mysqli_query($conn, "DELETE FROM ulohy WHERE id = $id");
    }
}

// Vždy presmerujeme späť na hlavnú stránku
header("Location: index.php");
exit;
?>
