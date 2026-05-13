<?php
// ============================================================
//  pridat.php – Pridanie novej úlohy (CREATE operácia)
//  Formulár sa odošle metódou POST na tú istú stránku
// ============================================================

include "db.php";

$chyby = [];   // pole pre chybové hlásenia
$uspech = "";  // správa o úspešnom uložení

// ── Spracovanie formulára (keď sa odoslal POST) ───────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Načítame hodnoty z formulára a odrežeme zbytočné medzery (trim)
    $nazov       = trim($_POST['nazov'] ?? '');
    $popis       = trim($_POST['popis'] ?? '');
    $kategoria   = (int)($_POST['kategoria_id'] ?? 0);

    // ── Validácia vstupov ─────────────────────────────────────────────
    if ($nazov === '') {
        $chyby[] = "Názov úlohy je povinný!";
    }
    if (strlen($nazov) > 255) {
        $chyby[] = "Názov je príliš dlhý (max 255 znakov).";
    }

    // ── Ak nie sú chyby, uložíme do databázy ─────────────────────────
    if (empty($chyby)) {
        // mysqli_real_escape_string ochráni pred SQL Injection útokmi
        $nazov_safe = mysqli_real_escape_string($conn, $nazov);
        $popis_safe = mysqli_real_escape_string($conn, $popis);
        $kat_val    = $kategoria > 0 ? $kategoria : "NULL";

        $sql = "INSERT INTO ulohy (nazov, popis, kategoria_id)
                VALUES ('$nazov_safe', '$popis_safe', $kat_val)";

        if (mysqli_query($conn, $sql)) {
            // Úspech – presmerujeme na hlavnú stránku
            header("Location: index.php");
            exit;
        } else {
            $chyby[] = "Chyba pri ukladaní: " . mysqli_error($conn);
        }
    }
}

// ── Načítame kategórie pre select dropdown ────────────────────────────────
$kategorie = mysqli_query($conn, "SELECT * FROM kategorie ORDER BY nazov");
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pridať úlohu – TodoApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:600px">

  <a href="index.php" class="text-muted text-decoration-none">← Späť</a>
  <h2 class="mt-2 mb-4">➕ Pridať úlohu</h2>

  <!-- Chybové hlásenia -->
  <?php if (!empty($chyby)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($chyby as $ch): ?>
          <li><?= htmlspecialchars($ch) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Formulár -->
  <!-- action="" = odošle na tú istú stránku | method="post" = bezpečnejšie ako GET -->
  <form action="" method="post" class="card p-4">

    <div class="mb-3">
      <label for="nazov" class="form-label fw-semibold">Názov úlohy *</label>
      <!-- value zachová zadaný text ak validácia zlyhá -->
      <input type="text"
             id="nazov"
             name="nazov"
             class="form-control"
             maxlength="255"
             value="<?= htmlspecialchars($_POST['nazov'] ?? '') ?>"
             placeholder="Napr. Napísať esej z literatúry"
             required>
    </div>

    <div class="mb-3">
      <label for="popis" class="form-label fw-semibold">Popis <small class="text-muted">(voliteľné)</small></label>
      <textarea id="popis" name="popis" class="form-control" rows="3"
                placeholder="Ďalšie detaily..."><?= htmlspecialchars($_POST['popis'] ?? '') ?></textarea>
    </div>

    <div class="mb-4">
      <label for="kategoria_id" class="form-label fw-semibold">Kategória</label>
      <select id="kategoria_id" name="kategoria_id" class="form-select">
        <option value="0">— Bez kategórie —</option>
        <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
          <option value="<?= $kat['id'] ?>"
            <?= (($_POST['kategoria_id'] ?? 0) == $kat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($kat['nazov']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">💾 Uložiť úlohu</button>
      <a href="index.php" class="btn btn-outline-secondary">Zrušiť</a>
    </div>

  </form>
</div>
</body>
</html>
