<?php
ob_start();
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', '1');

// print_r($_SESSION);

date_default_timezone_set("Asia/Tokyo");
try {
    $con = new PDO("mysql:dbname=doodle;host=mysql", "doodle", "root");
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
} catch (PDOException $e) {
    echo "Connection failed:" . $e->getMessage();
}
