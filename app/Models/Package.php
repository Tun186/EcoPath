<?php
class Package {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllPackages() {
        $this->db->query('
            SELECT p.*, b.OperatorName,
            (SELECT GROUP_CONCAT(h.HotelName SEPARATOR ", ") FROM Package_Hotel ph JOIN Hotel h ON ph.HotelID = h.HotelID WHERE ph.PackageID = p.PackageID) as Hotels,
            (SELECT GROUP_CONCAT(l.LandmarkName SEPARATOR ", ") FROM Package_Landmarks pl JOIN Landmarks l ON pl.LandmarkID = l.LandmarkID WHERE pl.PackageID = p.PackageID) as Landmarks,
            (SELECT GROUP_CONCAT(pp.LocationName SEPARATOR ", ") FROM pickup_points pp WHERE pp.PackageID = p.PackageID) as PickupPoints
            FROM package p 
            LEFT JOIN bus b ON p.BusID = b.BusID
        ');
        return $this->db->resultSet();
    }

    public function createPackage($data) {
        $packageId = $this->db->autoID('package', 'PackageID', 'PKG-', 6);
        $this->db->query('INSERT INTO package (PackageID, PackageName, BaseTreeCount, Price) VALUES (:id, :name, :trees, :price)');
        $this->db->bind(':id', $packageId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':trees', $data['trees']);
        $this->db->bind(':price', $data['price']);
        
        return $this->db->execute();
    }

    public function createPlannerPackage($data) {
        $packageId = $this->db->autoID('package', 'PackageID', 'PKG-', 6);
        $this->db->query('INSERT INTO package (PackageID, BusID, PackageName, BaseTreeCount, Price, Distance, CalculatedCO2) VALUES (:id, :bus, :name, :trees, :price, :dist, :co2)');
        $this->db->bind(':id', $packageId);
        $this->db->bind(':bus', $data['bus_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':trees', $data['trees']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':dist', $data['distance']);
        $this->db->bind(':co2', $data['calculated_co2']);
        
        if (!$this->db->execute()) {
            return false;
        }

        if (!empty($data['hotel_ids'])) {
            foreach($data['hotel_ids'] as $hotelId) {
                $this->db->query('INSERT INTO Package_Hotel (PackageID, HotelID) VALUES (:pid, :hid)');
                $this->db->bind(':pid', $packageId);
                $this->db->bind(':hid', $hotelId);
                $this->db->execute();
            }
        }

        if (!empty($data['landmark_ids'])) {
            foreach($data['landmark_ids'] as $landmarkId) {
                $this->db->query('INSERT INTO Package_Landmarks (PackageID, LandmarkID) VALUES (:pid, :lid)');
                $this->db->bind(':pid', $packageId);
                $this->db->bind(':lid', $landmarkId);
                $this->db->execute();
            }
        }

        // Insert pickup points
        if (!empty($data['pickup_points'])) {
            $points = explode(',', $data['pickup_points']);
            foreach ($points as $point) {
                $pointName = trim($point);
                if ($pointName !== '') {
                    $pointId = $this->db->autoID('pickup_points', 'PickupPointID', 'PKP-', 6);
                    $this->db->query('INSERT INTO pickup_points (PickupPointID, PackageID, LocationName) VALUES (:id, :pid, :name)');
                    $this->db->bind(':id', $pointId);
                    $this->db->bind(':pid', $packageId);
                    $this->db->bind(':name', $pointName);
                    $this->db->execute();
                }
            }
        }
        
        return true;
    }

    public function getPackageById($packageId) {
        $this->db->query('SELECT * FROM package WHERE PackageID = :id');
        $this->db->bind(':id', $packageId);
        return $this->db->single();
    }
}
