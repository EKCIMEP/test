<?php
require_once(__DIR__.'/App/Autoloader.php');
use App\Router as Route;

$route = new Route();
$route->execute($_SERVER);