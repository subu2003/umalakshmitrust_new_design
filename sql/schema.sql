-- Umalakshmi Educational Trust — database schema (MySQL 5.7+/8.x, MariaDB 10.3+)
-- Run once in cPanel → phpMyAdmin → Import, after creating the database and user
-- in cPanel → MySQL® Databases. Table names follow BACKEND-SPEC.md.
-- Step 3 (applications) tables are here; later steps append to this file.

SET NAMES utf8mb4;
SET time_zone = '+05:30';

CREATE TABLE IF NOT EXISTS applications (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ref_no          VARCHAR(20)  NOT NULL,                -- UET-2026-0001
  academic_year   VARCHAR(9)   NOT NULL,                -- 2026-27
  status          ENUM('new','under_review','shortlisted','selected','rejected') NOT NULL DEFAULT 'new',
  submitted_at    DATETIME     NOT NULL,
  ip              VARCHAR(45)  NOT NULL DEFAULT '',
  consent_declared TINYINT(1)  NOT NULL DEFAULT 0,
  parent_name     VARCHAR(120) NOT NULL DEFAULT '',
  declared_on     DATE         NULL,
  pdf_file_id     INT UNSIGNED NULL,                    -- generated application PDF (files.id)
  notes           TEXT         NULL,                    -- trustees' notes (admin)
  PRIMARY KEY (id),
  UNIQUE KEY uq_ref_no (ref_no),
  KEY idx_status (status),
  KEY idx_year (academic_year)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS applicants (
  application_id  INT UNSIGNED NOT NULL,
  name            VARCHAR(120) NOT NULL,
  gender          ENUM('male','female','') NOT NULL DEFAULT '',
  dob             DATE         NULL,
  standard        VARCHAR(40)  NOT NULL DEFAULT '',
  course          VARCHAR(120) NOT NULL DEFAULT '',
  phone           VARCHAR(20)  NOT NULL DEFAULT '',
  whatsapp        VARCHAR(20)  NOT NULL DEFAULT '',
  email           VARCHAR(190) NOT NULL DEFAULT '',
  address         TEXT         NULL,
  photo_file_id   INT UNSIGNED NULL,
  PRIMARY KEY (application_id),
  KEY idx_email (email),
  KEY idx_phone (phone),
  CONSTRAINT fk_applicants_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS family_members (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id  INT UNSIGNED NOT NULL,
  relation        ENUM('father','mother','sibling','guardian') NOT NULL,
  name            VARCHAR(120) NOT NULL DEFAULT '',
  education       VARCHAR(120) NOT NULL DEFAULT '',
  occupation      VARCHAR(120) NOT NULL DEFAULT '',
  yearly_income   DECIMAL(12,2) NULL,
  PRIMARY KEY (id),
  KEY idx_app (application_id),
  CONSTRAINT fk_family_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS family_details (
  application_id      INT UNSIGNED NOT NULL,
  other_income        ENUM('yes','no','') NOT NULL DEFAULT '',
  other_income_detail VARCHAR(255) NOT NULL DEFAULT '',
  house               ENUM('own','rent','') NOT NULL DEFAULT '',
  rent_per_month      DECIMAL(10,2) NULL,
  PRIMARY KEY (application_id),
  CONSTRAINT fk_famdet_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS education (
  application_id  INT UNSIGNED NOT NULL,
  institution     VARCHAR(190) NOT NULL DEFAULT '',
  inst_type       VARCHAR(60)  NOT NULL DEFAULT '',
  address         TEXT         NULL,
  phone           VARCHAR(20)  NOT NULL DEFAULT '',
  hm_name         VARCHAR(120) NOT NULL DEFAULT '',
  passed_all      ENUM('yes','no','') NOT NULL DEFAULT '',
  `rank`          INT UNSIGNED NULL,
  class_strength  INT UNSIGNED NULL,
  awards          TEXT         NULL,
  PRIMARY KEY (application_id),
  CONSTRAINT fk_edu_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS marks (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id  INT UNSIGNED NOT NULL,
  subject         VARCHAR(80)  NOT NULL,               -- 'Total' row stored with subject = 'Total'
  quarterly       DECIMAL(5,2) NULL,
  half_yearly     DECIMAL(5,2) NULL,
  annual          DECIMAL(5,2) NULL,
  PRIMARY KEY (id),
  KEY idx_app (application_id),
  CONSTRAINT fk_marks_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS funding (
  application_id  INT UNSIGNED NOT NULL,
  applied_before  ENUM('yes','no','') NOT NULL DEFAULT '',
  received_before ENUM('yes','no','') NOT NULL DEFAULT '',
  years           VARCHAR(120) NOT NULL DEFAULT '',
  total_fees      DECIMAL(12,2) NULL,
  PRIMARY KEY (application_id),
  CONSTRAINT fk_funding_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fee_items (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  application_id  INT UNSIGNED NOT NULL,
  label           VARCHAR(120) NOT NULL,
  amount          DECIMAL(12,2) NULL,
  PRIMARY KEY (id),
  KEY idx_app (application_id),
  CONSTRAINT fk_fee_app FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Uploaded documents. Bytes live on disk under UPLOAD_DIR/applications/<ref_no>/<stored_name>;
-- this table holds the metadata. Served only through the admin download script.
CREATE TABLE IF NOT EXISTS files (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner_type      ENUM('application','donation','contact') NOT NULL,
  owner_id        INT UNSIGNED NOT NULL,
  kind            ENUM('photo','marksheet','certificate','fee_demand','income_cert','bonafide','application_pdf','screenshot') NOT NULL,
  original_name   VARCHAR(255) NOT NULL,
  stored_name     VARCHAR(255) NOT NULL,               -- random name, relative to the owner folder
  mime            VARCHAR(100) NOT NULL,
  size            INT UNSIGNED NOT NULL,
  uploaded_at     DATETIME     NOT NULL,
  PRIMARY KEY (id),
  KEY idx_owner (owner_type, owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Donations (donate.php). Each QR code carries a signed reference (UET-D-<date>-<random>, shown in the UPI note);
-- nothing is stored until the donor fills "After you pay", which creates a row here.
CREATE TABLE IF NOT EXISTS donations (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  intent_ref      VARCHAR(30)  NULL,                    -- QR reference (signed, from the page), when the donor paid via the QR
  name            VARCHAR(120) NOT NULL,
  phone           VARCHAR(20)  NOT NULL DEFAULT '',
  email           VARCHAR(190) NOT NULL DEFAULT '',
  pan             VARCHAR(10)  NOT NULL DEFAULT '',
  address         TEXT         NULL,
  amount          DECIMAL(10,2) NOT NULL,               -- amount the donor reports
  txn_ref         VARCHAR(60)  NOT NULL DEFAULT '',     -- UPI / UTR reference typed by the donor
  paid_on         DATE         NULL,
  mode            ENUM('upi','bank','cheque') NOT NULL DEFAULT 'upi',
  list_on_site    TINYINT(1)   NOT NULL DEFAULT 0,
  screenshot_file_id INT UNSIGNED NULL,
  status          ENUM('pending','verified','receipted','rejected') NOT NULL DEFAULT 'pending',
  receipt_no      VARCHAR(30)  NULL,                    -- 80G/2026-27/0001
  receipt_sent_at DATETIME     NULL,
  notes           TEXT         NULL,
  ip              VARCHAR(45)  NOT NULL DEFAULT '',
  created_at      DATETIME     NOT NULL,
  PRIMARY KEY (id),
  KEY idx_status (status),
  KEY idx_intent (intent_ref),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Messages from the contact form (contact.php → form_process.php). Mailed to TRUST_INBOX as well.
CREATE TABLE IF NOT EXISTS contact_messages (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name            VARCHAR(120) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  phone           VARCHAR(20)  NOT NULL DEFAULT '',
  subject         VARCHAR(60)  NOT NULL DEFAULT '',
  message         TEXT         NOT NULL,
  ip              VARCHAR(45)  NOT NULL DEFAULT '',
  created_at      DATETIME     NOT NULL,
  replied_at      DATETIME     NULL,
  PRIMARY KEY (id),
  KEY idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Outgoing mail record, so the trustees can see whether the applicant copy went out.
CREATE TABLE IF NOT EXISTS mail_log (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  owner_type      ENUM('application','donation','contact') NOT NULL,
  owner_id        INT UNSIGNED NOT NULL,
  recipient       VARCHAR(190) NOT NULL,
  subject         VARCHAR(255) NOT NULL,
  status          ENUM('sent','failed','logged') NOT NULL,
  error           TEXT         NULL,
  sent_at         DATETIME     NOT NULL,
  PRIMARY KEY (id),
  KEY idx_owner (owner_type, owner_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Simple per-IP rate limiting for public forms.
CREATE TABLE IF NOT EXISTS rate_limits (
  ip              VARCHAR(45)  NOT NULL,
  action          VARCHAR(40)  NOT NULL,               -- 'apply', 'contact', 'donation'
  window_start    DATETIME     NOT NULL,
  hits            INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (ip, action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
