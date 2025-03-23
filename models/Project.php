<?php
// models/Project.php
require_once __DIR__ . '/../config/database.php';

class Project {
    public $id;
    public $title;
    public $description;
    public $owner_id;
    public $created_at;
    public $updated_at;
    
    public function __construct($id, $title, $description, $owner_id, $created_at, $updated_at) {
        $this->id          = $id;
        $this->title       = $title;
        $this->description = $description;
        $this->owner_id    = $owner_id;
        $this->created_at  = $created_at;
        $this->updated_at  = $updated_at;
    }
    
    // Création d'un nouveau projet
    public static function create($title, $description, $owner_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, owner_id) VALUES (:title, :description, :owner_id)");
            $stmt->execute([
                ':title'       => $title,
                ':description' => $description,
                ':owner_id'    => $owner_id
            ]);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Recherche d'un projet par son ID
    public static function findById($id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $project = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($project) {
                return new Project(
                    $project['id'],
                    $project['title'],
                    $project['description'],
                    $project['owner_id'],
                    $project['created_at'],
                    $project['updated_at']
                );
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Trouver tous les projets d'un membre
    public static function findByMember($user_id) {
        global $pdo;
        try {
            // On cherche les projets dont l'utilisateur est membre via la table project_members
            $stmt = $pdo->prepare("
                SELECT p.* FROM projects p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = :user_id
                UNION
                SELECT * FROM projects
                WHERE owner_id = :owner_id
                ORDER BY created_at DESC
            ");
            $stmt->execute([
                ':user_id' => $user_id,
                ':owner_id' => $user_id
            ]);
            
            $projects = [];
            while ($project = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $projects[] = new Project(
                    $project['id'],
                    $project['title'],
                    $project['description'],
                    $project['owner_id'],
                    $project['created_at'],
                    $project['updated_at']
                );
            }
            return $projects;
        } catch (PDOException $e) {
            return [];
        }
    }
}
