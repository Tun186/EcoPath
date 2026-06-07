<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec('ALTER TABLE User ADD COLUMN ResetToken VARCHAR(255) NULL');
    $pdo->exec('ALTER TABLE User ADD COLUMN ResetExpiry DATETIME NULL');
    echo "Columns added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
