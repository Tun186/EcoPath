<?php

class Home extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Ensure user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $packageModel = $this->model('Package');
        $packages = $packageModel->getAllPackages();

        $data = [
            'title' => 'User Dashboard - EcoPath',
            'packages' => $packages
        ];

        $this->view('home/index', $data);
    }

    public function book($packageId) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $packageModel = $this->model('Package');
            $transactionModel = $this->model('Transaction');

            $package = $packageModel->getPackageById($packageId);

            if ($package) {
                // Perform booking
                $userId = $_SESSION['user_id'];
                $transactionModel->bookPackage($userId, $package->PackageID, $package->Price, $package->BaseTreeCount);
                
                header('Location: ' . URLROOT . '/?success=booked');
                exit;
            } else {
                header('Location: ' . URLROOT . '/?error=not_found');
                exit;
            }
        }
    }
}
