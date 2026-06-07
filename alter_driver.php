<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec('ALTER TABLE Driver ADD COLUMN DateOfBirth DATE NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN NRC VARCHAR(100) NULL');
    $pdo->exec('ALTER TABLE Driver ADD COLUMN ProfileImage VARCHAR(255) NULL');
    echo "Columns added successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
