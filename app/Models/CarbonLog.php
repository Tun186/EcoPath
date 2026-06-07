<?php
class CarbonLog {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAvailableGlobalCredits() {
        // Total trees planted globally (which generate credits)
        $this->db->query("SELECT SUM(TreesPlanted) as TotalTrees FROM Carbon_log WHERE CompanyID IS NULL");
        $row = $this->db->single();
        $totalTrees = $row->TotalTrees ?? 0;

        // Total credits already purchased by companies
        $this->db->query("SELECT SUM(CreditsPurchased) as TotalSold FROM Company_Transaction");
        $soldRow = $this->db->single();
        $totalSold = $soldRow->TotalSold ?? 0;

        return $totalTrees - $totalSold;
    }
}
