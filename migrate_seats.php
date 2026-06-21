<?php
// Define HTTP_HOST if not set to prevent notices when running via CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/migrate_seats.php';
}

require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Alter Bus table to add TotalSeats if not exists
    $q = $pdo->query("SHOW COLUMNS FROM bus LIKE 'TotalSeats'");
    if (!$q->fetch()) {
        $pdo->exec("ALTER TABLE bus ADD COLUMN TotalSeats INT NOT NULL DEFAULT 40");
        echo "Column TotalSeats added to bus table.<br>\n";
    } else {
        echo "Column TotalSeats already exists on bus table.<br>\n";
    }

    // 2. Alter transaction table to add SeatID
    $q = $pdo->query("SHOW COLUMNS FROM transaction LIKE 'SeatID'");
    if (!$q->fetch()) {
        $pdo->exec("ALTER TABLE transaction ADD COLUMN SeatID VARCHAR(50) NULL");
        echo "Column SeatID added to transaction table.<br>\n";
    } else {
        echo "Column SeatID already exists on transaction table.<br>\n";
    }

    // 3. Add foreign key from transaction(SeatID) to bus_seats(SeatID)
    try {
        $pdo->exec("ALTER TABLE transaction ADD CONSTRAINT fk_transaction_seat FOREIGN KEY (SeatID) REFERENCES bus_seats(SeatID) ON DELETE SET NULL");
        echo "Foreign key constraint fk_transaction_seat added.<br>\n";
    } catch (PDOException $e) {
        echo "Constraint check/addition message: " . $e->getMessage() . "<br>\n";
    }

    echo "Migration completed successfully.<br>\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}
