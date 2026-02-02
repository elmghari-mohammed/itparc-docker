<?php
require_once(__DIR__ . '/../helpers/AuthHelper.php');

class AuthController {
    public function login() {
        session_name(SESSION_NAME);
        session_start();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = AuthHelper::findUserByEmail($email);
            if ($user) {
                // Vérification mot de passe
                $result = AuthHelper::verifyPassword($user['role'], $user['id'], $password);
                if (!empty($result['success'])) {
                    header('Location: ' . BASE_URL . 'dashboard');
                    exit;
                } else {
                    $error = $result['error'];
                }
            } else {
                $error = "Email inconnu";
            }
        }
        require_once(__DIR__ . '/../views/auth/login.php');
    }

    public function logout() {
        session_name(SESSION_NAME);
        session_start();
        AuthHelper::logout();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
?>