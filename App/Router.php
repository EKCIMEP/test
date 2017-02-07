<?php
namespace App;

class Router
{

    protected static $router;

    public function __construct()
    {
        self::$router = include(dirname(__DIR__) . '/route/routing.php');
    }

    public function execute($request)
    {
        $result = array_filter(self::$router, function ($data) use ($request) {
            if ($request['REQUEST_URI'] === $data['route']) {
                return true;
            } else {
                if (!stristr($data['route'], '{', true) && !stristr($data['route'], '}', true)) {
                    return false;
                } else {
                    $requestData = explode('/', $request['REQUEST_URI']);
                    $settingData = explode('/', $data['route']);

                    if (sizeof($requestData) === sizeof($settingData)) {
                        $result = array_combine($requestData, $settingData);
                        $str = implode('/', array_keys($result));

                        if ($request['REQUEST_URI'] === $str) {
                            return true;
                        }
                    } else {
                        return false;
                    }
                }
            }
        });

        if (empty($result)) {
            throw new \Exception("Error in " . __CLASS__);
        }

        $setting = $this->parse($result);

        $str = str_replace('/', '\\', $setting['controller']);

        $controller = new $str;

        call_user_func_array(array($controller, $setting['action']."Action"), []);
    }

    public static function redirectTo($route, $status)
    {
        header('HTTP/1.1 '.$status.' Moved Permanently');
        header('Location: '.$route);
        exit();
    }

    protected function parse(array $array)
    {
        $result = [];

        foreach ($array as $value) {
            $result = $value;
        }

        return $result;
    }
}