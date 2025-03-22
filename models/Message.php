<?php
// models/Message.php
require_once __DIR__ . '/../config/database.php';

class Message {
    public $id;
    public $project_id;
    public $sender_id;
    public $sender_username;
    public $content;
    public $created_at;
    
    public function __construct($id, $project_id, $sender_id, $content, $created_at, $sender_username = null) {
        $this->id             = $id;
        $this->project_id     = $project_id;
        $this->sender_id      = $sender_id;
        $this->sender_username = $sender_username;
        $this->content        = $content;
        $this->created_at     = $created_at;
    }
    
    // Création d'un nouveau message
    public static function create($project_id, $sender_id, $content) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (project_id, sender_id, content) VALUES (:project_id, :sender_id, :content)");
            $stmt->execute([
                ':project_id' => $project_id,
                ':sender_id'  => $sender_id,
                ':content'    => $content
            ]);
            return $pdo->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Récupérer tous les messages d'un projet
    public static function findByProject($project_id) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT m.*, u.username as sender_username 
                FROM messages m
                LEFT JOIN users u ON m.sender_id = u.id
                WHERE m.project_id = :project_id 
                ORDER BY m.created_at ASC
            ");
            $stmt->execute([':project_id' => $project_id]);
            $messages = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $messages[] = new Message(
                    $row['id'],
                    $row['project_id'],
                    $row['sender_id'],
                    $row['content'],
                    $row['created_at'],
                    $row['sender_username']
                );
            }
            return $messages;
        } catch (PDOException $e) {
            return [];
        }
    }
}
