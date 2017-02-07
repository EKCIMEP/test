<?php
namespace App;
session_start();

class Autoloader
{
    public function __construct(){}

    public static function autoload($file)
    {
        if($file !== 'DefaultController'){
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $file) . '.php';
        } else {
            $filePath = '../Controller/' . str_replace('\\', '/', $file) . '.php';
        }
        if (file_exists($filePath)) {
            include($filePath);
        } else {

        }
    }
}
spl_autoload_extensions('*.php');
spl_autoload_register('App\Autoloader::autoload');