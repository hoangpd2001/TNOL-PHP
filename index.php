<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load(); // ✅ Load tại đây 1 lần là đủ cho toàn hệ thống

require_once "./mvc/Bridge.php";

$myApp = new App();
