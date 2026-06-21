<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec('ALTER TABLE Driver ADD COLUMN LicenseFrontImage VARCHAR(255) NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN LicenseBackImage VARCHAR(255) NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN LicenseExpDate DATE NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN LicenseIssueYear VARCHAR(10) NULL');
    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
