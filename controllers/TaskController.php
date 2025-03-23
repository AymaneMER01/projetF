<?php
// controllers/TaskController.php
require_once __DIR__ . '/../models/Task.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ProjectMember.php';

class TaskController {
    // Création d'une nouvelle tâche pour un projet
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 
                     (isset($_POST['project_id']) ? intval($_POST['project_id']) : 0);
        
        // Vérifier si l'utilisateur est propriétaire du projet
        if (ProjectMember::getMemberRole($project_id, $_SESSION['user_id']) !== 'propriétaire') {
            $error = "Seul le propriétaire du projet peut créer des tâches.";
            redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title']);
            $description = trim($_POST['description']);
            $due_date    = isset($_POST['due_date']) && !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            $assigned_to = isset($_POST['assigned_to']) && !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
            
            if (empty($title)) {
                $error = "Le titre de la tâche est obligatoire.";
                
                // Récupérer les membres du projet pour le formulaire d'attribution
                $members = ProjectMember::getMembers($project_id);
                
                include ROOT_DIR . '/views/task_create.php';
                return;
            }
            
            $taskId = Task::create($project_id, $title, $description, $due_date, $assigned_to);
            if ($taskId) {
                redirect('router.php?action=project_view&id=' . $project_id . '&success=' . urlencode('Tâche créée avec succès'));
            } else {
                $error = "Erreur lors de la création de la tâche.";
                
                // Récupérer les membres du projet pour le formulaire d'attribution
                $members = ProjectMember::getMembers($project_id);
                
                include ROOT_DIR . '/views/task_create.php';
            }
        } else {
            // Récupérer les membres du projet pour le formulaire d'attribution
            $members = ProjectMember::getMembers($project_id);
            
            // Vérifier si l'utilisateur est propriétaire du projet
            $is_owner = true; // À ce stade, nous avons déjà vérifié qu'il est propriétaire
            
            include ROOT_DIR . '/views/task_create.php';
        }
    }
    
    /**
     * Met à jour le statut d'une tâche
     */
    public function updateStatus() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        // Récupère les données du formulaire
        $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $status = isset($_POST['status']) ? $_POST['status'] : '';
        
        // Valide les données
        if (empty($task_id) || empty($project_id) || empty($status)) {
            redirect("router.php?action=project_view&id=$project_id&error=Données invalides pour la mise à jour de statut");
        }
        
        // Vérifie que l'utilisateur est propriétaire du projet
        if (ProjectMember::getMemberRole($project_id, $_SESSION['user_id']) !== 'propriétaire') {
            redirect("router.php?action=project_view&id=$project_id&error=Seul le propriétaire peut modifier le statut des tâches");
        }
        
        // Vérifie le statut (doit être l'un des statuts valides)
        $valid_statuses = ['À faire', 'En cours', 'Terminé'];
        if (!in_array($status, $valid_statuses)) {
            redirect("router.php?action=project_view&id=$project_id&error=Statut invalide");
        }
        
        // Récupère la tâche
        $task = Task::findById($task_id);
        if (!$task || $task->project_id != $project_id) {
            redirect("router.php?action=project_view&id=$project_id&error=Tâche introuvable");
        }
        
        // Mise à jour du statut
        if (Task::updateStatus($task_id, $status)) {
            redirect("router.php?action=project_view&id=$project_id&success=Statut mis à jour avec succès");
        } else {
            redirect("router.php?action=project_view&id=$project_id&error=Erreur lors de la mise à jour du statut");
        }
    }
    
    // Attribution d'une tâche à un utilisateur
    public function assignTask() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $task_id = intval($_POST['task_id']);
            $user_id = intval($_POST['user_id']);
            $project_id = intval($_POST['project_id']);
            
            // Vérifier si l'utilisateur est propriétaire du projet
            if (ProjectMember::getMemberRole($project_id, $_SESSION['user_id']) !== 'propriétaire') {
                $error = "Seul le propriétaire du projet peut attribuer des tâches.";
                redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
                return;
            }
            
            // Vérifier si l'utilisateur assigné est membre du projet
            if (!ProjectMember::isMember($project_id, $user_id)) {
                $error = "L'utilisateur n'est pas membre du projet.";
                redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
                return;
            }
            
            if (Task::assignTask($task_id, $user_id)) {
                $success = "Tâche attribuée avec succès.";
                redirect('router.php?action=project_view&id=' . $project_id . '&success=' . urlencode($success));
            } else {
                $error = "Erreur lors de l'attribution de la tâche.";
                redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
            }
        } else {
            redirect('router.php?action=dashboard');
        }
    }
    
    // Affichage du formulaire de modification d'une tâche
    public function edit() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        $task_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$task_id) {
            redirect('router.php?action=dashboard');
            return;
        }
        
        $task = Task::findById($task_id);
        
        if (!$task) {
            $error = "Tâche introuvable.";
            include ROOT_DIR . '/views/error.php';
            return;
        }
        
        // Vérifier si l'utilisateur est propriétaire du projet
        if (ProjectMember::getMemberRole($task->project_id, $_SESSION['user_id']) !== 'propriétaire') {
            $error = "Seul le propriétaire du projet peut modifier les tâches.";
            redirect('router.php?action=project_view&id=' . $task->project_id . '&error=' . urlencode($error));
            return;
        }
        
        // Récupérer les membres du projet pour le formulaire d'attribution
        $members = ProjectMember::getMembers($task->project_id);
        
        include ROOT_DIR . '/views/task_edit.php';
    }
    
    // Traitement de la mise à jour d'une tâche
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('router.php?action=dashboard');
            return;
        }
        
        $task_id = intval($_POST['task_id']);
        $project_id = intval($_POST['project_id']);
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $assigned_to = isset($_POST['assigned_to']) && !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : null;
        
        // Vérifier si l'utilisateur est propriétaire du projet
        if (ProjectMember::getMemberRole($project_id, $_SESSION['user_id']) !== 'propriétaire') {
            $error = "Seul le propriétaire du projet peut modifier les tâches.";
            redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
            return;
        }
        
        $success = true;
        
        // Mettre à jour le statut si fourni
        if ($status) {
            $valid_statuses = ['À faire', 'En cours', 'Terminé'];
            if (in_array($status, $valid_statuses)) {
                $success = $success && Task::updateStatus($task_id, $status);
            }
        }
        
        // Attribuer la tâche si fourni
        if ($assigned_to !== null) {
            // Vérifier si l'utilisateur assigné est membre du projet
            if (ProjectMember::isMember($project_id, $assigned_to)) {
                $success = $success && Task::assignTask($task_id, $assigned_to);
            }
        }
        
        if ($success) {
            $success_msg = "Tâche mise à jour avec succès.";
            redirect('router.php?action=project_view&id=' . $project_id . '&success=' . urlencode($success_msg));
        } else {
            $error = "Erreur lors de la mise à jour de la tâche.";
            redirect('router.php?action=project_view&id=' . $project_id . '&error=' . urlencode($error));
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
        
        // Vérifier si l'utilisateur est propriétaire du projet
        $is_owner = (ProjectMember::getMemberRole($project_id, $_SESSION['user_id']) === 'propriétaire');
        
        // Récupérer les membres du projet pour le formulaire d'attribution
        $members = ProjectMember::getMembers($project_id);
        
        include ROOT_DIR . '/views/tasks_list.php';
    }
}
