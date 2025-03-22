<?php
$host = 'localhost';
$dbname = 'projet';
$user = 'root';
$password = '';
$dsn = "mysql:host=$host;dbname=$dbname";

try {
    // Création de l'objet PDO pour la connexion
    $pdo = new PDO($dsn, $user, $password);
    // Configuration pour lever les exceptions en cas d'erreur
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    if($pdo){
        
    }
} catch (PDOException $e) {
    
    $error_conn = $e -> getMessage();
    echo $error_conn;
    exit();
}
?>
