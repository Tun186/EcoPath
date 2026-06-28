<?php
// Define HTTP_HOST if not set to prevent notices when running via CLI
if (!isset($_SERVER['HTTP_HOST'])) {
    $_SERVER['HTTP_HOST'] = 'localhost';
}
if (!isset($_SERVER['SCRIPT_NAME'])) {
    $_SERVER['SCRIPT_NAME'] = '/set_default_passwords.php';
}

require_once 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $defaultPassword = 'User@123';
    $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('UPDATE User SET PasswordHash = :hash');
    $stmt->execute([':hash' => $hash]);
    
    $count = $stmt->rowCount();
    echo "Successfully updated " . $count . " users to the default password: '" . $defaultPassword . "'<br>\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "<br>\n";
}
