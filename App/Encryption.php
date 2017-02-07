<?php

namespace App;

class Encryption
{
    private static $algo = '$2a';

    private static $cost = '$10';


    public static function encryptionCrypt($password)
    {
        $hash = self::hash($password);

        return $hash;
    }

    private static function uniqueSalt()
    {
        return substr(sha1(mt_rand()), 0, 22);
    }

    private static function hash($password)
    {
        return crypt($password,
            self::$algo .
            self::$cost .
            '$' . self::uniqueSalt());

    }

    public static function checkPassword($hash, $password)
    {
        $fullSalt = substr($hash, 0, 29);

        $newHash = crypt($password, $fullSalt);

        return ($hash == $newHash);
    }


}