<?php
class Planner extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != '03') {
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function index() {
        $packageModel = $this->model('Package');
        $packages = $packageModel->getAllPackages();

        $data = [
            'title' => 'Planner Dashboard',
            'packageCount' => count($packages)
        ];

        $this->view('planner/dashboard', $data);
    }

    public function packages() {
        $packageModel = $this->model('Package');
        $infrastructureModel = $this->model('Infrastructure');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'trees' => trim($_POST['trees']),
                'price' => trim($_POST['price']),
                'bus_id' => $_POST['bus_id'],
                'distance' => $_POST['distance'],
                'calculated_co2' => $_POST['calculated_co2'],
                'hotel_ids' => $_POST['hotel_ids'] ?? [],
                'landmark_ids' => $_POST['landmark_ids'] ?? []
            ];
            
            $packageModel->createPlannerPackage($data);
            header('Location: ' . URLROOT . '/planner/packages?success=created');
            exit;
        }

        $packages = $packageModel->getAllPackages();
        $hotels = $infrastructureModel->getAllHotels();
        $landmarks = $infrastructureModel->getAllLandmarks();
        $buses = $infrastructureModel->getAllBuses();

        $data = [
            'title' => 'Manage Expeditions',
            'packages' => $packages,
            'hotels' => $hotels,
            'landmarks' => $landmarks,
            'buses' => $buses
        ];

        $this->view('planner/packages', $data);
    }

    public function infrastructure() {
        $infrastructureModel = $this->model('Infrastructure');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] == 'add_hotel') {
                    $infrastructureModel->createHotel($_POST);
                } elseif ($_POST['action'] == 'add_landmark') {
                    $infrastructureModel->createLandmark($_POST);
                } elseif ($_POST['action'] == 'add_bus') {
                    $infrastructureModel->createBus($_POST);
                } elseif ($_POST['action'] == 'edit_hotel') {
                    $infrastructureModel->updateHotel($_POST);
                } elseif ($_POST['action'] == 'edit_landmark') {
                    $infrastructureModel->updateLandmark($_POST);
                } elseif ($_POST['action'] == 'deactivate_hotel') {
                    $infrastructureModel->toggleHotelStatus($_POST['hotel_id'], 0);
                } elseif ($_POST['action'] == 'restore_hotel') {
                    $infrastructureModel->toggleHotelStatus($_POST['hotel_id'], 1);
                } elseif ($_POST['action'] == 'deactivate_landmark') {
                    $infrastructureModel->toggleLandmarkStatus($_POST['landmark_id'], 0);
                } elseif ($_POST['action'] == 'restore_landmark') {
                    $infrastructureModel->toggleLandmarkStatus($_POST['landmark_id'], 1);
                } elseif ($_POST['action'] == 'export_hotels') {
                    $hotels = $infrastructureModel->getAllHotels(1);
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->fromArray(['Name', 'City Name', 'EcoRating', 'Latitude', 'Longitude', 'Description'], NULL, 'A1');
                    $rowNum = 2;
                    foreach ($hotels as $h) {
                        $sheet->fromArray([$h->HotelName, $h->CityName, $h->EcoRating, $h->Lat, $h->Lng, $h->Description], NULL, 'A' . $rowNum);
                        $rowNum++;
                    }
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="hotels_export.xlsx"');
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit;
                } elseif ($_POST['action'] == 'template_hotels') {
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->fromArray(['Name', 'City Name', 'EcoRating', 'Latitude', 'Longitude', 'Description'], NULL, 'A1');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="hotels_template.xlsx"');
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit;
                } elseif ($_POST['action'] == 'export_landmarks') {
                    $landmarks = $infrastructureModel->getAllLandmarks(1);
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->fromArray(['Name', 'City Name', 'Latitude', 'Longitude', 'Description'], NULL, 'A1');
                    $rowNum = 2;
                    foreach ($landmarks as $l) {
                        $sheet->fromArray([$l->LandmarkName, $l->CityName, $l->Lat, $l->Lng, $l->Description], NULL, 'A' . $rowNum);
                        $rowNum++;
                    }
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="landmarks_export.xlsx"');
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit;
                } elseif ($_POST['action'] == 'template_landmarks') {
                    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->fromArray(['Name', 'City Name', 'Latitude', 'Longitude', 'Description'], NULL, 'A1');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename="landmarks_template.xlsx"');
                    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                    $writer->save('php://output');
                    exit;
                } elseif ($_POST['action'] == 'import_hotels' || $_POST['action'] == 'import_landmarks') {
                    $isHotel = $_POST['action'] == 'import_hotels';
                    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
                        $cities = $infrastructureModel->getAllCities();
                        $cityMap = [];
                        foreach ($cities as $c) {
                            $cityMap[strtolower(trim($c->CityName))] = $c->CityID;
                        }

                        $existingNames = [];
                        if ($isHotel) {
                            $existing = $infrastructureModel->getAllHotels(1);
                            foreach ($existing as $e) {
                                $existingNames[strtolower(trim($e->HotelName))] = $e->HotelName;
                            }
                        } else {
                            $existing = $infrastructureModel->getAllLandmarks(1);
                            foreach ($existing as $e) {
                                $existingNames[strtolower(trim($e->LandmarkName))] = $e->LandmarkName;
                            }
                        }

                        $errors = [];
                        $successCount = 0;
                        
                        try {
                            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['excel_file']['tmp_name']);
                            $reader->setReadDataOnly(true);
                            $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']);
                            $worksheet = $spreadsheet->getActiveSheet();
                            $rows = $worksheet->toArray();
                            
                            $rowNum = 1;
                            $failedRows = [];

                            $validRows = [];
                            
                            foreach ($rows as $data) {
                                // Skip header row
                                if ($rowNum === 1 && strtolower(trim($data[0] ?? '')) === 'name') {
                                    $rowNum++;
                                    continue;
                                }
                                // Skip empty rows
                                if (empty($data) || (!isset($data[0]) && !isset($data[1]))) {
                                    $rowNum++;
                                    continue;
                                }
                                
                                $name = trim($data[0] ?? '');
                                $cityName = strtolower(trim($data[1] ?? ''));
                                
                                $cellErrors = [];
                                
                                if (empty($name)) {
                                    $cellErrors[0] = "Missing Name (Required)";
                                } else {
                                    $nameLower = strtolower($name);
                                    $cleanName = trim(str_replace(['hotel', 'pagoda', 'resort'], '', $nameLower));
                                    
                                    foreach ($existingNames as $dbNameLower => $originalDbName) {
                                        $cleanDbName = trim(str_replace(['hotel', 'pagoda', 'resort'], '', $dbNameLower));
                                        
                                        if ($cleanName !== '' && ($cleanName === $cleanDbName || (strlen($cleanName) > 3 && (str_contains($cleanDbName, $cleanName) || str_contains($cleanName, $cleanDbName))))) {
                                            $cellErrors[0] = "Similar to existing: " . $originalDbName;
                                            break;
                                        }
                                    }
                                }
                                
                                if (empty($cityName)) {
                                    $cellErrors[1] = "Missing City Name (Required)";
                                } elseif (!isset($cityMap[$cityName])) {
                                    $cellErrors[1] = "City '" . ($data[1] ?? '') . "' not found in database";
                                }
                                
                                if ($isHotel) {
                                    $ecoRating = trim($data[2] ?? '');
                                    $lat = trim($data[3] ?? '');
                                    $lng = trim($data[4] ?? '');
                                    $desc = trim($data[5] ?? '');
                                    
                                    if (empty($ecoRating)) $cellErrors[2] = "Missing EcoRating";
                                    if ($lat === '' || !is_numeric($lat)) $cellErrors[3] = "Must be a decimal (e.g. 12.1134455)";
                                    if ($lng === '' || !is_numeric($lng)) $cellErrors[4] = "Must be a decimal (e.g. 96.1134455)";
                                    
                                    if (empty($cellErrors)) {
                                        $validRows[] = [
                                            'row' => $rowNum,
                                            'data' => $data,
                                            'insert_data' => [
                                                'name' => $name, 'city_id' => $cityMap[$cityName], 'eco_rating' => $ecoRating,
                                                'lat' => $lat, 'lng' => $lng, 'description' => $desc
                                            ]
                                        ];
                                    }
                                } else {
                                    $lat = trim($data[2] ?? '');
                                    $lng = trim($data[3] ?? '');
                                    $desc = trim($data[4] ?? '');
                                    
                                    if ($lat === '' || !is_numeric($lat)) $cellErrors[2] = "Must be a decimal (e.g. 12.1134455)";
                                    if ($lng === '' || !is_numeric($lng)) $cellErrors[3] = "Must be a decimal (e.g. 96.1134455)";
                                    
                                    if (empty($cellErrors)) {
                                        $validRows[] = [
                                            'row' => $rowNum,
                                            'data' => $data,
                                            'insert_data' => [
                                                'name' => $name, 'city_id' => $cityMap[$cityName],
                                                'lat' => $lat, 'lng' => $lng, 'description' => $desc
                                            ]
                                        ];
                                    }
                                }
                                
                                if (!empty($cellErrors)) {
                                    $failedRows[] = [
                                        'row' => $rowNum,
                                        'data' => $data,
                                        'errors' => $cellErrors
                                    ];
                                }
                                $rowNum++;
                            }
                            
                            $_SESSION['import_preview'] = [
                                'type' => $isHotel ? 'hotels' : 'landmarks',
                                'validRows' => $validRows,
                                'failedRows' => $failedRows
                            ];
                            
                            header('Location: ' . URLROOT . '/planner/importPreview');
                            exit;
                        } catch (Exception $e) {
                            $_SESSION['import_errors'] = ["Error parsing Excel file: " . $e->getMessage()];
                        }
                    } else {
                        $_SESSION['import_errors'] = ["Failed to upload file."];
                    }
                }
                
                $redirectQuery = isset($_GET['show_inactive']) ? '?show_inactive=1&success=1' : '?success=1';
                header('Location: ' . URLROOT . '/planner/infrastructure' . $redirectQuery);
                exit;
            }
        }

        $showInactive = isset($_GET['show_inactive']) ? 1 : 0;
        $isActiveFilter = $showInactive ? 0 : 1;

        $data = [
            'title' => 'Infrastructure',
            'hotels' => $infrastructureModel->getAllHotels($isActiveFilter),
            'landmarks' => $infrastructureModel->getAllLandmarks($isActiveFilter),
            'buses' => $infrastructureModel->getAllBuses(),
            'regions' => $infrastructureModel->getAllRegions(),
            'cities' => $infrastructureModel->getAllCities(),
            'showInactive' => $showInactive
        ];

        $this->view('planner/infrastructure', $data);
    }
    
    public function importPreview() {
        if (!isset($_SESSION['import_preview'])) {
            header('Location: ' . URLROOT . '/planner/infrastructure');
            exit;
        }
        
        $data = [
            'title' => 'Import Preview',
            'preview' => $_SESSION['import_preview']
        ];
        $this->view('planner/import_preview', $data);
    }

    public function importConfirm() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'confirm_import' && isset($_SESSION['import_preview'])) {
                $infrastructureModel = $this->model('Infrastructure');
                $previewData = $_SESSION['import_preview'];
                $validRows = $previewData['validRows'];
                
                foreach ($validRows as $row) {
                    if ($previewData['type'] == 'hotels') {
                        $infrastructureModel->createHotel($row['insert_data']);
                    } else {
                        $infrastructureModel->createLandmark($row['insert_data']);
                    }
                }
                $_SESSION['import_success'] = "Successfully imported " . count($validRows) . " records.";
                unset($_SESSION['import_preview']);
            } elseif ($_POST['action'] == 'cancel_import') {
                unset($_SESSION['import_preview']);
            }
        }
        header('Location: ' . URLROOT . '/planner/infrastructure?success=1');
        exit;
    }

    public function locations() {
        $infrastructureModel = $this->model('Infrastructure');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
            if ($_POST['action'] == 'add_region') {
                $infrastructureModel->createRegion($_POST);
            } elseif ($_POST['action'] == 'add_city') {
                $infrastructureModel->createCity($_POST);
            } elseif ($_POST['action'] == 'update_region') {
                $infrastructureModel->updateRegion($_POST);
            } elseif ($_POST['action'] == 'update_city') {
                $infrastructureModel->updateCity($_POST);
            }
            header('Location: ' . URLROOT . '/planner/locations?success=1');
            exit;
        }

        $regions = $infrastructureModel->getAllRegions();
        $cities = $infrastructureModel->getAllCities();

        $data = [
            'title' => 'Manage Locations',
            'regions' => $regions,
            'cities' => $cities
        ];

        $this->view('planner/locations', $data);
    }

    public function import_cities_temp() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == UPLOAD_ERR_OK) {
            $infrastructureModel = $this->model('Infrastructure');
            
            try {
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($_FILES['excel_file']['tmp_name']);
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($_FILES['excel_file']['tmp_name']);
                $worksheet = $spreadsheet->getActiveSheet();
                
                $regions = $infrastructureModel->getAllRegions();
                $regionMap = [];
                foreach ($regions as $r) {
                    $regionMap[strtolower(trim($r->RegionName))] = $r->RegionID;
                }

                $cities = $infrastructureModel->getAllCities();
                $cityMap = [];
                foreach ($cities as $c) {
                    $cityMap[strtolower(trim($c->CityName))] = true;
                }
                
                $rowsToImport = [];
                $missingRegions = [];
                $existingCities = [];

                $rowNum = 1;
                foreach ($worksheet->getRowIterator() as $row) {
                    if ($rowNum == 1) { // Skip header
                        $rowNum++;
                        continue;
                    }
                    
                    $cellIterator = $row->getCellIterator();
                    $cellIterator->setIterateOnlyExistingCells(false);
                    $data = [];
                    foreach ($cellIterator as $cell) {
                        $data[] = $cell->getValue();
                    }
                    
                    $cityName = trim($data[0] ?? '');
                    $regionName = trim($data[1] ?? '');
                    
                    if (!empty($cityName) && !empty($regionName)) {
                        $cityKey = strtolower($cityName);
                        $regionKey = strtolower($regionName);
                        
                        if (isset($cityMap[$cityKey])) {
                            $existingCities[] = $cityName;
                        }
                        
                        if (!isset($regionMap[$regionKey])) {
                            $missingRegions[] = $regionName;
                        } 
                        
                        if (!isset($cityMap[$cityKey]) && isset($regionMap[$regionKey])) {
                            $rowsToImport[] = [
                                'name' => $cityName,
                                'region_id' => $regionMap[$regionKey]
                            ];
                        }
                    }
                    $rowNum++;
                }

                if (!empty($missingRegions) || !empty($existingCities)) {
                    $errorUrl = '/planner/locations?error=import_failed';
                    if (!empty($missingRegions)) {
                        $errorUrl .= '&missing_regions=' . urlencode(implode(', ', array_unique($missingRegions)));
                    }
                    if (!empty($existingCities)) {
                        $errorUrl .= '&existing_cities=' . urlencode(implode(', ', array_unique($existingCities)));
                    }
                    header('Location: ' . URLROOT . $errorUrl);
                    exit;
                }

                // If all good, import
                foreach ($rowsToImport as $importData) {
                    $infrastructureModel->createCity($importData);
                }
            } catch (\Exception $e) {
                // Silent fail for temp tool
            }
        }
        header('Location: ' . URLROOT . '/planner/locations?success=imported');
        exit;
    }

    public function bus_seats($busId) {
        $infrastructureModel = $this->model('Infrastructure');
        $bus = $infrastructureModel->getBusById($busId);
        $seats = $infrastructureModel->getSeatsByBusId($busId);
        header('Content-Type: application/json');
        echo json_encode([
            'bus' => $bus,
            'seats' => $seats
        ]);
        exit;
    }
}
