<?php
class Transaction {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllTransactions() {
        $this->db->query('SELECT t.*, u.Username, u.Email, p.PackageName, bs.SeatNumber 
                          FROM transaction t 
                          LEFT JOIN user u ON t.UserID = u.UserID 
                          LEFT JOIN package p ON t.PackageID = p.PackageID 
                          LEFT JOIN bus_seats bs ON t.SeatID = bs.SeatID
                          ORDER BY t.TransactionDate DESC');
        return $this->db->resultSet();
    }

    public function bookPackage($userId, $packageId, $price, $treesPlanted) {
        // 1. Create the Transaction
        $transactionId = $this->db->autoID('transaction', 'TransactionID', 'TXN-', 6);
        $this->db->query('INSERT INTO transaction (TransactionID, UserID, PackageID, TotalAmount, Status, TransactionDate) VALUES (:tid, :uid, :pid, :amt, :status, :tdate)');
        $this->db->bind(':tid', $transactionId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':pid', $packageId);
        $this->db->bind(':amt', $price);
        $this->db->bind(':status', 'Completed');
        $this->db->bind(':tdate', date('Y-m-d'));
        $this->db->execute();

        // 2. Create the Carbon Log (this adds to the Global Pool!)
        $logId = $this->db->autoID('Carbon_log', 'LogID', 'LOG-', 6);
        $this->db->query('INSERT INTO Carbon_log (LogID, UserID, PackageID, CO2Emitted, TreesPlanted, LogDate) VALUES (:lid, :uid, :pid, :co2, :trees, :ldate)');
        $this->db->bind(':lid', $logId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':pid', $packageId);
        $this->db->bind(':co2', 10.50); // Dummy CO2 estimation
        $this->db->bind(':trees', $treesPlanted);
        $this->db->bind(':ldate', date('Y-m-d'));
        return $this->db->execute();
    }

    public function bookPackageWithSeat($userId, $packageId, $price, $treesPlanted, $seatId) {
        // 1. Create the Transaction with SeatID
        $transactionId = $this->db->autoID('transaction', 'TransactionID', 'TXN-', 6);
        $this->db->query('INSERT INTO transaction (TransactionID, UserID, PackageID, SeatID, TotalAmount, Status, TransactionDate) VALUES (:tid, :uid, :pid, :sid, :amt, :status, :tdate)');
        $this->db->bind(':tid', $transactionId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':pid', $packageId);
        $this->db->bind(':sid', $seatId);
        $this->db->bind(':amt', $price);
        $this->db->bind(':status', 'Completed');
        $this->db->bind(':tdate', date('Y-m-d'));
        $this->db->execute();

        // 2. Create the Carbon Log
        $logId = $this->db->autoID('Carbon_log', 'LogID', 'LOG-', 6);
        $this->db->query('INSERT INTO Carbon_log (LogID, UserID, PackageID, CO2Emitted, TreesPlanted, LogDate) VALUES (:lid, :uid, :pid, :co2, :trees, :ldate)');
        $this->db->bind(':lid', $logId);
        $this->db->bind(':uid', $userId);
        $this->db->bind(':pid', $packageId);
        $this->db->bind(':co2', 10.50); // Dummy CO2 estimation
        $this->db->bind(':trees', $treesPlanted);
        $this->db->bind(':ldate', date('Y-m-d'));
        return $this->db->execute();
    }
}
