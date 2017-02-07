<?php
namespace Controller;

use Model\User as User;
use App\Encryption as Encrypt;
use App\Router;

class AuthController extends Controller
{
    const FILE_ERROR      = 4;
    const MAX_NAME_LENGTH = 64;
    const MAX_FILE_SIZE   = 5000000;

    protected $data = [];

    public function loginAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User();

            $nickname = $_POST['nickname'];
            $password = $_POST['password'];

            $result = $user->find($nickname);

            if ($result) {
                if (Encrypt::checkPassword($result['password'], $password)) {
                    $_SESSION['id'] = $result['id'];
                    $this->generatedToken($nickname);
                }
            } else {
                $this->data['errors'] = $this->getParameters('error_login');
            }
        }

        $this->data['template'] = $this->rootDir . '/../View/login.html';

        extract($this->data);

        return include($this->rootDir . '/../View/index.html');
    }

    public function registrationAction()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$this->data['errors'] = $this->validateForm($_POST, $_FILES)) {
            $user = new User();

            $nickname = $_POST['nickname'];
            $password = Encrypt::encryptionCrypt($_POST['password']);
            $file = '';

            if ($_FILES['avatar']['error'] !== self::FILE_ERROR) {
                $uploadDir = $this->rootDir . '/../public/';
                $uploadFile = str_replace('\\', '/', $uploadDir . basename($_FILES['avatar']['name']));

                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                    $file = '/public/images/' . $_FILES['avatar']['name'];
                } else {
                    throw new \Exception($this->getParameters('upload_file_error'));
                }
            }

            if (!$user->find($nickname) && $user->create($nickname, $password, $file)) {
                $_SESSION['id'] = $user->lastInsertId();
                $this->generatedToken($nickname);
            } else {
                throw new \Exception($this->getParameters('server_error'));
            }
        }

        $this->data['template'] = $this->rootDir . '/../View/registration.html';

        extract($this->data);

        return include($this->rootDir . '/../View/index.html');
    }

    protected function validateForm(array $data, array $file)
    {
        $error = [];

        if (empty($data['nickname'])) {
            $error['nickname'] = $this->getParameters('nickname_error');
        } else if (strlen($data['nickname']) > self::MAX_NAME_LENGTH) {
            $error['nickname'] = $this->getParameters('nickname_max_length_error');
        }

        if (empty($data['password'])) {
            $error['password'] = $this->getParameters('password_error');
        }

        if ($file['avatar']['error'] !== self::FILE_ERROR) {
            list($width, $height) = getimagesize($file['avatar']['tmp_name']);
            if ($width == null && $height == null) {
                $error['image'] = $this->getParameters('file_type_error');
            } else if ($file['avatar']['size'] > self::MAX_FILE_SIZE) {
                $error['image'] = $this->getParameters('max_file_size_error');
            }
        }

        return $error;
    }

    protected function generatedToken($user)
    {
        $secretKey = $this->getParameters('secret_key');
        $key = hash("sha256", $secretKey);
        $iv = substr(hash("sha256", $this->getParameters('iv')), 0, 16);

        $data = [
            'set'  => time(),
            'exp'  => time() + 3540,
            'user' => $user,
        ];

        $cipherText = openssl_encrypt(json_encode($data), 'AES-128-CBC', $key, true, $iv);

        $_SESSION['token'] = $cipherText;
        setcookie("token", $cipherText, time() + 3600, '/');

        Router::redirectTo('/', '301');
    }
}