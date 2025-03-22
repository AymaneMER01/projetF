<?php
// controllers/TaskController.php
require_once __DIR__ . '/../models/Task.php';

class TaskController {
    // Création d'une nouvelle tâche pour un projet
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_id  = intval($_POST['project_id']);
            $title       = trim($_POST['title']);
            $description = trim($_POST['description']);
            $due_date    = isset($_POST['due_date']) ? $_POST['due_date'] : null;
            
            if (empty($title)) {
                $error = "Le titre de la tâche est obligatoire.";
                include ROOT_DIR . '/views/task_create.php';
                return;
            }
            
            $taskId = Task::create($project_id, $title, $description, $due_date);
            if ($taskId) {
                redirect('router.php?action=project_view&id=' . $project_id);
            } else {
                $error = "Erreur lors de la création de la tâche.";
                include ROOT_DIR . '/views/task_create.php';
            }
        } else {
            include ROOT_DIR . '/views/task_create.php';
        }
    }
    
    // Mise à jour du statut d'une tâche
    public function updateStatus() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = intval($_POST['task_id']);
            $status  = $_POST['status']; // Doit être 'A faire', 'En cours', ou 'Terminé'
            if (Task::updateStatus($task_id, $status)) {
                echo "Statut mis à jour.";
            } else {
                echo "Erreur lors de la mise à jour du statut.";
            }
        }
    }
    
    // Affichage des tâches d'un projet
    public function listByProject() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if (!isset($_GET['project_id'])) {
            redirect('router.php?action=dashboard');
        }
        
        $project_id = intval($_GET['project_id']);
        $tasks = Task::findByProject($project_id);
        include ROOT_DIR . '/views/tasks_list.php';
    }
}
