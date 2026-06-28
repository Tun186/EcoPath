<?php

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct() {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            // Don't echo the error directly in production, but okay for development.
        }
    }

    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute() {
        return $this->stmt->execute();
    }

    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount() {
        return $this->stmt->rowCount();
    }

    public function autoID($tableName, $columnName, $prefix, $numberLength) {
        $this->query("SELECT MAX($columnName) AS MaxID FROM $tableName WHERE $columnName LIKE :prefix");
        $this->bind(':prefix', $prefix . '%');
        $row = $this->single();

        if ($row && $row->MaxID) {
            $maxID = $row->MaxID;
            $maxID = str_replace($prefix, "", $maxID);
            $maxID = (int)$maxID;
            $maxID++;
            return $prefix . str_pad($maxID, $numberLength, "0", STR_PAD_LEFT);
        } else {
            return $prefix . str_pad(1, $numberLength, "0", STR_PAD_LEFT);
        }
    }
}
