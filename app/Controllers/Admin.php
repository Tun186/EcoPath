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
            
            // Helper logic for file uploads (Profile, License Front, License Back)
            $uploadFile = function($key) {
                if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES[$key]['tmp_name'];
                    $fileName = basename($_FILES[$key]['name']);
                    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
                    if (in_array($fileExt, $allowedExts)) {
                        $newFileName = uniqid($key . '_') . '.' . $fileExt;
                        $uploadPath = dirname(dirname(dirname(__FILE__))) . '/public/uploads/drivers/' . $newFileName;
                        
                        // Ensure upload directory exists
                        if (!file_exists(dirname($uploadPath))) {
                            mkdir(dirname($uploadPath), 0777, true);
                        }
                        
                        if (move_uploaded_file($tmpName, $uploadPath)) {
                            return 'uploads/drivers/' . $newFileName;
                        }
                    }
                }
                return null;
            };

            if (isset($_POST['action']) && $_POST['action'] == 'create') {
                $imagePath = $uploadFile('ProfileImage');
                $frontPath = $uploadFile('LicenseFrontImage');
                $backPath = $uploadFile('LicenseBackImage');
                
                $data = [
                    'DriverName' => trim($_POST['DriverName']),
                    'LicenseCode' => trim($_POST['LicenseCode']),
                    'DateOfBirth' => empty($_POST['DateOfBirth']) ? null : $_POST['DateOfBirth'],
                    'NRC' => trim($_POST['NRC']),
                    'ProfileImage' => $imagePath,
                    'LicenseFrontImage' => $frontPath,
                    'LicenseBackImage' => $backPath,
                    'LicenseExpDate' => empty($_POST['LicenseExpDate']) ? null : $_POST['LicenseExpDate'],
                    'LicenseIssueYear' => trim($_POST['LicenseIssueYear']),
                    'BloodType' => trim($_POST['BloodType']),
                    'LicenseClass' => trim($_POST['LicenseClass']),
                    'Address' => trim($_POST['Address'])
                ];
                $driverModel->createDriver($data);
                header('Location: ' . URLROOT . '/admin/drivers?success=created');
                exit;
            } elseif (isset($_POST['action']) && $_POST['action'] == 'update') {
                $imagePath = $uploadFile('ProfileImage');
                $frontPath = $uploadFile('LicenseFrontImage');
                $backPath = $uploadFile('LicenseBackImage');
                
                $existingDriver = $driverModel->getDriverById($_POST['DriverID']);
                
                $data = [
                    'DriverID' => $_POST['DriverID'],
                    'DriverName' => trim($_POST['DriverName']),
                    'LicenseCode' => trim($_POST['LicenseCode']),
                    'DateOfBirth' => empty($_POST['DateOfBirth']) ? null : $_POST['DateOfBirth'],
                    'NRC' => trim($_POST['NRC']),
                    'LicenseExpDate' => empty($_POST['LicenseExpDate']) ? null : $_POST['LicenseExpDate'],
                    'LicenseIssueYear' => trim($_POST['LicenseIssueYear']),
                    'BloodType' => trim($_POST['BloodType']),
                    'LicenseClass' => trim($_POST['LicenseClass']),
                    'Address' => trim($_POST['Address'])
                ];
                
                $data['ProfileImage'] = $imagePath ? $imagePath : $existingDriver->ProfileImage;
                $data['LicenseFrontImage'] = $frontPath ? $frontPath : $existingDriver->LicenseFrontImage;
                $data['LicenseBackImage'] = $backPath ? $backPath : $existingDriver->LicenseBackImage;

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

    public function ocr() {
        // Ensure request is POST and contains a file
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['image'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid request. File "image" is required.']);
            exit;
        }

        $side = isset($_POST['side']) ? $_POST['side'] : 'front';
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'File upload error code: ' . $file['error']]);
            exit;
        }

        // Check if GEMINI_API_KEY is configured
        if (!defined('GEMINI_API_KEY') || empty(GEMINI_API_KEY) || GEMINI_API_KEY === 'YOUR_GEMINI_API_KEY') {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Gemini API Key is not configured. Please define GEMINI_API_KEY in config/config.php']);
            exit;
        }

        $imagePath = $file['tmp_name'];
        $mimeType = $file['type'];
        
        // Base64 encode the image
        $imageData = base64_encode(file_get_contents($imagePath));

        // Prepare prompt based on front or back side
        if ($side === 'front') {
            $prompt = "You are a professional scanner. Extract driver license information from the FRONT side of this Myanmar Driving License. Read both English and Myanmar texts if needed, and parse values accurately. Provide output in STRICT JSON format with exactly the following fields (if a field cannot be found, output null):
            {
              \"license_no\": \"(extract the License/Licence No, e.g. B/38251/25 or B/08942/20)\",
              \"name\": \"(extract the Name in English capital letters, e.g. TUN LINN NAING or THIRI SAN)\",
              \"nrc_no\": \"(extract the NRC No in English, e.g. 12/KAMATA(N)091837 or 13/TAHATA(N)987654. If it's written in Myanmar or partly English, convert it fully to English like 12/KAMATA(N)091837)\",
              \"date_of_birth\": \"(extract Date of Birth, e.g. 1-7-2005 or 15-7-1982. Output strictly as YYYY-MM-DD, e.g., 2005-07-01 or 1982-07-15. Carefully verify day and month order)\",
              \"blood_type\": \"(extract Blood Type, e.g. O, O-, A, B, AB, etc.)\",
              \"valid_up_to\": \"(extract Valid up to date, e.g. 5-12-2030 or 16-8-2030. Output strictly as YYYY-MM-DD, e.g. 2030-12-05 or 2030-08-16)\"
            }";
        } else {
            $prompt = "You are a professional scanner. Extract driver license information from the BACK side of this Myanmar Driving License. Provide output in STRICT JSON format with exactly the following fields (if a field cannot be found, output null):
            {
              \"issued_year\": \"(extract the Issued Year, e.g. 2025 or 2020)\",
              \"license_class\": \"(extract the Licence Class or License Category, e.g., B or A. Provide ONLY the letters or code, e.g. 'B' or 'A' or 'E', keep it under 10 characters)\",
              \"address\": \"(extract the full Address in English. Be thorough, combine multiline address if necessary. E.g., '41, HTAETAN ST, AYENYEIN QTR, KYINMYINDAING' or 'MYO THIT QTR, BAGO TSP')\"
            }";
        }

        // Call Gemini API
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . GEMINI_API_KEY;

        $requestBody = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inlineData' => [
                                'mimeType' => $mimeType,
                                'data' => $imageData
                            ]
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'responseMimeType' => 'application/json'
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local testing compatibility

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Curl error calling Gemini API: ' . $error]);
            exit;
        }

        if ($httpCode !== 200) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Gemini API returned HTTP status code ' . $httpCode,
                'response' => json_decode($response, true) ?? $response
            ]);
            exit;
        }

        $result = json_decode($response, true);
        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Gemini API response structure is unexpected.',
                'raw_response' => $result
            ]);
            exit;
        }

        $text = trim($result['candidates'][0]['content']['parts'][0]['text']);
        
        // Strip markdown JSON block if present
        if (strpos($text, '```') === 0) {
            $text = preg_replace('/^```(?:json)?\s*/i', '', $text);
            $text = preg_replace('/\s*```$/', '', $text);
            $text = trim($text);
        }

        $ocrData = json_decode($text, true);
        if ($ocrData === null) {
            header('Content-Type: application/json');
            echo json_encode([
                'error' => 'Failed to parse JSON response from Gemini.',
                'raw_text' => $text
            ]);
            exit;
        }

        if ($side === 'front') {
            $output = [
                'license_no' => $ocrData['license_no'] ?? $ocrData['LicenseNo'] ?? $ocrData['licenseNo'] ?? null,
                'name' => $ocrData['name'] ?? $ocrData['Name'] ?? null,
                'nrc_no' => $ocrData['nrc_no'] ?? $ocrData['nrc'] ?? $ocrData['NRC'] ?? null,
                'date_of_birth' => $ocrData['date_of_birth'] ?? $ocrData['dob'] ?? $ocrData['DOB'] ?? null,
                'blood_type' => $ocrData['blood_type'] ?? $ocrData['BloodType'] ?? null,
                'valid_up_to' => $ocrData['valid_up_to'] ?? $ocrData['validUpTo'] ?? null
            ];
            
            // Clean up and truncate values if necessary to fit database
            if ($output['license_no'] !== null) $output['license_no'] = substr(trim($output['license_no']), 0, 100);
            if ($output['name'] !== null) $output['name'] = substr(trim($output['name']), 0, 255);
            if ($output['nrc_no'] !== null) $output['nrc_no'] = substr(trim($output['nrc_no']), 0, 100);
            if ($output['blood_type'] !== null) $output['blood_type'] = substr(trim($output['blood_type']), 0, 10);
        } else {
            $class = $ocrData['license_class'] ?? $ocrData['class'] ?? $ocrData['LicenseClass'] ?? null;
            if (is_array($class)) {
                $cleanedClasses = [];
                foreach ($class as $c) {
                    $c = trim($c);
                    if (!empty($c)) {
                        $cleanedClasses[] = $c;
                    }
                }
                $class = implode(', ', $cleanedClasses);
            }
            
            $output = [
                'issued_year' => $ocrData['issued_year'] ?? $ocrData['issuedYear'] ?? $ocrData['IssuedYear'] ?? null,
                'license_class' => $class,
                'address' => $ocrData['address'] ?? $ocrData['Address'] ?? null
            ];
            
            // Clean up and truncate values
            if ($output['issued_year'] !== null) $output['issued_year'] = substr(trim($output['issued_year']), 0, 10);
            if ($output['license_class'] !== null) {
                $output['license_class'] = trim($output['license_class']);
                if (strlen($output['license_class']) > 50) {
                    // Try to match a single letter class code or list of them at start, e.g. "B - Motorcycles" -> "B"
                    if (preg_match('/^([A-Z\s,\/&]+)\s*-/i', $output['license_class'], $matches)) {
                        $output['license_class'] = trim($matches[1]);
                    }
                    $output['license_class'] = substr($output['license_class'], 0, 50);
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($output);
        exit;
    }
}
