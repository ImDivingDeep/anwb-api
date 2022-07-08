<?php
header('Content-Type: application/json; charset=utf-8');
require_once "JamsModel.php";

$jamsModel = new JamsModel();

$request = $_SERVER['REQUEST_URI'];

if (array_key_exists("date_from", $_GET))
{
    $time = date_parse($_GET["date_from"]);
    echo json_encode($jamsModel->getJamsByDate($_GET["date_from"]));
    return;
}

echo json_encode($jamsModel->getJams());