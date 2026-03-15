-- Notes V2 - Etape 1 (version compatible MySQL/MariaDB anciens)
-- Objectif: script idempotent sans "ADD COLUMN IF NOT EXISTS"
-- Remarque: les DDL (ALTER/CREATE) font des commits implicites en MySQL.

-- =========================================================
-- 1) dd_personnages_notes: passage vers pno_dd
-- =========================================================

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_personnages_notes ADD COLUMN pno_dd TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER pno_no_id',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND COLUMN_NAME = 'pno_dd'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Reprise des valeurs legacy si pno_niveau existe encore.
SET @has_pno_niveau := (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND COLUMN_NAME = 'pno_niveau'
);

SET @sql_pno_backfill := IF(
  @has_pno_niveau > 0,
  'UPDATE dd_personnages_notes
   SET pno_dd = CASE
      WHEN pno_niveau IS NULL OR pno_niveau < 1 THEN 1
      WHEN pno_niveau > 99 THEN 99
      ELSE pno_niveau
   END
   WHERE (pno_dd IS NULL OR pno_dd = 1)',
  'SELECT 1'
);
PREPARE stmt_pno_backfill FROM @sql_pno_backfill;
EXECUTE stmt_pno_backfill;
DEALLOCATE PREPARE stmt_pno_backfill;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_personnages_notes ADD COLUMN pno_actif TINYINT(1) NOT NULL DEFAULT 1 AFTER pno_dd',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND COLUMN_NAME = 'pno_actif'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_pno_pe_no ON dd_personnages_notes (pno_pe_id, pno_no_id)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND INDEX_NAME = 'idx_pno_pe_no'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_pno_no_pe ON dd_personnages_notes (pno_no_id, pno_pe_id)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND INDEX_NAME = 'idx_pno_no_pe'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Un personnage ne doit avoir qu'une seule attribution par note.
SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_personnages_notes ADD UNIQUE INDEX uq_pno_pe_no (pno_pe_id, pno_no_id)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_personnages_notes'
    AND INDEX_NAME = 'uq_pno_pe_no'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =========================================================
-- 2) dd_notes_contenus: contenus multiples par note
-- =========================================================

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_notes_contenus ADD COLUMN noc_no_id INT UNSIGNED NOT NULL',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_notes_contenus'
    AND COLUMN_NAME = 'noc_no_id'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_notes_contenus ADD COLUMN noc_dd TINYINT UNSIGNED NOT NULL',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_notes_contenus'
    AND COLUMN_NAME = 'noc_dd'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE dd_notes_contenus ADD COLUMN noc_texte MEDIUMTEXT NOT NULL',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_notes_contenus'
    AND COLUMN_NAME = 'noc_texte'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_noc_no_dd ON dd_notes_contenus (noc_no_id, noc_dd)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_notes_contenus'
    AND INDEX_NAME = 'idx_noc_no_dd'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =========================================================
-- 3) Tags: nouvelle structure N-N
-- =========================================================

CREATE TABLE IF NOT EXISTS dd_tags (
  tag_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  tag_nom VARCHAR(120) NOT NULL,
  tag_slug VARCHAR(140) NOT NULL,
  tag_j_id INT UNSIGNED NOT NULL,
  tag_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (tag_id),
  UNIQUE KEY uq_tag_user_slug (tag_j_id, tag_slug),
  KEY idx_tag_nom (tag_nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dd_notes_tags (
  notag_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  notag_no_id INT UNSIGNED NOT NULL,
  notag_tag_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (notag_id),
  UNIQUE KEY uq_notag_note_tag (notag_no_id, notag_tag_id),
  KEY idx_notag_tag (notag_tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================================================
-- 4) Index recherche notes
-- =========================================================

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_notes_auteur_type_nom ON dd_notes (no_j_id, no_tyno_id, no_nom)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_notes'
    AND INDEX_NAME = 'idx_notes_auteur_type_nom'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql := (
  SELECT IF(
    COUNT(*) = 0,
    'CREATE INDEX idx_cpno_camp_no ON dd_campagnes_notes (cpno_camp_id, cpno_no_id)',
    'SELECT 1'
  )
  FROM information_schema.STATISTICS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'dd_campagnes_notes'
    AND INDEX_NAME = 'idx_cpno_camp_no'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

