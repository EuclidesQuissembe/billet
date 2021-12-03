CREATE DATABASE IF NOT EXISTS billet DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE billet;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR (20) NOT NULL,
    `last_name` VARCHAR (20) NOT NULL,
    `email` VARCHAR (255) NOT NULL UNIQUE,
    `password_hash` VARCHAR (255) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `name` VARCHAR (40) NOT NULL,
    `document_path` VARCHAR (255) NULL,
    `email` VARCHAR (255) NOT NULL UNIQUE,
    `cell_phone` VARCHAR (20) NOT NULL,
    `day` INT NOT NULL,
    `month` INT NOT NULL,
    `year` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT UNSIGNED NOT NULL,
    `payer_id` INT UNSIGNED NOT NULL,
    `due_date` DATE NOT NULL,
    `price` DOUBLE NOT NULL,
    `description` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    FOREIGN KEY (`payer_id`) REFERENCES `payers` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets_instructions` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `billet_id` INT UNSIGNED NOT NULL,
    `instruction` TEXT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`billet_id`) REFERENCES `billets` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets_tickets_fine` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `billet_id` INT UNSIGNED NOT NULL,
    `type` INT NOT NULL,
    `value` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`billet_id`) REFERENCES `billets` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets_fees` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `billet_id` INT UNSIGNED NOT NULL,
    `type` INT NOT NULL,
    `value` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`billet_id`) REFERENCES `billets` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets_discounts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `billet_id` INT UNSIGNED NOT NULL,
    `type` INT NOT NULL,
    `value` INT NOT NULL,
    `deadline_date` DATE NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`billet_id`) REFERENCES `billets` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `billets_references` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `billet_id` INT UNSIGNED NOT NULL,
    `reference` VARCHAR (50) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`billet_id`) REFERENCES `billets` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payers_addresses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payer_id` INT UNSIGNED NOT NULL,
    `cep` VARCHAR (6) NOT NULL,
    `street` VARCHAR (20) NOT NULL,
    `district` VARCHAR (20) NOT NULL,
    `city` VARCHAR (30) NOT NULL,
    `state` VARCHAR (30) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`payer_id`) REFERENCES `payers` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payers_numbers` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payer_id` INT UNSIGNED NOT NULL,
    `number` INT NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`payer_id`) REFERENCES `payers` (`id`)
)ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `payers_complements` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `payer_id` INT UNSIGNED NOT NULL,
    `complement` VARCHAR (30) NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`payer_id`) REFERENCES `payers` (`id`)
)ENGINE=InnoDB;