<?php

class Driver {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllDrivers() {
        $this->db->query('SELECT * FROM Driver ORDER BY DriverName ASC');
        return $this->db->resultSet();
    }

    public function getDriverById($id) {
        $this->db->query('SELECT * FROM Driver WHERE DriverID = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function createDriver($data) {
        $driverId = $this->db->autoID('Driver', 'DriverID', 'DRV-', 6);
        $this->db->query('INSERT INTO Driver (DriverID, DriverName, LicenseCode, DateOfBirth, NRC, ProfileImage, LicenseFrontImage, LicenseBackImage, LicenseExpDate, LicenseIssueYear, BloodType, LicenseClass, Address) VALUES (:driverid, :name, :license, :dob, :nrc, :image, :front_image, :back_image, :exp_date, :issue_year, :blood_type, :license_class, :address)');
        $this->db->bind(':driverid', $driverId);
        $this->db->bind(':name', $data['DriverName']);
        $this->db->bind(':license', $data['LicenseCode']);
        $this->db->bind(':dob', $data['DateOfBirth']);
        $this->db->bind(':nrc', $data['NRC']);
        $this->db->bind(':image', $data['ProfileImage']);
        $this->db->bind(':front_image', $data['LicenseFrontImage'] ?? null);
        $this->db->bind(':back_image', $data['LicenseBackImage'] ?? null);
        $this->db->bind(':exp_date', $data['LicenseExpDate'] ?? null);
        $this->db->bind(':issue_year', $data['LicenseIssueYear'] ?? null);
        $this->db->bind(':blood_type', $data['BloodType'] ?? null);
        $this->db->bind(':license_class', $data['LicenseClass'] ?? null);
        $this->db->bind(':address', $data['Address'] ?? null);
        return $this->db->execute();
    }

    public function updateDriver($data) {
        $this->db->query('UPDATE Driver SET DriverName = :name, LicenseCode = :license, DateOfBirth = :dob, NRC = :nrc, ProfileImage = :image, LicenseFrontImage = :front_image, LicenseBackImage = :back_image, LicenseExpDate = :exp_date, LicenseIssueYear = :issue_year, BloodType = :blood_type, LicenseClass = :license_class, Address = :address WHERE DriverID = :id');
        $this->db->bind(':id', $data['DriverID']);
        $this->db->bind(':name', $data['DriverName']);
        $this->db->bind(':license', $data['LicenseCode']);
        $this->db->bind(':dob', $data['DateOfBirth']);
        $this->db->bind(':nrc', $data['NRC']);
        $this->db->bind(':image', $data['ProfileImage']);
        $this->db->bind(':front_image', $data['LicenseFrontImage'] ?? null);
        $this->db->bind(':back_image', $data['LicenseBackImage'] ?? null);
        $this->db->bind(':exp_date', $data['LicenseExpDate'] ?? null);
        $this->db->bind(':issue_year', $data['LicenseIssueYear'] ?? null);
        $this->db->bind(':blood_type', $data['BloodType'] ?? null);
        $this->db->bind(':license_class', $data['LicenseClass'] ?? null);
        $this->db->bind(':address', $data['Address'] ?? null);
        return $this->db->execute();
    }

    public function deleteDriver($id) {
        // Warning: Might fail if Driver is linked to a Bus. We should probably just allow it and catch exception if linked.
        $this->db->query('DELETE FROM Driver WHERE DriverID = :id');
        $this->db->bind(':id', $id);
        try {
            return $this->db->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
}
