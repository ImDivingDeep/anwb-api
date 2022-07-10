<?php
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

header('Content-Type: application/json; charset=utf-8');

require_once "TrafficModel.php";

$TrafficModel = new TrafficModel();

$request = $_SERVER['REQUEST_URI'];

if (array_key_exists("date_from", $_GET))
{
    $time = date_parse($_GET["date_from"]);
    echo json_encode($TrafficModel->getTrafficByDate($_GET["date_from"]));
    return;
}

echo json_encode($TrafficModel->getTraffic());