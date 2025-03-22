<?php
// Charger d'abord les modèles essentiels avant de démarrer la session
require_once __DIR__ . '/../models/User.php';

// Définir un gestionnaire de désérialisation pour les objets en session
function session_custom_handler($class_name) {
    // Charger d'abord dans les modèles
    $model_path = realpath(__DIR__ . '/../models/' . $class_name . '.php');
    if (file_exists($model_path)) {
        require_once $model_path;
        return;
    }
    
    // Ensuite dans les contrôleurs
    $controller_path = realpath(__DIR__ . '/../controllers/' . $class_name . '.php');
    if (file_exists($controller_path)) {
        require_once $controller_path;
        return;
    }
}

// Enregistrer le gestionnaire de désérialisation
spl_autoload_register('session_custom_handler');

// Démarrage de la session une seule fois
session_start();

// Définition des chemins de base de l'application
define('ROOT_DIR', realpath(__DIR__ . '/..'));
define('BASE_URL', '/projetF');

// Inclure la connexion à la base de données
require_once ROOT_DIR . '/config/database.php';

// Fonction pour charger automatiquement les classes
spl_autoload_register(function ($class_name) {
    // Chercher d'abord dans les modèles
    $model_path = ROOT_DIR . '/models/' . $class_name . '.php';
    if (file_exists($model_path)) {
        require_once $model_path;
        return;
    }
    
    // Ensuite dans les contrôleurs
    $controller_path = ROOT_DIR . '/controllers/' . $class_name . '.php';
    if (file_exists($controller_path)) {
        require_once $controller_path;
        return;
    }
});

// Fonction pour rediriger
function redirect($path) {
    header('Location: ' . BASE_URL . '/' . $path);
    exit;
}

// Fonction pour afficher les chemins d'assets
function asset($path) {
    return BASE_URL . '/assets/' . $path;
} 