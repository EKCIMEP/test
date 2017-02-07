<?php
namespace App;

class DataBase
{
    private static $_instance;

    private $connection;

    private function __clone(){}

    private function __construct()
    {
        $config = json_decode(file_get_contents(__DIR__.'/config/config.json'), true);

        $this->connection = mysqli_connect(
            $config['database']['host'],
            $config['database']['user'],
            $config['database']['password'],
            $config['database']['name'],
            $config['database']['port']
        );

        $this->connection->query('SET NAMES utf8');

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
    }

    public static function getInstance()
    {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}