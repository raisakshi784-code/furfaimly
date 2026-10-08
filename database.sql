-- FurFaimily Production Schema
CREATE TABLE IF NOT EXISTS `users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `google_id` VARCHAR(255) UNIQUE DEFAULT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `avatar` VARCHAR(500) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS `pets` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `breed` VARCHAR(100) DEFAULT 'Mixed Breed',
  `age` VARCHAR(50) DEFAULT '1 Year',
  `status` VARCHAR(50) DEFAULT 'available',
  `trait_tag` VARCHAR(50) DEFAULT 'family',
  `vaccinated` INTEGER DEFAULT 1,
  `neutered` INTEGER DEFAULT 1,
  `dewormed` INTEGER DEFAULT 1,
  `city` VARCHAR(100) DEFAULT 'Delhi NCR',
  `image` VARCHAR(255) DEFAULT 'Img/Img/persian.jpeg',
  `description` TEXT DEFAULT 'Affectionate, playful, and looking for a loving home.'
);

CREATE TABLE IF NOT EXISTS `applications` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER DEFAULT NULL,
  `pet_id` INTEGER NOT NULL,
  `applicant_name` VARCHAR(255) NOT NULL,
  `applicant_email` VARCHAR(255) NOT NULL,
  `applicant_phone` VARCHAR(50) NOT NULL,
  `applicant_city` VARCHAR(100) NOT NULL,
  `residence_type` VARCHAR(100) NOT NULL,
  `has_experience` VARCHAR(20) NOT NULL,
  `other_pets` VARCHAR(50) NOT NULL,
  `reason` TEXT,
  `status` VARCHAR(50) DEFAULT 'Under Review',
  `applied_at DATETIME DEFAULT CURRENT_TIMESTAMP`
);

CREATE TABLE IF NOT EXISTS `lost_found` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pet_type` VARCHAR(50) NOT NULL,
  `pet_name` VARCHAR(100) NOT NULL,
  `status` VARCHAR(20) NOT NULL,
  `city` VARCHAR(100) NOT NULL,
  `last_seen` VARCHAR(255) NOT NULL,
  `contact_phone` VARCHAR(50) NOT NULL,
  `reward` VARCHAR(100) DEFAULT '',
  `image` VARCHAR(255) DEFAULT 'Img/Img/DOG!.webp',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS `donations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `donor_name` VARCHAR(255) NOT NULL,
  `donor_email` VARCHAR(255) NOT NULL,
  `amount` REAL NOT NULL,
  `pet_id` INTEGER DEFAULT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'UPI',
  `receipt_no` VARCHAR(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
);
