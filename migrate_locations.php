<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $pdo->exec('ALTER TABLE Region ADD COLUMN CreatedBy VARCHAR(50) NULL, ADD COLUMN CreatedAt DATETIME NULL, ADD COLUMN UpdatedBy VARCHAR(50) NULL, ADD COLUMN UpdatedAt DATETIME NULL');
    $pdo->exec('ALTER TABLE City ADD COLUMN CreatedBy VARCHAR(50) NULL, ADD COLUMN CreatedAt DATETIME NULL, ADD COLUMN UpdatedBy VARCHAR(50) NULL, ADD COLUMN UpdatedAt DATETIME NULL');
    
    // Add foreign keys
    $pdo->exec('ALTER TABLE Region ADD FOREIGN KEY (CreatedBy) REFERENCES user(UserID), ADD FOREIGN KEY (UpdatedBy) REFERENCES user(UserID)');
    $pdo->exec('ALTER TABLE City ADD FOREIGN KEY (CreatedBy) REFERENCES user(UserID), ADD FOREIGN KEY (UpdatedBy) REFERENCES user(UserID)');
    
    echo "Migration completed successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
