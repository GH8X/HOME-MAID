-- ============================================================================
-- Maid4Condos — MySQL schema
--
-- Import with:   mysql -u USER -p DATABASE < database/schema.sql
-- Or run the one-time installer at  /database/install.php
--
-- The public website renders from includes/seed.php whenever a table is empty,
-- so the site is never blank before you import this file.
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+00:00';

-- ---------------------------------------------------------------------------
-- Administrators (completely separate from any public account system)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`           VARCHAR(120)  NOT NULL,
  `email`          VARCHAR(190)  NOT NULL,
  `password_hash`  VARCHAR(255)  NOT NULL,
  `role`           ENUM('owner','editor') NOT NULL DEFAULT 'editor',
  `is_active`      TINYINT(1)    NOT NULL DEFAULT 1,
  `last_login_at`  DATETIME      DEFAULT NULL,
  `created_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admins_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Site settings (key/value, editable in Admin → Settings)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `setting_key`    VARCHAR(120)  NOT NULL,
  `setting_value`  TEXT          NULL,
  `setting_group`  VARCHAR(60)   NOT NULL DEFAULT 'general',
  `setting_type`   VARCHAR(20)   NOT NULL DEFAULT 'text',
  `label`          VARCHAR(190)  NOT NULL DEFAULT '',
  `updated_at`     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`setting_key`),
  KEY `settings_group_idx` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Cleaning packages
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`             VARCHAR(80)   NOT NULL,
  `name`             VARCHAR(120)  NOT NULL,
  `eyebrow`          VARCHAR(120)  NOT NULL DEFAULT '',
  `tagline`          VARCHAR(190)  NOT NULL DEFAULT '',
  `summary`          TEXT          NULL,
  `intro_json`       LONGTEXT      NULL,
  `price_from`       DECIMAL(10,2) DEFAULT NULL,
  `image`            VARCHAR(190)  NOT NULL DEFAULT '',
  `hero_image`       VARCHAR(190)  NOT NULL DEFAULT '',
  `duration_note`    VARCHAR(190)  NOT NULL DEFAULT '',
  `best_paired`      VARCHAR(190)  NOT NULL DEFAULT '',
  `meta_title`       VARCHAR(190)  NOT NULL DEFAULT '',
  `meta_description` VARCHAR(320)  NOT NULL DEFAULT '',
  `who_for_json`     LONGTEXT      NULL,
  `checklist_json`   LONGTEXT      NULL,
  `benefits_json`    LONGTEXT      NULL,
  `faqs_json`        LONGTEXT      NULL,
  `sort_order`       INT           NOT NULL DEFAULT 0,
  `is_published`     TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `services_slug_unique` (`slug`),
  KEY `services_published_idx` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Frequently asked questions
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category`     VARCHAR(120) NOT NULL DEFAULT 'General',
  `question`     VARCHAR(320) NOT NULL,
  `answer`       TEXT         NOT NULL,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `faqs_published_idx` (`is_published`, `sort_order`),
  KEY `faqs_category_idx` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Client testimonials
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120) NOT NULL,
  `location`     VARCHAR(120) NOT NULL DEFAULT '',
  `service`      VARCHAR(120) NOT NULL DEFAULT '',
  `quote`        TEXT         NOT NULL,
  `rating`       TINYINT      DEFAULT NULL,
  `source`       VARCHAR(120) NOT NULL DEFAULT '',
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `testimonials_published_idx` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Service areas (neighbourhoods)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `service_areas` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(120) NOT NULL,
  `note`         VARCHAR(255) NOT NULL DEFAULT '',
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `areas_published_idx` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Add-on services
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `extras` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(80)  NOT NULL,
  `name`         VARCHAR(120) NOT NULL,
  `summary`      TEXT         NULL,
  `details`      TEXT         NULL,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `extras_slug_unique` (`slug`),
  KEY `extras_published_idx` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Cleaning frequencies (AutoPilot schedules)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `frequencies` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug`         VARCHAR(40)  NOT NULL,
  `label`        VARCHAR(80)  NOT NULL,
  `discount`     VARCHAR(40)  NOT NULL DEFAULT '',
  `note`         VARCHAR(190) NOT NULL DEFAULT '',
  `recommended`  TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `frequencies_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Editable homepage / about content blocks (stored as JSON)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `content_blocks` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `block_key`    VARCHAR(80)  NOT NULL,
  `block_json`   LONGTEXT     NULL,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blocks_key_unique` (`block_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Primary navigation
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `navigation` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label`        VARCHAR(80)  NOT NULL,
  `route`        VARCHAR(120) NOT NULL,
  `sort_order`   INT          NOT NULL DEFAULT 0,
  `is_published` TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `nav_published_idx` (`is_published`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Quote requests
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`            VARCHAR(120) NOT NULL,
  `email`           VARCHAR(190) NOT NULL,
  `phone`           VARCHAR(40)  NOT NULL DEFAULT '',
  `neighbourhood`   VARCHAR(120) NOT NULL DEFAULT '',
  `postal_code`     VARCHAR(12)  NOT NULL DEFAULT '',
  `property_type`   VARCHAR(40)  NOT NULL DEFAULT '',
  `bedrooms`        VARCHAR(10)  NOT NULL DEFAULT '',
  `bathrooms`       VARCHAR(10)  NOT NULL DEFAULT '',
  `sqft`            INT          NOT NULL DEFAULT 0,
  `service_slug`    VARCHAR(80)  NOT NULL DEFAULT '',
  `service_name`    VARCHAR(120) NOT NULL DEFAULT '',
  `frequency_slug`  VARCHAR(40)  NOT NULL DEFAULT '',
  `frequency_label` VARCHAR(80)  NOT NULL DEFAULT '',
  `extras_json`     VARCHAR(500) NOT NULL DEFAULT '[]',
  `preferred_date`  DATE         DEFAULT NULL,
  `preferred_time`  VARCHAR(40)  NOT NULL DEFAULT '',
  `access_method`   VARCHAR(40)  NOT NULL DEFAULT '',
  `message`         TEXT         NULL,
  `status`          ENUM('new','contacted','quoted','booked','closed','spam') NOT NULL DEFAULT 'new',
  `admin_note`      TEXT         NULL,
  `source_page`     VARCHAR(120) NOT NULL DEFAULT '',
  `ip_hash`         CHAR(64)     NOT NULL DEFAULT '',
  `user_agent`      VARCHAR(255) NOT NULL DEFAULT '',
  `created_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `inquiries_status_idx` (`status`, `created_at`),
  KEY `inquiries_email_idx` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Contact form messages
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(190) NOT NULL,
  `phone`      VARCHAR(40)  NOT NULL DEFAULT '',
  `subject`    VARCHAR(190) NOT NULL DEFAULT '',
  `message`    TEXT         NOT NULL,
  `status`     ENUM('new','read','replied','closed','spam') NOT NULL DEFAULT 'new',
  `admin_note` TEXT         NULL,
  `ip_hash`    CHAR(64)     NOT NULL DEFAULT '',
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `messages_status_idx` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
