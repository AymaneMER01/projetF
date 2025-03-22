<?php
// models/User.php
require_once __DIR__ . '/../config/database.php';

class User {
    public $id;
    public $username;
    public $password;
    public $role;
    public $created_at;
    
    public function __construct($id, $username, $password, $role, $created_at) {
        $this->id         = $id;
        $this->username   = $username;
        $this->password   = $password;
        $this->role       = $role;
        $this->created_at = $created_at;
    }
    
    // Inscription d'un nouvel utilisateur
    public static function register($username, $password, $role) {
        global $pdo;
        // Hachage du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
                ':role'     => $role
            ]);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Connexion d'un utilisateur
    public static function login($username, $password) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                return new User($user['id'], $user['username'], $user['password'], $user['role'], $user['created_at']);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Recherche d'un utilisateur par son ID
    public static function findById($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                return new User($user['id'], $user['username'], $user['password'], $user['role'], $user['created_at']);
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer tous les utilisateurs
    public static function getAll() {
        global $pdo;
        try {
            $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY username");
            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'role' => $row['role'],
                    'created_at' => $row['created_at']
                ];
            }
            return $users;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Rechercher des utilisateurs par nom (recherche partielle)
    public static function searchByUsername($search) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT id, username, role, created_at FROM users WHERE username LIKE :search ORDER BY username");
            $stmt->execute([':search' => '%' . $search . '%']);
            $users = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $users[] = [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'role' => $row['role'],
                    'created_at' => $row['created_at']
                ];
            }
            return $users;
        } catch (PDOException $e) {
            return [];
        }
    }
}
