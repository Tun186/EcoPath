<?php
require_once '../app/init.php';

$db = new Database();

$queries = [
    "ALTER TABLE Hotel ADD COLUMN CreatedBy VARCHAR(50)",
    "ALTER TABLE Hotel ADD COLUMN UpdatedBy VARCHAR(50)",
    "ALTER TABLE Hotel ADD COLUMN UpdatedAt DATETIME",
    "ALTER TABLE Landmarks ADD COLUMN CreatedBy VARCHAR(50)",
    "ALTER TABLE Landmarks ADD COLUMN UpdatedBy VARCHAR(50)",
    "ALTER TABLE Landmarks ADD COLUMN UpdatedAt DATETIME"
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
