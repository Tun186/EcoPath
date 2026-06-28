<?php
// Define HTTP_HOST if not set to prevent notices when running via CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/migrate_pickup_images.php';
}

require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Add Image1, Image2, Image3 columns to bus table if they don't exist
    $cols = ['Image1', 'Image2', 'Image3'];
    foreach ($cols as $col) {
        $q = $pdo->query("SHOW COLUMNS FROM bus LIKE '$col'");
        if (!$q->fetch()) {
            $pdo->exec("ALTER TABLE bus ADD COLUMN $col VARCHAR(255) NULL");
            echo "Column $col added to bus table.<br>\n";
        } else {
            echo "Column $col already exists on bus table.<br>\n";
        }
    }

    // 2. Create pickup_points table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS pickup_points (
        PickupPointID VARCHAR(50) PRIMARY KEY,
        PackageID VARCHAR(50) NOT NULL,
        LocationName VARCHAR(255) NOT NULL,
        FOREIGN KEY (PackageID) REFERENCES package(PackageID) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "Table pickup_points checked/created successfully.<br>\n";

    echo "Migration completed successfully.<br>\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}
