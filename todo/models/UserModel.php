<?php

class UserModel {
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../data/users.json';
    }

    public function getUsers() {
        if (file_exists($this->filePath)) {
            $jsonData = file_get_contents($this->filePath);
            return json_decode($jsonData, true) ?? [];
        }
        return [];
    }

    public function saveUsers($users) {
        file_put_contents($this->filePath, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function findUser($username) {
        $users = $this->getUsers();
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    public function createUser($username, $password) {
        $users = $this->getUsers();
        
        // Check if username already exists
        if ($this->findUser($username)) {
            return false;
        }

        // Hash password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $users[] = [
            'id' => uniqid(),
            'username' => $username,
            'password' => $hashedPassword
        ];

        $this->saveUsers($users);
        return true;
    }

    public function verifyUser($username, $password) {
        $user = $this->findUser($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }
}

?>
