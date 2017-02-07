<?php

namespace Controller;

use App\Router;

class Controller
{
    const ONE_HOUR = 3600;
    const LOGIN = '/login';
    const REGISTRATION = '/registration';
    const REDIRECT_STATUS = 301;

    protected $data = [];
    protected $rootDir = __DIR__;

    private $_params;

    public function __construct()
    {
        $this->_params = json_decode(file_get_contents($this->rootDir . '/../App/config/parameters.json'), true);

        $this->_checkAuth();
    }

    public function getParameters(string $fieldName)
    {
        return $this->_params[$fieldName];
    }


    public function getUser()
    {
        $secretKey = $this->getParameters('secret_key');
        $key = hash("sha256", $secretKey);
        $iv = substr(hash("sha256", $this->getParameters('iv')), 0, 16);

        $data = json_decode(
            openssl_decrypt($_COOKIE['token'], 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv),
            true
        );

        return $data['user'];
    }

    private function _checkAuth()
    {
        if ((isset($_SESSION['token']) && isset($_COOKIE['token'])) &&
            $_SESSION['token'] == $_COOKIE['token']) {
            $secretKey = $this->getParameters('secret_key');
            $key = hash("sha256", $secretKey);
            $iv = substr(hash("sha256", $this->getParameters('iv')), 0, 16);

            $data = json_decode(
                openssl_decrypt($_COOKIE['token'], 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv),
                true
            );

            if ($data['exp'] < time()) {
                $_SESSION['token'] = '';
                unset($_COOKIE['token']);
                setcookie('token', '', time() - self::ONE_HOUR, '/');
                unset($_SESSION['token']);

                Router::redirectTo(self::LOGIN, self::REDIRECT_STATUS);
            } else if ($_SERVER['REQUEST_URI'] === self::LOGIN || $_SERVER['REQUEST_URI'] === self::REGISTRATION) {
                Router::redirectTo('/', self::REDIRECT_STATUS);
            }
        } else if ($_SERVER['REQUEST_URI'] !== self::LOGIN && $_SERVER['REQUEST_URI'] !== self::REGISTRATION){
            $_SESSION['token'] = '';
            unset($_COOKIE['token']);
            setcookie('token', '', time() - self::ONE_HOUR, '/');
            unset($_SESSION['token']);
            Router::redirectTo(self::LOGIN, self::REDIRECT_STATUS);
        }
    }
}