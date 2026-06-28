<?php
if (file_exists('../vendor/autoload.php')) {
    require_once '../vendor/autoload.php';
}
require_once '../config/config.php';
require_once 'Helpers/MailHelper.php';
require_once 'Helpers/EmissionHelper.php';
require_once 'Core/App.php';
require_once 'Core/Controller.php';
require_once 'Core/Database.php';
