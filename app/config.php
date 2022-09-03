<?php
require_once(__DIR__ . '/vendor/autoload.php');
ob_start();
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

// print_r($_SESSION);
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set("Asia/Tokyo");
try {
    $con = new PDO("mysql:dbname=".$_ENV['DB_NAME'].";host=".$_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
    echo "Connection failed:" . $e->getMessage();
}