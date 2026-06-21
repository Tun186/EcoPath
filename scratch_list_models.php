<?php
require_once 'config/config.php';

echo "Listing models for v1beta...\n";
$url_beta = 'https://generativelanguage.googleapis.com/v1beta/models?key=' . GEMINI_API_KEY;
$ch = curl_init($url_beta);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $httpCode\n";
$data = json_decode($response, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        echo "- " . $m['name'] . " (" . implode(', ', $m['supportedGenerationMethods']) . ")\n";
    }
} else {
    echo "Response: " . $response . "\n";
}

echo "\nListing models for v1...\n";
$url_v1 = 'https://generativelanguage.googleapis.com/v1/models?key=' . GEMINI_API_KEY;
$ch = curl_init($url_v1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP $httpCode\n";
$data = json_decode($response, true);
if (isset($data['models'])) {
    foreach ($data['models'] as $m) {
        echo "- " . $m['name'] . " (" . implode(', ', $m['supportedGenerationMethods']) . ")\n";
    }
} else {
    echo "Response: " . $response . "\n";
}
