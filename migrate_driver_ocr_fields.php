<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    // Add columns if they do not exist
    $pdo->exec('ALTER TABLE Driver ADD COLUMN BloodType VARCHAR(10) NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN LicenseClass VARCHAR(50) NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN Address TEXT NULL');
    echo "Migration completed successfully: BloodType, LicenseClass, and Address columns added.\n";
} catch (PDOException $e) {
    echo "Error or already exists: " . $e->getMessage() . "\n";
}
