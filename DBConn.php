<?php
/**
 * DBConn.php
 * -------------------------------------------------------
 * Database connection file for the ClothingStore application.
 * Uses MySQLi (improved) for a secure, object-oriented
 * connection to the MySQL database.
 *
 * Include this file in any script that requires DB access:
 *   include 'DBConn.php';
 *
 * Author : Pastimes Dev Team
 * Date   : 2026
 * -------------------------------------------------------
 */

// ── Database credentials ──────────────────────────────────
define('DB_HOST',     'localhost');   // MySQL server host
define('DB_USER',     'root');        // MySQL username (change for production)
define('DB_PASS',     '');            // MySQL password  (change for production)
define('DB_NAME',     'ClothingStore'); // Target database name
// ─────────────────────────────────────────────────────────

/**
 * Create a new MySQLi connection using the credentials above.
 * The connection is stored in $conn and available to any
 * script that includes this file.
 */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// ── Error handling ────────────────────────────────────────
// If the connection fails, terminate the script immediately
// and display a descriptive error message.
if ($conn->connect_error) {
    die(
        "<p style='color:red;font-family:monospace;'>"
        . "<strong>Database connection failed:</strong> "
        . htmlspecialchars($conn->connect_error)
        . "</p>"
    );
}

// Set the character set to UTF-8 for full Unicode support
$conn->set_charset('utf8mb4');

// ── Optional success feedback (comment out in production) ─
// echo "<p style='color:green;'>Connected to <strong>" . DB_NAME . "</strong> successfully.</p>";
// ─────────────────────────────────────────────────────────
?>
