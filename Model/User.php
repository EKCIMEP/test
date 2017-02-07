<?php
namespace Model;

use App\DataBase;

class User extends DataBase
{
    protected $mysqli;

    public function __construct()
    {
        $this->mysqli = parent::getInstance()->getConnection();
    }

    public function create($name, $password, $file)
    {
        $name = $this->mysqli->real_escape_string($name);
        $query = 'INSERT INTO `user`(`nickname`, `password`, `avatar_url`) VALUES' .
            '("' . $name . '", "' . $password . '", "' . $file . '")';

        return $this->mysqli->query($query);
    }

    public function find($name)
    {
        $name = $this->mysqli->real_escape_string($name);
        $query = 'SELECT * FROM `user` WHERE `nickname`="' . $name . '"';

        return $this->mysqli->query($query)->fetch_array(MYSQLI_ASSOC);
    }

    public function lastInsertId()
    {
        return $this->mysqli->insert_id;
    }
}