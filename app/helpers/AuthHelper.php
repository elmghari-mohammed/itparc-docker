<?php
require_once(__DIR__ . '/../models/Database.php');

class AuthHelper {
    public static function findUserByEmail($email) {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare("CALL VerifierEmailExistence(:email)");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if (isset($result['result'])) {
            list($role, $id) = explode(':', $result['result']);
            if ($role !== 'aucune' && $id !== 'aucune') {
                return ['role' => $role, 'id' => $id];
            }
        }
        return null;
    }

    public static function verifyPassword($role, $id, $password) {
        $pdo = Database::getInstance();
        $hashed = hash('sha256', $password);
        $sql = "SELECT * FROM $role WHERE matricul = :id LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && $user['password'] === $hashed) {
            if (!empty($user['bloque'])) {
                return ['error' => "Compte bloqué"];
            }
            // Session data (tu peux ajouter plus selon besoin)
            $_SESSION['id'] = $user['matricul'];
            $_SESSION['role'] = $role;
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            return ['success' => true, 'role' => $role];
        }
        return ['error' => "Mot de passe incorrect"];
    }

    public static function isAuthenticated() {
        return isset($_SESSION['id']) && isset($_SESSION['role']);
    }

    public static function logout() {
        session_destroy();
    }
}
?>