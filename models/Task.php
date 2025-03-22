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
    
    public function __construct($id, $project_id, $title, $description, $status, $due_date, $created_at, $updated_at) {
        $this->id          = $id;
        $this->project_id  = $project_id;
        $this->title       = $title;
        $this->description = $description;
        $this->status      = $status;
        $this->due_date    = $due_date;
        $this->created_at  = $created_at;
        $this->updated_at  = $updated_at;
    }
    
    // Création d'une nouvelle tâche
    public static function create($project_id, $title, $description, $due_date) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO tasks (project_id, title, description, due_date) VALUES (:project_id, :title, :description, :due_date)");
            $stmt->execute([
                ':project_id'  => $project_id,
                ':title'       => $title,
                ':description' => $description,
                ':due_date'    => $due_date
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
            $stmt = $pdo->prepare("UPDATE tasks SET status = :status WHERE id = :task_id");
            return $stmt->execute([
                ':status'  => $status,
                ':task_id' => $task_id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer toutes les tâches d'un projet
    public static function findByProject($project_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM tasks WHERE project_id = :project_id");
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
                    $row['updated_at']
                );
            }
            return $tasks;
        } catch (PDOException $e) {
            return [];
        }
    }
}
