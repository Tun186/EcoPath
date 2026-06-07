<?php
class Company {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllCompanies() {
        $this->db->query('SELECT * FROM Company_Profile ORDER BY CompanyName ASC');
        return $this->db->resultSet();
    }

    public function createCompany($data) {
        $companyId = $this->db->autoID('Company_Profile', 'CompanyID', 'COM-', 6);
        $this->db->query('INSERT INTO Company_Profile (CompanyID, CompanyName, RegistrationNumber, ContactEmail, PurchasedCredits) VALUES (:id, :name, :reg, :email, 0)');
        $this->db->bind(':id', $companyId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':reg', $data['registration']);
        $this->db->bind(':email', $data['email']);
        
        return $this->db->execute();
    }

    public function sellCarbonCredits($companyId, $amountToBuy, $totalPrice) {
        // 1. Log the transaction
        $transactionId = $this->db->autoID('Company_Transaction', 'TransactionID', 'CTX-', 6);
        $this->db->query('INSERT INTO Company_Transaction (TransactionID, CompanyID, CreditsPurchased, TotalAmount, TransactionDate) VALUES (:tid, :cid, :credits, :amt, :tdate)');
        $this->db->bind(':tid', $transactionId);
        $this->db->bind(':cid', $companyId);
        $this->db->bind(':credits', $amountToBuy);
        $this->db->bind(':amt', $totalPrice);
        $this->db->bind(':tdate', date('Y-m-d'));
        $this->db->execute();

        // 2. Update company profile balance
        $this->db->query('UPDATE Company_Profile SET PurchasedCredits = PurchasedCredits + :credits WHERE CompanyID = :cid');
        $this->db->bind(':credits', $amountToBuy);
        $this->db->bind(':cid', $companyId);
        return $this->db->execute();
    }
}
