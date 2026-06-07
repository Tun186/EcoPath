<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Admin extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }

        // Ensure user is an Admin (RoleID '01')
        if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] !== '01') {
            die('Access Denied. You do not have permission to view this page.');
        }
    }

    public function index() {
        $data = [
            'title' => 'Admin Dashboard'
        ];

        $this->view('admin/dashboard', $data);
    }
    public function users() {
        $userModel = $this->model('User');
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action']) && $_POST['action'] == 'send_reset_link' && isset($_POST['email'])) {
                $email = $_POST['email'];
                $user = $userModel->getUserDataByEmail($email);
                
                if ($user) {
                    $token = bin2hex(random_bytes(32));
                    // 5 minutes expiry
                    $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));
                    
                    if ($userModel->setResetToken($email, $token, $expiry)) {
                        $resetLink = URLROOT . '/auth/resetPassword?token=' . $token;
                        
                        if (sendPasswordResetEmail($email, $resetLink)) {
                            header('Location: ' . URLROOT . '/admin/users?success=reset_sent');
                            exit;
                        } else {
                            die("Message could not be sent.");
                        }
                    }
                }
            } elseif (isset($_POST['user_id'], $_POST['role_id'])) {
                $userModel->updateUserRole($_POST['user_id'], $_POST['role_id']);
                header('Location: ' . URLROOT . '/admin/users?success=1');
                exit;
            }
        }

        $users = $userModel->getAllUsers();
        
        $data = [
            'title' => 'Manage Users',
            'users' => $users
        ];

        $this->view('admin/users', $data);
    }

    public function packages() {
        $packageModel = $this->model('Package');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'trees' => trim($_POST['trees']),
                'price' => trim($_POST['price'])
            ];
            $packageModel->createPackage($data);
            header('Location: ' . URLROOT . '/admin/packages?success=1');
            exit;
        }

        $packages = $packageModel->getAllPackages();

        $data = [
            'title' => 'Manage Packages',
            'packages' => $packages
        ];

        $this->view('admin/packages', $data);
    }

    public function transactions() {
        $transactionModel = $this->model('Transaction');
        $transactions = $transactionModel->getAllTransactions();

        $data = [
            'title' => 'Transactions & Financials',
            'transactions' => $transactions
        ];

        $this->view('admin/transactions', $data);
    }

    public function companies() {
        $companyModel = $this->model('Company');
        $carbonLogModel = $this->model('CarbonLog');

        $availableCredits = $carbonLogModel->getAvailableGlobalCredits();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action']) && $_POST['action'] == 'create') {
                $data = [
                    'name' => trim($_POST['name']),
                    'registration' => trim($_POST['registration']),
                    'email' => trim($_POST['email'])
                ];
                $companyModel->createCompany($data);
                header('Location: ' . URLROOT . '/admin/companies?success=created');
                exit;
            }

            if (isset($_POST['action']) && $_POST['action'] == 'buy') {
                $companyId = $_POST['company_id'];
                $amountToBuy = (int)$_POST['amount'];
                // Example price: $10 per credit
                $totalPrice = $amountToBuy * 10.00;

                if ($amountToBuy <= $availableCredits) {
                    $companyModel->sellCarbonCredits($companyId, $amountToBuy, $totalPrice);
                    header('Location: ' . URLROOT . '/admin/companies?success=sold');
                    exit;
                } else {
                    header('Location: ' . URLROOT . '/admin/companies?error=not_enough_credits');
                    exit;
                }
            }
        }

        $companies = $companyModel->getAllCompanies();

        $data = [
            'title' => 'B2B Carbon Sales',
            'companies' => $companies,
            'availableCredits' => $availableCredits
        ];

        $this->view('admin/companies', $data);
    }

    public function drivers() {
        $driverModel = $this->model('Driver');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Helper logic for image upload
            $uploadImage = function() {
                if (isset($_FILES['ProfileImage']) && $_FILES['ProfileImage']['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['ProfileImage']['tmp_name'];
                    $fileName = basename($_FILES['ProfileImage']['name']);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($fileExt, $allowedExts)) {
                        $newFileName = uniqid('drv_') . '.' . $fileExt;
                        $uploadPath = dirname(dirname(dirname(__FILE__))) . '/public/uploads/drivers/' . $newFileName;
                        if (move_uploaded_file($tmpName, $uploadPath)) {
                            return 'uploads/drivers/' . $newFileName;
                        }
                    }
                }
                return null;
            };

            if (isset($_POST['action']) && $_POST['action'] == 'create') {
                $imagePath = $uploadImage();
                $data = [
                    'DriverName' => trim($_POST['DriverName']),
                    'LicenseCode' => trim($_POST['LicenseCode']),
                    'DateOfBirth' => empty($_POST['DateOfBirth']) ? null : $_POST['DateOfBirth'],
                    'NRC' => trim($_POST['NRC']),
                    'ProfileImage' => $imagePath
                ];
                $driverModel->createDriver($data);
                header('Location: ' . URLROOT . '/admin/drivers?success=created');
                exit;
            } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
                $imagePath = $uploadImage();
                $data = [
                    'DriverID' => $_POST['DriverID'],
                    'DriverName' => trim($_POST['DriverName']),
                    'LicenseCode' => trim($_POST['LicenseCode']),
                    'DateOfBirth' => empty($_POST['DateOfBirth']) ? null : $_POST['DateOfBirth'],
                    'NRC' => trim($_POST['NRC'])
                ];
                
                // Keep old image if a new one wasn't uploaded
                if ($imagePath) {
                    $data['ProfileImage'] = $imagePath;
                } else {
                    $existingDriver = $driverModel->getDriverById($_POST['DriverID']);
                    $data['ProfileImage'] = $existingDriver->ProfileImage;
                }

                $driverModel->updateDriver($data);
                header('Location: ' . URLROOT . '/admin/drivers?success=updated');
                exit;
            } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
                $driverId = $_POST['DriverID'];
                if ($driverModel->deleteDriver($driverId)) {
                    header('Location: ' . URLROOT . '/admin/drivers?success=deleted');
                } else {
                    header('Location: ' . URLROOT . '/admin/drivers?error=linked');
                }
                exit;
            }
        }

        $drivers = $driverModel->getAllDrivers();

        $data = [
            'title' => 'Manage Drivers',
            'drivers' => $drivers
        ];

        $this->view('admin/drivers', $data);
    }
}
