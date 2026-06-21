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
            $infrastructureModel = $this->model('Infrastructure');

            $package = $packageModel->getPackageById($packageId);
            $seatId = $_POST['seat_id'] ?? null;

            if ($package) {
                $userId = $_SESSION['user_id'];

                if ($seatId && $package->BusID) {
                    // Check if seat exists and belongs to this bus
                    $seat = $infrastructureModel->getSeatById($seatId);
                    if ($seat && $seat->BusID === $package->BusID && !$seat->IsBooked) {
                        // Mark seat as booked
                        $infrastructureModel->markSeatAsBooked($seatId);

                        // Book package with seat
                        $transactionModel->bookPackageWithSeat($userId, $package->PackageID, $package->Price, $package->BaseTreeCount, $seatId);
                        
                        header('Location: ' . URLROOT . '/?success=booked&seat=' . urlencode($seat->SeatNumber));
                        exit;
                    } else {
                        header('Location: ' . URLROOT . '/?error=seat_taken');
                        exit;
                    }
                } else {
                    // Fallback to normal booking without seat
                    $transactionModel->bookPackage($userId, $package->PackageID, $package->Price, $package->BaseTreeCount);
                    
                    header('Location: ' . URLROOT . '/?success=booked');
                    exit;
                }
            } else {
                header('Location: ' . URLROOT . '/?error=not_found');
                exit;
            }
        }
    }

    public function seats($packageId) {
        $packageModel = $this->model('Package');
        $infrastructureModel = $this->model('Infrastructure');

        $package = $packageModel->getPackageById($packageId);
        if (!$package || !$package->BusID) {
            header('Content-Type: application/json');
            echo json_encode(['bus' => null, 'seats' => []]);
            exit;
        }

        $bus = $infrastructureModel->getBusById($package->BusID);
        $seats = $infrastructureModel->getSeatsByBusId($package->BusID);
        
        header('Content-Type: application/json');
        echo json_encode([
            'bus' => $bus,
            'seats' => $seats
        ]);
        exit;
    }
}
