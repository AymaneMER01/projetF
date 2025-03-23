<?php
// models/Task.php
require_once __DIR__ . '/../config/database.php';

class Task {
    public $id;
    public $project_id;
    public $title;
    public $description;
    public $status;
    public $due_date;
    public $created_at;
    public $updated_at;
    public $assigned_to; // ID de l'utilisateur assigné
    public $assigned_username; // Nom de l'utilisateur assigné
    
    public function __construct($id, $project_id, $title, $description, $status, $due_date, $created_at, $updated_at, $assigned_to = null, $assigned_username = null) {
        $this->id               = $id;
        $this->project_id       = $project_id;
        $this->title            = $title;
        $this->description      = $description;
        $this->status           = $status;
        $this->due_date         = $due_date;
        $this->created_at       = $created_at;
        $this->updated_at       = $updated_at;
        $this->assigned_to      = $assigned_to;
        $this->assigned_username = $assigned_username;
    }
    
    // Création d'une nouvelle tâche
    public static function create($project_id, $title, $description, $due_date, $assigned_to = null) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tasks 
                (project_id, title, description, status, due_date, assigned_to) 
                VALUES 
                (:project_id, :title, :description, 'À faire', :due_date, :assigned_to)
            ");
            $stmt->execute([
                ':project_id'  => $project_id,
                ':title'       => $title,
                ':description' => $description,
                ':due_date'    => $due_date,
                ':assigned_to' => $assigned_to
            ]);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Mise à jour du statut d'une tâche
    public static function updateStatus($task_id, $status) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET status = :status, updated_at = NOW() WHERE id = :task_id");
            return $stmt->execute([
                ':status'  => $status,
                ':task_id' => $task_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Attribution d'une tâche à un utilisateur
    public static function assignTask($task_id, $user_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("UPDATE tasks SET assigned_to = :user_id, updated_at = NOW() WHERE id = :task_id");
            return $stmt->execute([
                ':user_id' => $user_id,
                ':task_id' => $task_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer une tâche par son ID
    public static function findById($task_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT t.*, u.username as assigned_username 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.id = :task_id
            ");
            $stmt->execute([':task_id' => $task_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return new Task(
                    $row['id'],
                    $row['project_id'],
                    $row['title'],
                    $row['description'],
                    $row['status'],
                    $row['due_date'],
                    $row['created_at'],
                    $row['updated_at'],
                    $row['assigned_to'],
                    $row['assigned_username']
                );
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer toutes les tâches d'un projet
    public static function findByProject($project_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT t.*, u.username as assigned_username 
                FROM tasks t 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.project_id = :project_id
                ORDER BY 
                    CASE t.status
                        WHEN 'À faire' THEN 1
                        WHEN 'En cours' THEN 2
                        WHEN 'Terminé' THEN 3
                        ELSE 4
                    END,
                    t.due_date IS NULL, t.due_date ASC, t.title
            ");
            $stmt->execute([':project_id' => $project_id]);
            $tasks = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = new Task(
                    $row['id'],
                    $row['project_id'],
                    $row['title'],
                    $row['description'],
                    $row['status'],
                    $row['due_date'],
                    $row['created_at'],
                    $row['updated_at'],
                    $row['assigned_to'],
                    $row['assigned_username']
                );
            }
            return $tasks;
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Vérifier si l'utilisateur a les droits de modification sur une tâche
    public static function canUpdate($task_id, $user_id) {
        global $pdo;
        try {
            // D'abord, récupérer la tâche et le projet associé
            $stmt = $pdo->prepare("SELECT project_id FROM tasks WHERE id = :task_id");
            $stmt->execute([':task_id' => $task_id]);
            $task = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$task) {
                return false;
            }
            
            // Ensuite, vérifier si l'utilisateur est propriétaire du projet
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM project_members 
                WHERE project_id = :project_id AND user_id = :user_id AND role = 'propriétaire'
            ");
            $stmt->execute([
                ':project_id' => $task['project_id'],
                ':user_id' => $user_id
            ]);
            
            return ($stmt->fetchColumn() > 0);
        } catch (PDOException $e) {
            return false;
        }
    }
}
