<?php
require_once '../app/init.php';

$db = new Database();

$queries = [
    "ALTER TABLE Hotel ADD COLUMN IsActive BOOLEAN DEFAULT TRUE",
    "ALTER TABLE Landmarks ADD COLUMN IsActive BOOLEAN DEFAULT TRUE"
];

foreach ($queries as $query) {
    try {
        $db->query($query);
        $db->execute();
        echo "Success: " . substr($query, 0, 50) . "...\n";
    } catch (Exception $e) {
        echo "Error or Already Exists: " . $e->getMessage() . "\n";
    }
}
echo "Migration Complete.\n";
