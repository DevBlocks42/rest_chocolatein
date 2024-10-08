<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once("Url.php");
include_once("Control.php");
$url = Url::getInstance();
$control = new Control();

if(!$url->authentication())
{
    $control->unauthorized();
}
else 
{
    $httpMethod = $url->getHTTPMethod();
    $table = $url->getVariable("table");
    $id = $url->getVariable("id");
    $fields = $url->getVariable("fields", "json");
    $control->request($httpMethod, $table, $id, $fields);
}

