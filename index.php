<?php
// ============================================================
//  index.php – Hlavná stránka (READ operácia)
//  Zobrazuje zoznam všetkých úloh + filtruje podľa kategórie
// ============================================================

include "db.php";  // pripojíme databázu

// ── Spracovanie "označ ako splnenú/nesplnenú" (bez extra stránky) ────────
// Keď klikneme na checkbox, pošle GET parameter ?toggle=ID
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];  // (int) = bezpečná konverzia na číslo

    // Načítame aktuálny stav úlohy
    $res = mysqli_query($conn, "SELECT hotova FROM ulohy WHERE id = $id");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        // Prehodíme 0 na 1 alebo 1 na 0
        $novy_stav = $row['hotova'] ? 0 : 1;
        mysqli_query($conn, "UPDATE ulohy SET hotova = $novy_stav WHERE id = $id");
    }

    // Presmerujeme späť na index (aby refresh nezopakoval akciu)
    header("Location: index.php");
    exit;
}

// ── Filter podľa kategórie (GET parameter ?kategoria=ID) ─────────────────
$filter_kategoria = isset($_GET['kategoria']) ? (int)$_GET['kategoria'] : 0;

// ── Načítanie úloh z databázy (SELECT = READ) ────────────────────────────
// JOIN spája tabuľku ulohy s tabuľkou kategorie
$sql = "
    SELECT u.*, k.nazov AS kat_nazov, k.farba AS kat_farba
    FROM ulohy u
    LEFT JOIN kategorie k ON u.kategoria_id = k.id
";

// Ak je nastavený filter, pridáme podmienku WHERE
if ($filter_kategoria > 0) {
    $sql .= " WHERE u.kategoria_id = $filter_kategoria";
}

$sql .= " ORDER BY u.hotova ASC, u.datum_vytvorenia DESC";
// ASC pri hotova = nesplnené sú hore, splnené dole

$vysledok = mysqli_query($conn, $sql);

// ── Načítanie kategórií pre filter menu ─────────────────────────────────
$kategorie = mysqli_query($conn, "SELECT * FROM kategorie ORDER BY nazov");

// ── Štatistiky ───────────────────────────────────────────────────────────
$stat = mysqli_query($conn, "SELECT COUNT(*) as celkom, SUM(hotova) as splnene FROM ulohy");
$stat_row = mysqli_fetch_assoc($stat);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TodoApp</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .uloha-splnena td { opacity: 0.5; text-decoration: line-through; }
    .kat-badge { font-size: 0.75rem; padding: 3px 8px; border-radius: 12px; color: #fff; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">

  <!-- Hlavička -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">📝 Moje úlohy</h1>
    <a href="pridat.php" class="btn btn-primary">+ Pridať úlohu</a>
  </div>

  <!-- Štatistiky -->
  <div class="row mb-4">
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold"><?= $stat_row['celkom'] ?></div>
        <div class="text-muted small">Celkom úloh</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card text-center p-3">
        <div class="fs-2 fw-bold text-success"><?= (int)$stat_row['splnene'] ?></div>
        <div class="text-muted small">Splnených</div>
      </div>
    </div>
  </div>

  <!-- Filter podľa kategórie -->
  <div class="mb-3">
    <a href="index.php" class="btn btn-sm <?= $filter_kategoria == 0 ? 'btn-dark' : 'btn-outline-dark' ?>">
      Všetky
    </a>
    
    <?php
    // Vypíšeme tlačidlo pre každú kategóriu
    mysqli_data_seek($kategorie, 0); // reset kurzora
    while ($kat = mysqli_fetch_assoc($kategorie)):
    ?>
      <a href="index.php?kategoria=<?= $kat['id'] ?>"
         class="btn btn-sm <?= $filter_kategoria == $kat['id'] ? 'btn-dark' : 'btn-outline-secondary' ?>">
        <?= htmlspecialchars($kat['nazov']) ?>
      </a>
    <?php endwhile; ?>
    <a href="kategorie.php" class="btn btn-sm btn-outline-info ms-2">⚙ Kategórie</a>
  </div>

  <!-- Zoznam úloh -->
  <div class="card">
    <table class="table table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th width="40">✓</th>
          <th>Úloha</th>
          <th>Kategória</th>
          <th>Vytvorená</th>
          <th width="120">Akcie</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($vysledok) == 0): ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              Žiadne úlohy. <a href="pridat.php">Pridaj prvú!</a>
            </td>
          </tr>
        <?php endif; ?>

        <?php while ($uloha = mysqli_fetch_assoc($vysledok)): ?>
          <tr class="<?= $uloha['hotova'] ? 'uloha-splnena' : '' ?>">
            <!-- Checkbox – toggle splnenia -->
            <td>
              <a href="index.php?toggle=<?= $uloha['id'] ?>" title="Označiť">
                <?= $uloha['hotova'] ? '☑' : '☐' ?>
              </a>
            </td>

            <!-- Názov + popis -->
            <td>
              <strong><?= htmlspecialchars($uloha['nazov']) ?></strong>
              <?php if ($uloha['popis']): ?>
                <br><small class="text-muted"><?= htmlspecialchars($uloha['popis']) ?></small>
              <?php endif; ?>
            </td>

            <!-- Kategória (farebný odznak) -->
            <td>
              <?php if ($uloha['kat_nazov']): ?>
                <span class="kat-badge" style="background:<?= $uloha['kat_farba'] ?>">
                  <?= htmlspecialchars($uloha['kat_nazov']) ?>
                </span>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>

            <!-- Dátum -->
            <td class="text-muted small">
              <?= date('d.m.Y', strtotime($uloha['datum_vytvorenia'])) ?>
            </td>

            <!-- Tlačidlá Upraviť / Zmazať -->
            <td>
              <a href="upravit.php?id=<?= $uloha['id'] ?>" class="btn btn-sm btn-outline-secondary">✏</a>
              <a href="zmazat.php?id=<?= $uloha['id'] ?>"
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Naozaj zmazať túto úlohu?')">🗑</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
