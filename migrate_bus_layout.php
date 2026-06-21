<?php
// Define HTTP_HOST if not set to prevent notices when running via CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/migrate_bus_layout.php';
}

require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Add SeatLayout if not exists
    $q = $pdo->query("SHOW COLUMNS FROM bus LIKE 'SeatLayout'");
    if (!$q->fetch()) {
        $pdo->exec("ALTER TABLE bus ADD COLUMN SeatLayout VARCHAR(10) NOT NULL DEFAULT '2+2'");
        echo "Column SeatLayout added to bus table.<br>\n";
    } else {
        echo "Column SeatLayout already exists on bus table.<br>\n";
    }

    echo "Migration completed successfully.<br>\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}
