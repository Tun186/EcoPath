<?php

// Detect if running on localhost or production
$isLocal = in_array($_SERVER['HTTP_HOST'], ['localhost', '127.0.0.1']) || strpos($_SERVER['HTTP_HOST'], 'localhost:') !== false;

if ($isLocal) {
    // Local Database Credentials
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'ecopath_db');
} else {
    // Production Database Credentials (ezyro)
    define('DB_HOST', 'sql300.ezyro.com');
    define('DB_USER', 'ezyro_42123151');
    define('DB_PASS', 'Leeleelee2020');
    define('DB_NAME', 'ezyro_42123151_ecopath');
}

define('APPROOT', dirname(dirname(__FILE__)));

// Dynamic URLROOT (Auto-detects path on both Local and Production)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptPath === '/' || $scriptPath === '\\') {
    $scriptPath = '';
}
define('URLROOT', $protocol . '://' . $_SERVER['HTTP_HOST'] . str_replace('\\', '/', $scriptPath));
define('SITENAME', 'EcoPath');

// Gemini API Key for Driver License OCR
define('GEMINI_API_KEY', 'AQ.Ab8RN6L7-wH1xHwY85wO6t135l5nGZYJ5K0bIj42SNOi47lIAA');


// OAuth Credentials
define('GOOGLE_CLIENT_ID', '864153578820-2nacn28icq6mk5r42pnt69t8kplr25qo.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
define('FACEBOOK_CLIENT_ID', '1424252548806029');
define('FACEBOOK_CLIENT_SECRET', '047d5083eb153f5e9f88edd6240d1799');

// SMTP Credentials
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'ecopath.project@gmail.com');
define('SMTP_PASS', 'icpb hklr lsft unit');
define('SMTP_PORT', 587);
