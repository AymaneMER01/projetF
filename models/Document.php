<?php
// models/Document.php
require_once __DIR__ . '/../config/database.php';

class Document {
    public $id;
    public $project_id;
    public $filename;
    public $file_path;
    public $uploaded_by;
    public $uploaded_at;
    
    public function __construct($id, $project_id, $filename, $file_path, $uploaded_by, $uploaded_at) {
        $this->id          = $id;
        $this->project_id  = $project_id;
        $this->filename    = $filename;
        $this->file_path   = $file_path;
        $this->uploaded_by = $uploaded_by;
        $this->uploaded_at = $uploaded_at;
    }
    
    // Ajout d'un document à un projet
    public static function create($project_id, $filename, $file_path, $uploaded_by) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO documents (project_id, filename, file_path, uploaded_by) VALUES (:project_id, :filename, :file_path, :uploaded_by)");
            $stmt->execute([
                ':project_id'  => $project_id,
                ':filename'    => $filename,
                ':file_path'   => $file_path,
                ':uploaded_by' => $uploaded_by
            ]);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer tous les documents d'un projet
    public static function findByProject($project_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT * FROM documents WHERE project_id = :project_id");
            $stmt->execute([':project_id' => $project_id]);
            $documents = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $documents[] = new Document(
                    $row['id'],
                    $row['project_id'],
                    $row['filename'],
                    $row['file_path'],
                    $row['uploaded_by'],
                    $row['uploaded_at']
                );
            }
            return $documents;
        } catch (PDOException $e) {
            return [];
        }
    }
}
