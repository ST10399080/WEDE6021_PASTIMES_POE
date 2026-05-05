<?php
/**
 * loadClothingStore.php
 * -------------------------------------------------------
 * Full database initialisation script for ClothingStore.
 *
 * Each time this script runs it will:
 *   1. Include the shared database connection (DBConn.php)
 *   2. Disable foreign-key checks so tables can be dropped
 *      in any order without constraint errors
 *   3. Drop ALL four tables if they exist
 *   4. Re-enable foreign-key checks
 *   5. Recreate all four tables with correct schema &
 *      relationships
 *   6. Seed each table with sample data
 *
 * Tables created:
 *   - tblUser      (customers / registered users)
 *   - tblAdmin     (back-office administrators)
 *   - tblClothes   (product catalogue)
 *   - tblAorder    (orders placed by users)
 *
 * Author : Pastimes Dev Team
 * Date   : 2026
 * -------------------------------------------------------
 */

// ── Step 1: Include the shared database connection ────────
include 'DBConn.php';
// $conn is now available from DBConn.php
// ─────────────────────────────────────────────────────────

echo "<h2>loadClothingStore.php — Full Database Initialisation</h2>";

// ── Helper function ───────────────────────────────────────
/**
 * runSQL()
 * Executes a single SQL statement and echoes the result.
 *
 * @param mysqli $conn  Active MySQLi connection
 * @param string $sql   SQL statement to execute
 * @param string $label Human-readable label for feedback
 */
function runSQL(mysqli $conn, string $sql, string $label): void
{
    if ($conn->query($sql) === TRUE) {
        echo "<p>✔ {$label}</p>";
    } else {
        die("<p style='color:red;'>✘ {$label} — Error: " . $conn->error . "</p>");
    }
}
// ─────────────────────────────────────────────────────────

// ── Step 2: Disable foreign-key checks ───────────────────
// This allows us to drop tables that are referenced by
// foreign keys without encountering constraint errors.
runSQL($conn, "SET FOREIGN_KEY_CHECKS = 0", "Foreign-key checks disabled");
// ─────────────────────────────────────────────────────────

// ── Step 3: Drop all tables if they exist ────────────────
runSQL($conn, "DROP TABLE IF EXISTS tblAorder",  "tblAorder dropped (or did not exist)");
runSQL($conn, "DROP TABLE IF EXISTS tblClothes", "tblClothes dropped (or did not exist)");
runSQL($conn, "DROP TABLE IF EXISTS tblUser",    "tblUser dropped (or did not exist)");
runSQL($conn, "DROP TABLE IF EXISTS tblAdmin",   "tblAdmin dropped (or did not exist)");
// ─────────────────────────────────────────────────────────

// ── Step 4: Re-enable foreign-key checks ─────────────────
runSQL($conn, "SET FOREIGN_KEY_CHECKS = 1", "Foreign-key checks re-enabled");
// ─────────────────────────────────────────────────────────

// ── Step 5: Create tables ─────────────────────────────────

// --- tblUser ---
// Stores all registered customers. isVerified must be set
// to 1 by an admin before the user can log in.
runSQL($conn, "
    CREATE TABLE IF NOT EXISTS tblUser (
        userID       INT           NOT NULL AUTO_INCREMENT,
        userName     VARCHAR(100)  NOT NULL,
        userEmail    VARCHAR(150)  NOT NULL UNIQUE,
        userPassword VARCHAR(255)  NOT NULL,
        isVerified   TINYINT(1)    NOT NULL DEFAULT 0,
        PRIMARY KEY (userID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "tblUser created");

// --- tblAdmin ---
// Stores administrator accounts for back-office access.
runSQL($conn, "
    CREATE TABLE IF NOT EXISTS tblAdmin (
        adminID       INT           NOT NULL AUTO_INCREMENT,
        adminName     VARCHAR(100)  NOT NULL,
        adminEmail    VARCHAR(150)  NOT NULL UNIQUE,
        adminPassword VARCHAR(255)  NOT NULL,
        PRIMARY KEY (adminID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "tblAdmin created");

// --- tblClothes ---
// The product catalogue. Each row is a clothing item.
runSQL($conn, "
    CREATE TABLE IF NOT EXISTS tblClothes (
        clothesID          INT            NOT NULL AUTO_INCREMENT,
        clothesName        VARCHAR(150)   NOT NULL,
        clothesDescription TEXT           NOT NULL,
        clothesPrice       DECIMAL(10,2)  NOT NULL,
        clothesCategory    VARCHAR(80)    NOT NULL,
        PRIMARY KEY (clothesID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "tblClothes created");

// --- tblAorder ---
// Records every order. References tblUser and tblClothes
// via foreign keys to maintain referential integrity.
runSQL($conn, "
    CREATE TABLE IF NOT EXISTS tblAorder (
        orderID       INT  NOT NULL AUTO_INCREMENT,
        userID        INT  NOT NULL,
        clothesID     INT  NOT NULL,
        orderDate     DATE NOT NULL,
        orderQuantity INT  NOT NULL DEFAULT 1,
        PRIMARY KEY (orderID),
        CONSTRAINT fk_order_user
            FOREIGN KEY (userID)    REFERENCES tblUser(userID)    ON DELETE CASCADE,
        CONSTRAINT fk_order_clothes
            FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", "tblAorder created");
// ─────────────────────────────────────────────────────────

// ── Step 6: Seed tables with sample data ─────────────────

// --- Seed tblUser (passwords hashed with password_hash) ---
// NOTE: In production you would collect these at registration.
//       The hashes below correspond to 'Password@1' for all seed users.
$userPassword = password_hash('Password@1', PASSWORD_DEFAULT);

runSQL($conn, "
    INSERT INTO tblUser (userName, userEmail, userPassword, isVerified) VALUES
    ('John Doe',        'j.doe@abc.co.za',           '{$userPassword}', 1),
    ('Jane Smith',      'j.smith@gmail.com',          '{$userPassword}', 1),
    ('Michael Brown',   'm.brown@outlook.com',         '{$userPassword}', 1),
    ('Sarah Johnson',   's.johnson@yahoo.com',         '{$userPassword}', 0),
    ('David Williams',  'd.williams@webmail.co.za',    '{$userPassword}', 1)
", "tblUser seeded with 5 users");

// --- Seed tblAdmin ---
$adminPassword = password_hash('Admin@2026', PASSWORD_DEFAULT);

runSQL($conn, "
    INSERT INTO tblAdmin (adminName, adminEmail, adminPassword) VALUES
    ('Super Admin',   'admin@clothingstore.co.za',  '{$adminPassword}'),
    ('Store Manager', 'manager@clothingstore.co.za', '{$adminPassword}')
", "tblAdmin seeded with 2 admins");

// --- Seed tblClothes ---
runSQL($conn, "
    INSERT INTO tblClothes (clothesName, clothesDescription, clothesPrice, clothesCategory) VALUES
    ('Classic White Tee',       'A timeless 100% cotton crew-neck t-shirt.',          149.99, 'T-Shirts'),
    ('Slim Fit Chinos',         'Smart casual slim-fit chino trousers in khaki.',      399.99, 'Trousers'),
    ('Denim Jacket',            'Classic denim jacket with button-front closure.',     699.99, 'Jackets'),
    ('Floral Summer Dress',     'Lightweight floral print midi dress for summer.',     549.99, 'Dresses'),
    ('Hooded Sweatshirt',       'Comfortable pull-over hoodie in fleece fabric.',      449.99, 'Hoodies'),
    ('Leather Belt',            'Genuine leather belt with silver buckle.',            199.99, 'Accessories'),
    ('Striped Polo Shirt',      'Short-sleeve polo shirt with contrast stripes.',      299.99, 'Shirts'),
    ('Cargo Shorts',            'Multi-pocket cargo shorts in olive green.',           349.99, 'Shorts'),
    ('Maxi Skirt',              'Boho-style maxi skirt with elastic waistband.',       429.99, 'Skirts'),
    ('Wool Scarf',              'Soft merino wool scarf in charcoal grey.',            249.99, 'Accessories'),
    ('Trench Coat',             'Classic double-breasted trench coat in beige.',      1199.99, 'Coats'),
    ('Graphic Tee',             'Unisex graphic print t-shirt, crew neck.',            179.99, 'T-Shirts'),
    ('High-Rise Jeans',         'High-waisted skinny jeans in dark wash denim.',       599.99, 'Jeans'),
    ('Linen Shirt',             'Breathable linen button-up shirt in white.',          329.99, 'Shirts'),
    ('Puffer Vest',             'Lightweight quilted puffer vest, water-resistant.',   499.99, 'Jackets'),
    ('Wrap Blouse',             'Elegant wrap blouse with ruffle detail.',             369.99, 'Blouses'),
    ('Jogger Pants',            'Tapered jogger trousers with drawstring waist.',      379.99, 'Trousers'),
    ('Sports Bra',              'High-support sports bra with moisture-wicking.',      269.99, 'Activewear'),
    ('Bomber Jacket',           'Satin-finish bomber jacket with ribbed cuffs.',       749.99, 'Jackets'),
    ('Knit Beanie',             'Chunky knit beanie hat in navy blue.',                129.99, 'Accessories'),
    ('Wide-Leg Trousers',       'Flowing wide-leg trousers in black crepe.',           479.99, 'Trousers'),
    ('Off-Shoulder Top',        'Stylish off-shoulder top with gathered hem.',         259.99, 'Tops'),
    ('Zip-Up Hoodie',           'Zip-through hoodie in heather grey marl.',            469.99, 'Hoodies'),
    ('Pleated Midi Skirt',      'Elegant pleated midi skirt in forest green.',         389.99, 'Skirts'),
    ('Oversized Blazer',        'Relaxed-fit oversized blazer in checked pattern.',    899.99, 'Blazers'),
    ('Tank Top',                'Essential ribbed tank top, available in 6 colours.',  119.99, 'Tops'),
    ('Relaxed Fit Shorts',      'Cotton-linen blend relaxed shorts in cream.',         289.99, 'Shorts'),
    ('Turtleneck Sweater',      'Fine-knit roll-neck sweater in burgundy.',            529.99, 'Knitwear'),
    ('Slip Dress',              'Satin-look slip dress with lace trim detail.',        489.99, 'Dresses'),
    ('Padded Anorak',           'Waterproof padded anorak with adjustable hood.',      849.99, 'Coats')
", "tblClothes seeded with 30 products");

// --- Seed tblAorder ---
runSQL($conn, "
    INSERT INTO tblAorder (userID, clothesID, orderDate, orderQuantity) VALUES
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
    (5, 12, '2026-04-20', 2)
", "tblAorder seeded with 30 orders");
// ─────────────────────────────────────────────────────────

// ── Close the database connection ────────────────────────
$conn->close();
echo "<hr><p style='color:green;'><strong>✔ loadClothingStore.php completed — all tables created and seeded.</strong></p>";
// ─────────────────────────────────────────────────────────
?>
