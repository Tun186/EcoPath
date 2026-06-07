<?php
class Infrastructure {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function getAllHotels($isActive = 1) {
        $this->db->query('SELECT h.*, c.CityName, c.RegionID, r.RegionName, u1.Username as CreatorName, u2.Username as UpdaterName 
                          FROM Hotel h 
                          LEFT JOIN City c ON h.CityID = c.CityID 
                          LEFT JOIN Region r ON c.RegionID = r.RegionID
                          LEFT JOIN user u1 ON h.CreatedBy = u1.UserID
                          LEFT JOIN user u2 ON h.UpdatedBy = u2.UserID
                          WHERE h.IsActive = :active
                          ORDER BY h.HotelName ASC');
        $this->db->bind(':active', $isActive);
        return $this->db->resultSet();
    }

    public function getAllLandmarks($isActive = 1) {
        $this->db->query('SELECT l.*, c.CityName, c.RegionID, r.RegionName, u1.Username as CreatorName, u2.Username as UpdaterName 
                          FROM Landmarks l 
                          LEFT JOIN City c ON l.CityID = c.CityID 
                          LEFT JOIN Region r ON c.RegionID = r.RegionID
                          LEFT JOIN user u1 ON l.CreatedBy = u1.UserID
                          LEFT JOIN user u2 ON l.UpdatedBy = u2.UserID
                          WHERE l.IsActive = :active
                          ORDER BY l.LandmarkName ASC');
        $this->db->bind(':active', $isActive);
        return $this->db->resultSet();
    }

    public function getAllBuses() {
        $this->db->query('SELECT * FROM Bus ORDER BY OperatorName ASC');
        return $this->db->resultSet();
    }

    public function getAllRegions() {
        $this->db->query('SELECT r.*, u1.Username as CreatorName, u2.Username as UpdaterName 
                          FROM Region r 
                          LEFT JOIN user u1 ON r.CreatedBy = u1.UserID
                          LEFT JOIN user u2 ON r.UpdatedBy = u2.UserID
                          ORDER BY r.RegionName ASC');
        return $this->db->resultSet();
    }

    public function getAllCities() {
        $this->db->query('SELECT c.*, r.RegionName, u1.Username as CreatorName, u2.Username as UpdaterName 
                          FROM City c 
                          LEFT JOIN Region r ON c.RegionID = r.RegionID 
                          LEFT JOIN user u1 ON c.CreatedBy = u1.UserID
                          LEFT JOIN user u2 ON c.UpdatedBy = u2.UserID
                          ORDER BY r.RegionName ASC, c.CityName ASC');
        return $this->db->resultSet();
    }

    public function createRegion($data) {
        $regionId = $this->db->autoID('Region', 'RegionID', 'REG-', 6);
        $this->db->query('INSERT INTO Region (RegionID, RegionName, CreatedBy, CreatedAt) VALUES (:rid, :name, :creator, NOW())');
        $this->db->bind(':rid', $regionId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':creator', $_SESSION['user_id']);
        return $this->db->execute();
    }

    public function updateRegion($data) {
        $this->db->query('UPDATE Region SET RegionName = :name, UpdatedBy = :updater, UpdatedAt = NOW() WHERE RegionID = :rid');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':updater', $_SESSION['user_id']);
        $this->db->bind(':rid', $data['id']);
        return $this->db->execute();
    }

    public function createCity($data) {
        $cityId = $this->db->autoID('City', 'CityID', 'CTY-', 6);
        $this->db->query('INSERT INTO City (CityID, RegionID, CityName, CreatedBy, CreatedAt) VALUES (:cid, :rid, :name, :creator, NOW())');
        $this->db->bind(':cid', $cityId);
        $this->db->bind(':rid', $data['region_id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':creator', $_SESSION['user_id']);
        return $this->db->execute();
    }

    public function updateCity($data) {
        $this->db->query('UPDATE City SET CityName = :name, RegionID = :rid, UpdatedBy = :updater, UpdatedAt = NOW() WHERE CityID = :cid');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':rid', $data['region_id']);
        $this->db->bind(':updater', $_SESSION['user_id']);
        $this->db->bind(':cid', $data['id']);
        return $this->db->execute();
    }

    // Dummy method to ensure at least one city exists for dropdowns to work
    public function getOrCreateDefaultCity() {
        $this->db->query("SELECT CityID FROM City LIMIT 1");
        $city = $this->db->single();
        if ($city) return $city->CityID;

        // Create default region & city
        $regionId = 'REG-001';
        $this->db->query("INSERT INTO Region (RegionID, RegionName) VALUES ('$regionId', 'Central')");
        $this->db->execute();

        $cityId = 'CTY-001';
        $this->db->query("INSERT INTO City (CityID, RegionID, CityName) VALUES ('$cityId', '$regionId', 'Default City')");
        $this->db->execute();
        
        return $cityId;
    }

    public function createHotel($data) {
        $cityId = !empty($data['city_id']) ? $data['city_id'] : $this->getOrCreateDefaultCity();
        $hotelId = $this->db->autoID('Hotel', 'HotelID', 'HTL-', 6);
        $this->db->query('INSERT INTO Hotel (HotelID, CityID, HotelName, EcoRating, Lat, Lng, Description, CreatedBy) VALUES (:hid, :cid, :name, :eco, :lat, :lng, :desc, :creator)');
        $this->db->bind(':hid', $hotelId);
        $this->db->bind(':cid', $cityId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':eco', $data['eco_rating']);
        $this->db->bind(':lat', $data['lat']);
        $this->db->bind(':lng', $data['lng']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':creator', $_SESSION['user_id']);
        return $this->db->execute();
    }

    public function createLandmark($data) {
        $cityId = !empty($data['city_id']) ? $data['city_id'] : $this->getOrCreateDefaultCity();
        $landmarkId = $this->db->autoID('Landmarks', 'LandmarkID', 'LMK-', 6);
        $this->db->query('INSERT INTO Landmarks (LandmarkID, CityID, LandmarkName, Lat, Lng, Description, CreatedBy) VALUES (:lid, :cid, :name, :lat, :lng, :desc, :creator)');
        $this->db->bind(':lid', $landmarkId);
        $this->db->bind(':cid', $cityId);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':lat', $data['lat']);
        $this->db->bind(':lng', $data['lng']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':creator', $_SESSION['user_id']);
        return $this->db->execute();
    }

    public function createBus($data) {
        // Need a default driver
        $this->db->query("SELECT DriverID FROM Driver LIMIT 1");
        $driver = $this->db->single();
        if ($driver) {
            $driverId = $driver->DriverID;
        } else {
            $driverId = 'DRV-001';
            $this->db->query("INSERT INTO Driver (DriverID, DriverName, LicenseCode) VALUES ('$driverId', 'Default Driver', 'LIC-000')");
            $this->db->execute();
        }

        $busId = $this->db->autoID('Bus', 'BusID', 'BUS-', 6);
        $this->db->query('INSERT INTO Bus (BusID, DriverID, OperatorName, EmissionRate) VALUES (:bid, :did, :name, :emission)');
        $this->db->bind(':bid', $busId);
        $this->db->bind(':did', $driverId);
        $this->db->bind(':name', $data['operator']);
        $this->db->bind(':emission', $data['emission_rate']);
        return $this->db->execute();
    }

    public function updateHotel($data) {
        $this->db->query('UPDATE Hotel SET HotelName = :name, CityID = :cityId, EcoRating = :eco, Lat = :lat, Lng = :lng, Description = :desc, UpdatedBy = :updater, UpdatedAt = :updatedAt WHERE HotelID = :hid');
        $this->db->bind(':hid', $data['hotel_id']);
        $this->db->bind(':cityId', !empty($data['city_id']) ? $data['city_id'] : $this->getOrCreateDefaultCity());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':eco', $data['eco_rating']);
        $this->db->bind(':lat', $data['lat']);
        $this->db->bind(':lng', $data['lng']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':updater', $_SESSION['user_id']);
        $this->db->bind(':updatedAt', date('Y-m-d H:i:s'));
        return $this->db->execute();
    }

    public function updateLandmark($data) {
        $this->db->query('UPDATE Landmarks SET LandmarkName = :name, CityID = :cityId, Lat = :lat, Lng = :lng, Description = :desc, UpdatedBy = :updater, UpdatedAt = :updatedAt WHERE LandmarkID = :lid');
        $this->db->bind(':lid', $data['landmark_id']);
        $this->db->bind(':cityId', !empty($data['city_id']) ? $data['city_id'] : $this->getOrCreateDefaultCity());
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':lat', $data['lat']);
        $this->db->bind(':lng', $data['lng']);
        $this->db->bind(':desc', $data['description']);
        $this->db->bind(':updater', $_SESSION['user_id']);
        $this->db->bind(':updatedAt', date('Y-m-d H:i:s'));
        return $this->db->execute();
    }

    public function toggleHotelStatus($id, $status) {
        $this->db->query('UPDATE Hotel SET IsActive = :status WHERE HotelID = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function toggleLandmarkStatus($id, $status) {
        $this->db->query('UPDATE Landmarks SET IsActive = :status WHERE LandmarkID = :id');
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
