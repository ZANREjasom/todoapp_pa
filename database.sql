-- ============================================================
--  TODOAPP – databázový skript
--  Spusti tento súbor v phpMyAdmin alebo cez MySQL klienta
--  Vytvorí databázu, 2 tabuľky a vloží testovacie dáta
-- ============================================================

-- 1. Vytvorenie databázy
CREATE DATABASE IF NOT EXISTS todoapp
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE todoapp;

-- ============================================================
-- TABUĽKA 1: kategorie
-- Každá úloha patrí do nejakej kategórie (Práca, Škola...)
-- ============================================================
CREATE TABLE IF NOT EXISTS kategorie (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nazov     VARCHAR(100) NOT NULL,
  farba     VARCHAR(7)   NOT NULL DEFAULT '#6c757d'
  -- farba je hex kód, napr. #FF5733
);

-- ============================================================
-- TABUĽKA 2: ulohy
-- Hlavná tabuľka so všetkými úlohami
-- kategoria_id je cudzí kľúč – prepája úlohu s kategóriou
-- ============================================================
CREATE TABLE IF NOT EXISTS ulohy (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nazov           VARCHAR(255) NOT NULL,
  popis           TEXT,
  hotova          TINYINT(1)   NOT NULL DEFAULT 0,  -- 0 = nesplnená, 1 = splnená
  kategoria_id    INT,
  datum_vytvorenia DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategoria_id) REFERENCES kategorie(id) ON DELETE SET NULL
);

-- ============================================================
-- TESTOVACIE DÁTA (dummy data)
-- ============================================================

-- Kategórie
INSERT INTO kategorie (nazov, farba) VALUES
  ('Škola',   '#4e73df'),
  ('Práca',   '#1cc88a'),
  ('Osobné',  '#f6c23e'),
  ('Nákupy',  '#e74a3b');

-- Úlohy
INSERT INTO ulohy (nazov, popis, hotova, kategoria_id) VALUES
  ('Projekt PHP',       'Dokončiť školský projekt do konca mesiaca', 0, 1),
  ('Matematika DÚ',     'Príklady zo strany 42-44',                  0, 1),
  ('Poslať faktúru',    'Faktúra č. 2024-05 pre klienta',            1, 2),
  ('Zavolať lekárovi',  NULL,                                         0, 3),
  ('Kúpiť mlieko',      '2x plnotučné, 1x odtučnené',               0, 4),
  ('Prečítať knihu',    'Dokončiť aspoň 50 strán tento týždeň',     0, 3);
