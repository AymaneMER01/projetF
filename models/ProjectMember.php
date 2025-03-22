<?php
// models/ProjectMember.php
require_once __DIR__ . '/../config/database.php';

class ProjectMember {
    public $project_id;
    public $user_id;
    public $role;
    
    public function __construct($project_id, $user_id, $role) {
        $this->project_id = $project_id;
        $this->user_id    = $user_id;
        $this->role       = $role;
    }
    
    // Ajouter un membre à un projet
    public static function addMember($project_id, $user_id, $role = 'collaborateur') {
        global $pdo;
        try {
            // Vérifier si l'utilisateur est déjà membre du projet
            if (self::isMember($project_id, $user_id)) {
                return false;
            }
            
            $stmt = $pdo->prepare("INSERT INTO project_members (project_id, user_id, role) VALUES (:project_id, :user_id, :role)");
            return $stmt->execute([
                ':project_id' => $project_id,
                ':user_id'    => $user_id,
                ':role'       => $role
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer les membres d'un projet
    public static function getMembers($project_id) {
        global $pdo;
        try {
            // Jointure avec la table users pour récupérer le nom d'utilisateur
            $stmt = $pdo->prepare("
                SELECT pm.*, u.username 
                FROM project_members pm
                LEFT JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = :project_id
            ");
            $stmt->execute([':project_id' => $project_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Vérifier si un utilisateur est membre d'un projet
    public static function isMember($project_id, $user_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM project_members WHERE project_id = :project_id AND user_id = :user_id");
            $stmt->execute([
                ':project_id' => $project_id,
                ':user_id' => $user_id
            ]);
            return ($stmt->fetchColumn() > 0);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer le rôle d'un membre dans un projet
    public static function getMemberRole($project_id, $user_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT role FROM project_members WHERE project_id = :project_id AND user_id = :user_id");
            $stmt->execute([
                ':project_id' => $project_id,
                ':user_id' => $user_id
            ]);
            $role = $stmt->fetchColumn();
            return $role ? $role : false;
        } catch (PDOException $e) {
            return false;
        }
    }
}
