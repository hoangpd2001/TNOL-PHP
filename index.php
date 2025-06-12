<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php'; 
require_once "./mvc/Bridge.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$myApp = new App();
