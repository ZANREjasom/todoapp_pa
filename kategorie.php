<?php
// ============================================================
//  kategorie.php – Správa kategórií (CRUD pre 2. tabuľku)
//  Pridávanie, zobrazenie a mazanie kategórií
// ============================================================

include "db.php";

$chyby = [];

// ── Pridanie kategórie (CREATE) ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['akcia']) && $_POST['akcia'] === 'pridat') {
    $nazov = trim($_POST['nazov'] ?? '');
    $farba = $_POST['farba'] ?? '#6c757d';

    if ($nazov === '') {
        $chyby[] = "Názov kategórie je povinný!";
    }
    if (strlen($nazov) > 100) {
        $chyby[] = "Názov je príliš dlhý.";
    }

    if (empty($chyby)) {
        $nazov_safe = mysqli_real_escape_string($conn, $nazov);
        $farba_safe = mysqli_real_escape_string($conn, $farba);
        mysqli_query($conn, "INSERT INTO kategorie (nazov, farba) VALUES ('$nazov_safe', '$farba_safe')");
        header("Location: kategorie.php");
        exit;
    }
}

// ── Zmazanie kategórie (DELETE) ───────────────────────────────────────────
if (isset($_GET['zmazat'])) {
    $id = (int)$_GET['zmazat'];
    if ($id > 0) {
        // Úlohy zostanú, len sa im nastaví kategoria_id = NULL (riešené cez FOREIGN KEY ON DELETE SET NULL)
        mysqli_query($conn, "DELETE FROM kategorie WHERE id = $id");
    }
    header("Location: kategorie.php");
    exit;
}

// ── Načítanie všetkých kategórií ─────────────────────────────────────────
// Spočítame aj počet úloh v každej kategórii (LEFT JOIN + COUNT)
$sql = "
    SELECT k.*, COUNT(u.id) AS pocet_uloh
    FROM kategorie k
    LEFT JOIN ulohy u ON k.id = u.kategoria_id
    GROUP BY k.id
    ORDER BY k.nazov
";
$kategorie = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kategórie – TodoApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4" style="max-width:700px">

  <a href="index.php" class="text-muted text-decoration-none">← Späť na úlohy</a>
  <h2 class="mt-2 mb-4">⚙️ Kategórie</h2>

  <!-- Chyby -->
  <?php if (!empty($chyby)): ?>
    <div class="alert alert-danger">
      <?php foreach ($chyby as $ch): ?>
        <div><?= htmlspecialchars($ch) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">

    <!-- Formulár na pridanie -->
    <div class="col-md-5">
      <div class="card p-4">
        <h5 class="mb-3">Nová kategória</h5>
        <form action="" method="post">
          <!-- Skrytý input hovorí PHP-čku čo sa má vykonať -->
          <input type="hidden" name="akcia" value="pridat">

          <div class="mb-3">
            <label class="form-label fw-semibold">Názov *</label>
            <input type="text" name="nazov" class="form-control"
                   value="<?= htmlspecialchars($_POST['nazov'] ?? '') ?>"
                   placeholder="Napr. Zdravie" maxlength="100" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold">Farba</label>
            <!-- type="color" zobrazí natívny výber farby -->
            <input type="color" name="farba" class="form-control form-control-color"
                   value="<?= htmlspecialchars($_POST['farba'] ?? '#4e73df') ?>">
          </div>

          <button type="submit" class="btn btn-primary w-100">Pridať kategóriu</button>
        </form>
      </div>
    </div>

    <!-- Zoznam kategórií -->
    <div class="col-md-7">
      <div class="card">
        <div class="list-group list-group-flush">
          <?php if (mysqli_num_rows($kategorie) === 0): ?>
            <div class="list-group-item text-muted text-center py-4">
              Žiadne kategórie. Pridaj prvú!
            </div>
          <?php endif; ?>

          <?php while ($kat = mysqli_fetch_assoc($kategorie)): ?>
            <div class="list-group-item d-flex align-items-center gap-3">
              <!-- Farebný krúžok -->
              <span style="width:18px;height:18px;border-radius:50%;background:<?= $kat['farba'] ?>;flex-shrink:0"></span>

              <!-- Názov + počet úloh -->
              <div class="flex-grow-1">
                <strong><?= htmlspecialchars($kat['nazov']) ?></strong>
                <span class="text-muted small ms-2"><?= $kat['pocet_uloh'] ?> úloh</span>
              </div>

              <!-- Mazanie (len ak kategória nemá úlohy) -->
              <?php if ($kat['pocet_uloh'] == 0): ?>
                <a href="kategorie.php?zmazat=<?= $kat['id'] ?>"
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Zmazať kategóriu?')">🗑</a>
              <?php else: ?>
                <span class="text-muted small" title="Najprv zmaž alebo presuň úlohy">🔒</span>
              <?php endif; ?>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

  </div>
</div>
</body>
</html>
