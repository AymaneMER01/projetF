<?php
// controllers/UserController.php
require_once __DIR__ . '/../models/User.php';

class UserController {
    // Inscription d'un utilisateur
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            $role     = $_POST['role']; // 'etudiant', 'professionnel', ou 'admin'

            if (empty($username) || empty($password)) {
                $error = "Veuillez remplir tous les champs.";
                include ROOT_DIR . '/views/register.php';
                return;
            }
            
            $userId = User::register($username, $password, $role);
            if ($userId) {
                redirect('router.php?action=login');
            } else {
                $error = "Erreur lors de l'inscription. Veuillez réessayer.";
                include ROOT_DIR . '/views/register.php';
            }
        } else {
            include ROOT_DIR . '/views/register.php';
        }
    }

    // Connexion d'un utilisateur
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username']);
            $password = $_POST['password'];
            
            $user = User::login($username, $password);
            if ($user) {
                // Stocker uniquement l'ID de l'utilisateur dans la session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['role'] = $user->role;
                
                redirect('router.php?action=dashboard');
            } else {
                $error = "Identifiants incorrects.";
                include ROOT_DIR . '/views/login.php';
            }
        } else {
            include ROOT_DIR . '/views/login.php';
        }
    }
    
    // Déconnexion d'un utilisateur
    public function logout() {
        session_destroy();
        redirect('router.php?action=login');
    }
    
    // Récupère l'utilisateur actuellement connecté
    public static function getCurrentUser() {
        if (isset($_SESSION['user_id'])) {
            return User::findById($_SESSION['user_id']);
        }
        return null;
    }
}
