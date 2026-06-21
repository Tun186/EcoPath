<?php
// Define HTTP_HOST if not set to prevent notices when running via CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/migrate_bus_details.php';
}

require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Add BusCompany if not exists
    $q = $pdo->query("SHOW COLUMNS FROM bus LIKE 'BusCompany'");
    if (!$q->fetch()) {
        $pdo->exec("ALTER TABLE bus ADD COLUMN BusCompany VARCHAR(100) NULL");
        echo "Column BusCompany added to bus table.<br>\n";
    } else {
        echo "Column BusCompany already exists on bus table.<br>\n";
    }

    // 2. Add FuelType if not exists
    $q = $pdo->query("SHOW COLUMNS FROM bus LIKE 'FuelType'");
    if (!$q->fetch()) {
        $pdo->exec("ALTER TABLE bus ADD COLUMN FuelType VARCHAR(50) NULL");
        echo "Column FuelType added to bus table.<br>\n";
    } else {
        echo "Column FuelType already exists on bus table.<br>\n";
    }

    echo "Migration completed successfully.<br>\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}
