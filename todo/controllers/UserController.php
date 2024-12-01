<?php

require_once __DIR__ . '/../models/UserModel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Please fill in all fields';
                header('Location: index.php?action=register');
                exit;
            }

            if ($this->userModel->createUser($username, $password)) {
                $_SESSION['success'] = 'Registration successful! Please login.';
                header('Location: index.php?action=login');
                exit;
            } else {
                $_SESSION['error'] = 'Username already exists';
                header('Location: index.php?action=register');
                exit;
            }
        }

        require __DIR__ . '/../views/register.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                $_SESSION['error'] = 'Please fill in all fields';
                header('Location: index.php?action=login');
                exit;
            }

            $user = $this->userModel->verifyUser($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: index.php');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid username or password';
                header('Location: index.php?action=login');
                exit;
            }
        }

        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?action=login');
        exit;
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}

?>
