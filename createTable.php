<?php
/**
 * createTable.php
 * -------------------------------------------------------
 * This script manages the tblUser table in ClothingStore.
 *
 * Each time this script runs it will:
 *   1. Include the shared database connection (DBConn.php)
 *   2. Check whether tblUser already exists
 *   3. Drop tblUser if it exists
 *   4. Recreate tblUser with the correct schema
 *   5. Read user data from userData.txt
 *   6. Insert every record from the text file into tblUser
 *
 * Author : Pastimes Dev Team
 * Date   : 2026
 * -------------------------------------------------------
 */

// ── Step 1: Include the shared database connection ────────
include 'DBConn.php';
// $conn is now available from DBConn.php
// ─────────────────────────────────────────────────────────

echo "<h2>createTable.php — tblUser Setup</h2>";

// ── Step 2 & 3: Drop tblUser if it already exists ────────
// We use IF EXISTS so MySQL won't throw an error when the
// table is absent on the very first run.
$dropSQL = "DROP TABLE IF EXISTS tblUser";

if ($conn->query($dropSQL) === TRUE) {
    echo "<p>✔ Existing <strong>tblUser</strong> dropped (or did not exist).</p>";
} else {
    die("<p style='color:red;'>Error dropping table: " . $conn->error . "</p>");
}
// ─────────────────────────────────────────────────────────

// ── Step 4: Recreate tblUser with the required schema ────
$createSQL = "
    CREATE TABLE tblUser (
        userID       INT            NOT NULL AUTO_INCREMENT,
        userName     VARCHAR(100)   NOT NULL,
        userEmail    VARCHAR(150)   NOT NULL UNIQUE,
        userPassword VARCHAR(255)   NOT NULL,   -- stores bcrypt hash
        isVerified   TINYINT(1)     NOT NULL DEFAULT 0,  -- 0 = pending, 1 = approved
        PRIMARY KEY (userID)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($conn->query($createSQL) === TRUE) {
    echo "<p>✔ <strong>tblUser</strong> created successfully.</p>";
} else {
    die("<p style='color:red;'>Error creating table: " . $conn->error . "</p>");
}
// ─────────────────────────────────────────────────────────

// ── Step 5: Read userData.txt ─────────────────────────────
// The text file contains one user per line in the format:
//   FirstName LastName email hashedPassword
// e.g.: John Doe j.doe@abc.co.za $2y$10$...
$txtFile = 'userData.txt';

// Verify the file exists before attempting to open it
if (!file_exists($txtFile)) {
    die("<p style='color:red;'>userData.txt not found in the same directory.</p>");
}

// Read all non-empty lines into an array
$lines = array_filter(
    file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)
);

echo "<p>✔ userData.txt loaded — " . count($lines) . " record(s) found.</p>";
// ─────────────────────────────────────────────────────────

// ── Step 6: Insert each record into tblUser ───────────────
// Use a prepared statement to prevent SQL injection.
$stmt = $conn->prepare(
    "INSERT INTO tblUser (userName, userEmail, userPassword, isVerified)
     VALUES (?, ?, ?, 1)"
    // isVerified = 1 for seed data so these accounts are active by default
);

if (!$stmt) {
    die("<p style='color:red;'>Prepare failed: " . $conn->error . "</p>");
}

// Bind parameters: s = string
$stmt->bind_param('sss', $name, $email, $password);

$insertCount = 0; // Track how many rows were inserted

foreach ($lines as $line) {
    // Each line: FirstName LastName email hashedPassword
    // Split on whitespace, limit to 4 parts so the hash is kept intact
    $parts = preg_split('/\s+/', trim($line), 4);

    if (count($parts) < 4) {
        // Skip malformed lines
        echo "<p style='color:orange;'>⚠ Skipped malformed line: " . htmlspecialchars($line) . "</p>";
        continue;
    }

    // Combine first + last name into one field
    $name     = $parts[0] . ' ' . $parts[1]; // e.g. "John Doe"
    $email    = $parts[2];                    // e.g. "j.doe@abc.co.za"
    $password = $parts[3];                    // bcrypt hash

    if ($stmt->execute()) {
        $insertCount++;
    } else {
        echo "<p style='color:red;'>Insert error for {$email}: " . $stmt->error . "</p>";
    }
}

// Clean up the prepared statement
$stmt->close();

echo "<p>✔ <strong>{$insertCount}</strong> user record(s) inserted into tblUser.</p>";
// ─────────────────────────────────────────────────────────

// ── Close the database connection ────────────────────────
$conn->close();
echo "<p style='color:green;'><strong>createTable.php completed successfully.</strong></p>";
// ─────────────────────────────────────────────────────────
?>
