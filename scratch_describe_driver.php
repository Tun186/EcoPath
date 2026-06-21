<?php
$pdo = new PDO('mysql:host=localhost;dbname=ecopath_db', 'root', '');
foreach($pdo->query('DESCRIBE Driver') as $row) {
    echo $row['Field'] . ' ' . $row['Type'] . "\n";
}
