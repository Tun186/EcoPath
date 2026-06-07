<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Find user by email
    public function findUserByEmail($email) {
        $this->db->query('SELECT * FROM User WHERE Email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    // Register User
    public function register($data) {
        // Determine role (first user is Admin '01', others are User '02')
        $this->db->query("SELECT COUNT(*) AS UserCount FROM User");
        $countRow = $this->db->single();
        $roleId = ($countRow->UserCount == 0) ? '01' : '02';

        $userid = $this->db->autoID('User', 'UserID', 'USR-', 6);
        $this->db->query('INSERT INTO User (UserID, RoleID, Username, Email, Phone, PasswordHash, EcoPoints, RegistrationDate) VALUES (:userid, :roleid, :username, :email, :phone, :password, 0, :regdate)');
        // Bind values
        $this->db->bind(':userid', $userid);
        $this->db->bind(':roleid', $roleId);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':regdate', date('Y-m-d'));

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Login User
    public function login($email, $password) {
        $this->db->query('SELECT * FROM User WHERE Email = :email');
        $this->db->bind(':email', $email);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row->PasswordHash;
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        return false;
    }

    public function getUserDataByEmail($email) {
        $this->db->query('SELECT * FROM User WHERE Email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function registerOAuth($data) {
        // Determine role (first user is Admin '01', others are User '02')
        $this->db->query("SELECT COUNT(*) AS UserCount FROM User");
        $countRow = $this->db->single();
        $roleId = ($countRow->UserCount == 0) ? '01' : '02';

        $userid = $this->db->autoID('User', 'UserID', 'USR-', 6);
        $this->db->query('INSERT INTO User (UserID, RoleID, Username, Email, Phone, PasswordHash, EcoPoints, RegistrationDate) VALUES (:userid, :roleid, :username, :email, :phone, :password, 0, :regdate)');
        
        $this->db->bind(':userid', $userid);
        $this->db->bind(':roleid', $roleId);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':password', $data['password']);
        $this->db->bind(':regdate', date('Y-m-d'));

        return $this->db->execute();
    }

    public function getAllUsers() {
        $this->db->query('SELECT u.*, r.RoleName FROM user u LEFT JOIN role r ON u.RoleID = r.RoleID ORDER BY u.RegistrationDate DESC');
        return $this->db->resultSet();
    }

    public function updateUserRole($userId, $roleId) {
        $this->db->query('UPDATE user SET RoleID = :roleid WHERE UserID = :userid');
        $this->db->bind(':roleid', $roleId);
        $this->db->bind(':userid', $userId);
        return $this->db->execute();
    }

    public function setResetToken($email, $token, $expiry) {
        // Ignore the PHP $expiry parameter and use MySQL's clock to prevent timezone mismatches
        $this->db->query('UPDATE user SET ResetToken = :token, ResetExpiry = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE Email = :email');
        $this->db->bind(':token', $token);
        $this->db->bind(':email', $email);
        return $this->db->execute();
    }

    public function findByResetToken($token) {
        $this->db->query('SELECT * FROM user WHERE ResetToken = :token AND ResetExpiry > NOW()');
        $this->db->bind(':token', $token);
        return $this->db->single();
    }

    public function updatePassword($userId, $passwordHash) {
        $this->db->query('UPDATE user SET PasswordHash = :password, ResetToken = NULL, ResetExpiry = NULL WHERE UserID = :userid');
        $this->db->bind(':password', $passwordHash);
        $this->db->bind(':userid', $userId);
        return $this->db->execute();
    }
}
