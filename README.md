# 📝 TodoApp – PHP + MySQL projekt

Jednoduchá todo aplikácia s kategóriami. Školský projekt – demonštruje CRUD operácie v PHP s databázou MySQL.

## Funkcie

- ✅ Pridávanie, upravovanie a mazanie úloh
- 🏷️ Kategorizácia úloh s farebnými štítkami
- ☑️ Označovanie úloh ako splnených
- 🔍 Filtrovanie podľa kategórie
- 📊 Štatistiky (celkový počet / splnené)

## Technológie

- **Backend:** PHP 8.x (čisté PHP, bez frameworkov)
- **Databáza:** MySQL cez `mysqli`
- **Frontend:** HTML + Bootstrap 5

## Štruktúra súborov

```
todoapp/
├── db.php          # Pripojenie k databáze
├── index.php       # Hlavná stránka – zoznam úloh (READ)
├── pridat.php      # Pridanie novej úlohy (CREATE)
├── upravit.php     # Úprava úlohy (UPDATE)
├── zmazat.php      # Zmazanie úlohy (DELETE)
├── kategorie.php   # Správa kategórií (CRUD pre 2. tabuľku)
├── database.sql    # SQL skript – vytvorí DB, tabuľky, dummy dáta
└── README.md
```

## Inštalácia

1. Nainštaluj **XAMPP** (xampp.apachefriends.org)
2. Skopíruj priečinok `todoapp/` do `C:\xampp\htdocs\`
3. Spusti Apache a MySQL v XAMPP Control Panel
4. Otvor **phpMyAdmin** (`http://localhost/phpmyadmin`)
5. Klikni na „SQL" záložku a vlož obsah súboru `database.sql` → Spusti
6. Otvor `http://localhost/todoapp/` v prehliadači

## CRUD operácie

| Operácia | Súbor | SQL príkaz |
|----------|-------|------------|
| Create   | `pridat.php` | `INSERT INTO ulohy ...` |
| Read     | `index.php` | `SELECT ... FROM ulohy JOIN kategorie` |
| Update   | `upravit.php` | `UPDATE ulohy SET ...` |
| Delete   | `zmazat.php` | `DELETE FROM ulohy WHERE id = ?` |

## Databáza

**Tabuľka `kategorie`:** id, nazov, farba  
**Tabuľka `ulohy`:** id, nazov, popis, hotova, kategoria_id, datum_vytvorenia
