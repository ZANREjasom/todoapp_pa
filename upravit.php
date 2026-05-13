<?php
// ============================================================
//  upravit.php – Úprava existujúcej úlohy (UPDATE operácia)
//  URL: upravit.php?id=5  →  načíta úlohu s ID 5
// ============================================================

include "db.php";

// Načítame ID z URL (GET parameter)
$id = (int)($_GET['id'] ?? 0);

// Ak nie je platné ID, presmerujeme späť
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// ── Načítame existujúcu úlohu z DB ───────────────────────────────────────
$res   = mysqli_query($conn, "SELECT * FROM ulohy WHERE id = $id");
$uloha = mysqli_fetch_assoc($res);

// Ak úloha neexistuje, presmerujeme
if (!$uloha) {
    header("Location: index.php");
    exit;
}

$chyby = [];

// ── Spracovanie formulára (UPDATE) ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nazov     = trim($_POST['nazov'] ?? '');
    $popis     = trim($_POST['popis'] ?? '');
    $kategoria = (int)($_POST['kategoria_id'] ?? 0);
    $hotova    = isset($_POST['hotova']) ? 1 : 0;

    // Validácia
    if ($nazov === '') {
        $chyby[] = "Názov úlohy je povinný!";
    }
    if (strlen($nazov) > 255) {
        $chyby[] = "Názov je príliš dlhý.";
    }

    if (empty($chyby)) {
        $nazov_safe = mysqli_real_escape_string($conn, $nazov);
        $popis_safe = mysqli_real_escape_string($conn, $popis);
        $kat_val    = $kategoria > 0 ? $kategoria : "NULL";

        $sql = "UPDATE ulohy
                SET nazov        = '$nazov_safe',
                    popis        = '$popis_safe',
                    kategoria_id = $kat_val,
                    hotova       = $hotova
                WHERE id = $id";

        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
            exit;
        } else {
            $chyby[] = "Chyba pri ukladaní: " . mysqli_error($conn);
        }
    }
}

// ── Kategórie pre dropdown ────────────────────────────────────────────────
$kategorie = mysqli_query($conn, "SELECT * FROM kategorie ORDER BY nazov");
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Upraviť úlohu – TodoApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:600px">

  <a href="index.php" class="text-muted text-decoration-none">← Späť</a>
  <h2 class="mt-2 mb-4">✏️ Upraviť úlohu</h2>

  <?php if (!empty($chyby)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($chyby as $ch): ?>
          <li><?= htmlspecialchars($ch) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="" method="post" class="card p-4">

    <div class="mb-3">
      <label for="nazov" class="form-label fw-semibold">Názov úlohy *</label>
      <input type="text"
             id="nazov"
             name="nazov"
             class="form-control"
             maxlength="255"
             value="<?= htmlspecialchars($_POST['nazov'] ?? $uloha['nazov']) ?>"
             required>
    </div>

    <div class="mb-3">
      <label for="popis" class="form-label fw-semibold">Popis</label>
      <textarea id="popis" name="popis" class="form-control" rows="3"><?=
        htmlspecialchars($_POST['popis'] ?? $uloha['popis'])
      ?></textarea>
    </div>

    <div class="mb-3">
      <label for="kategoria_id" class="form-label fw-semibold">Kategória</label>
      <select id="kategoria_id" name="kategoria_id" class="form-select">
        <option value="0">— Bez kategórie —</option>
        <?php
        // Aktuálna kategória úlohy (z POST alebo z DB)
        $aktualna_kat = $_POST['kategoria_id'] ?? $uloha['kategoria_id'];
        while ($kat = mysqli_fetch_assoc($kategorie)):
        ?>
          <option value="<?= $kat['id'] ?>"
            <?= ($aktualna_kat == $kat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($kat['nazov']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-4">
      <div class="form-check">
        <!-- Checkbox pre stav splnenia -->
        <input type="checkbox"
               id="hotova"
               name="hotova"
               class="form-check-input"
               <?= (($_POST['hotova'] ?? $uloha['hotova']) ? 'checked' : '') ?>>
        <label for="hotova" class="form-check-label">Označiť ako splnenú</label>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-success">💾 Uložiť zmeny</button>
      <a href="index.php" class="btn btn-outline-secondary">Zrušiť</a>
    </div>

  </form>
</div>
</body>
</html>
