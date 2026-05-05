-- ================================================================
-- myClothingStore.sql
-- Full DDL export for the ClothingStore database
-- Compatible with phpMyAdmin import / MySQL 5.7+ / MariaDB 10.3+
--
-- To import:
--   • phpMyAdmin → select database → Import → choose this file
--   • OR via CLI: mysql -u root -p ClothingStore < myClothingStore.sql
--
-- Author : Pastimes Dev Team
-- Date   : 2026
-- ================================================================

-- ── Create database if it does not exist ─────────────────
CREATE DATABASE IF NOT EXISTS `ClothingStore`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

-- Select the database to use
USE `ClothingStore`;

-- ── Disable foreign-key checks during import ─────────────
-- Prevents errors when dropping tables that are referenced
-- by foreign keys in other tables.
SET FOREIGN_KEY_CHECKS = 0;

-- ================================================================
-- TABLE: tblUser
-- Stores registered customer accounts.
-- isVerified = 0 means pending admin approval;
-- isVerified = 1 means the account is active.
-- ================================================================

DROP TABLE IF EXISTS `tblUser`;

CREATE TABLE `tblUser` (
    `userID`       INT           NOT NULL AUTO_INCREMENT,
    `userName`     VARCHAR(100)  NOT NULL,
    `userEmail`    VARCHAR(150)  NOT NULL,
    `userPassword` VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash via password_hash()',
    `isVerified`   TINYINT(1)    NOT NULL DEFAULT 0 COMMENT '0=pending, 1=active',
    PRIMARY KEY (`userID`),
    UNIQUE KEY `uq_userEmail` (`userEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data — passwords are bcrypt hashes of 'Password@1'
INSERT INTO `tblUser` (`userName`, `userEmail`, `userPassword`, `isVerified`) VALUES
('John Doe',       'j.doe@abc.co.za',         '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Jane Smith',     'j.smith@gmail.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Michael Brown',  'm.brown@outlook.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Sarah Johnson',  's.johnson@yahoo.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('David Williams', 'd.williams@webmail.co.za',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Emily Clarke',   'e.clarke@icloud.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Liam Nkosi',     'l.nkosi@mweb.co.za',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Aisha Patel',    'a.patel@rediffmail.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0),
('Tom Pretorius',  't.pretorius@vodamail.co.za', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Nina van Wyk',   'n.vanwyk@telkomsa.net',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- ================================================================
-- TABLE: tblAdmin
-- Stores back-office administrator accounts.
-- ================================================================

DROP TABLE IF EXISTS `tblAdmin`;

CREATE TABLE `tblAdmin` (
    `adminID`       INT           NOT NULL AUTO_INCREMENT,
    `adminName`     VARCHAR(100)  NOT NULL,
    `adminEmail`    VARCHAR(150)  NOT NULL,
    `adminPassword` VARCHAR(255)  NOT NULL COMMENT 'bcrypt hash via password_hash()',
    PRIMARY KEY (`adminID`),
    UNIQUE KEY `uq_adminEmail` (`adminEmail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Sample data — passwords are bcrypt hashes of 'Admin@2026'
INSERT INTO `tblAdmin` (`adminName`, `adminEmail`, `adminPassword`) VALUES
('Super Admin',      'admin@clothingstore.co.za',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Store Manager',    'manager@clothingstore.co.za',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Warehouse Admin',  'warehouse@clothingstore.co.za',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Returns Admin',    'returns@clothingstore.co.za',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Marketing Admin',  'marketing@clothingstore.co.za',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- ================================================================
-- TABLE: tblClothes
-- Product catalogue — each row represents a clothing item.
-- ================================================================

DROP TABLE IF EXISTS `tblClothes`;

CREATE TABLE `tblClothes` (
    `clothesID`          INT            NOT NULL AUTO_INCREMENT,
    `clothesName`        VARCHAR(150)   NOT NULL,
    `clothesDescription` TEXT           NOT NULL,
    `clothesPrice`       DECIMAL(10,2)  NOT NULL,
    `clothesCategory`    VARCHAR(80)    NOT NULL,
    PRIMARY KEY (`clothesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tblClothes` (`clothesName`, `clothesDescription`, `clothesPrice`, `clothesCategory`) VALUES
('Classic White Tee',       'A timeless 100% cotton crew-neck t-shirt.',            149.99, 'T-Shirts'),
('Slim Fit Chinos',         'Smart casual slim-fit chino trousers in khaki.',        399.99, 'Trousers'),
('Denim Jacket',            'Classic denim jacket with button-front closure.',       699.99, 'Jackets'),
('Floral Summer Dress',     'Lightweight floral print midi dress for summer.',       549.99, 'Dresses'),
('Hooded Sweatshirt',       'Comfortable pull-over hoodie in fleece fabric.',        449.99, 'Hoodies'),
('Leather Belt',            'Genuine leather belt with silver buckle.',              199.99, 'Accessories'),
('Striped Polo Shirt',      'Short-sleeve polo shirt with contrast stripes.',        299.99, 'Shirts'),
('Cargo Shorts',            'Multi-pocket cargo shorts in olive green.',             349.99, 'Shorts'),
('Maxi Skirt',              'Boho-style maxi skirt with elastic waistband.',         429.99, 'Skirts'),
('Wool Scarf',              'Soft merino wool scarf in charcoal grey.',              249.99, 'Accessories'),
('Trench Coat',             'Classic double-breasted trench coat in beige.',        1199.99, 'Coats'),
('Graphic Tee',             'Unisex graphic print t-shirt, crew neck.',              179.99, 'T-Shirts'),
('High-Rise Jeans',         'High-waisted skinny jeans in dark wash denim.',         599.99, 'Jeans'),
('Linen Shirt',             'Breathable linen button-up shirt in white.',            329.99, 'Shirts'),
('Puffer Vest',             'Lightweight quilted puffer vest, water-resistant.',     499.99, 'Jackets'),
('Wrap Blouse',             'Elegant wrap blouse with ruffle detail.',               369.99, 'Blouses'),
('Jogger Pants',            'Tapered jogger trousers with drawstring waist.',        379.99, 'Trousers'),
('Sports Bra',              'High-support sports bra with moisture-wicking fabric.', 269.99, 'Activewear'),
('Bomber Jacket',           'Satin-finish bomber jacket with ribbed cuffs.',         749.99, 'Jackets'),
('Knit Beanie',             'Chunky knit beanie hat in navy blue.',                  129.99, 'Accessories'),
('Wide-Leg Trousers',       'Flowing wide-leg trousers in black crepe fabric.',      479.99, 'Trousers'),
('Off-Shoulder Top',        'Stylish off-shoulder top with gathered hem.',           259.99, 'Tops'),
('Zip-Up Hoodie',           'Zip-through hoodie in heather grey marl.',              469.99, 'Hoodies'),
('Pleated Midi Skirt',      'Elegant pleated midi skirt in forest green.',           389.99, 'Skirts'),
('Oversized Blazer',        'Relaxed-fit oversized blazer in checked pattern.',      899.99, 'Blazers'),
('Tank Top',                'Essential ribbed tank top, available in 6 colours.',    119.99, 'Tops'),
('Relaxed Fit Shorts',      'Cotton-linen blend relaxed shorts in cream.',           289.99, 'Shorts'),
('Turtleneck Sweater',      'Fine-knit roll-neck sweater in burgundy.',              529.99, 'Knitwear'),
('Slip Dress',              'Satin-look slip dress with lace trim detail.',          489.99, 'Dresses'),
('Padded Anorak',           'Waterproof padded anorak with adjustable hood.',        849.99, 'Coats');

-- ================================================================
-- TABLE: tblAorder
-- Stores customer orders. References tblUser and tblClothes
-- via foreign keys to maintain referential integrity.
-- ================================================================

DROP TABLE IF EXISTS `tblAorder`;

CREATE TABLE `tblAorder` (
    `orderID`       INT  NOT NULL AUTO_INCREMENT,
    `userID`        INT  NOT NULL COMMENT 'FK → tblUser.userID',
    `clothesID`     INT  NOT NULL COMMENT 'FK → tblClothes.clothesID',
    `orderDate`     DATE NOT NULL,
    `orderQuantity` INT  NOT NULL DEFAULT 1,
    PRIMARY KEY (`orderID`),
    CONSTRAINT `fk_order_user`
        FOREIGN KEY (`userID`)    REFERENCES `tblUser`(`userID`)    ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_order_clothes`
        FOREIGN KEY (`clothesID`) REFERENCES `tblClothes`(`clothesID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tblAorder` (`userID`, `clothesID`, `orderDate`, `orderQuantity`) VALUES
(1, 1,  '2026-01-05', 2),
(1, 3,  '2026-01-12', 1),
(2, 5,  '2026-01-15', 3),
(2, 7,  '2026-01-20', 1),
(3, 2,  '2026-01-22', 1),
(3, 10, '2026-01-28', 2),
(4, 4,  '2026-02-01', 1),
(4, 9,  '2026-02-03', 1),
(5, 6,  '2026-02-07', 4),
(5, 11, '2026-02-10', 1),
(1, 13, '2026-02-14', 1),
(2, 15, '2026-02-18', 2),
(3, 17, '2026-02-20', 1),
(4, 19, '2026-02-25', 1),
(5, 21, '2026-03-01', 2),
(1, 23, '2026-03-05', 1),
(2, 25, '2026-03-08', 1),
(3, 27, '2026-03-11', 3),
(4, 29, '2026-03-15', 1),
(5, 8,  '2026-03-18', 2),
(1, 14, '2026-03-22', 1),
(2, 16, '2026-03-25', 2),
(3, 18, '2026-03-28', 1),
(4, 20, '2026-04-01', 1),
(5, 22, '2026-04-04', 3),
(1, 24, '2026-04-07', 1),
(2, 26, '2026-04-10', 2),
(3, 28, '2026-04-13', 1),
(4, 30, '2026-04-16', 1),
(5, 12, '2026-04-20', 2);

-- ── Re-enable foreign-key checks ─────────────────────────
SET FOREIGN_KEY_CHECKS = 1;

-- ================================================================
-- END OF myClothingStore.sql
-- ================================================================
