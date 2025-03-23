<?php
// controllers/ProjectController.php
require_once __DIR__ . '/../models/Project.php';
require_once __DIR__ . '/../models/ProjectMember.php';
require_once __DIR__ . '/../models/User.php';

class ProjectController {
    // Création d'un nouveau projet
    public function create() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title']);
            $description = trim($_POST['description']);
            $owner_id    = $_SESSION['user_id'];
            
            if (empty($title)) {
                $error = "Le titre du projet est obligatoire.";
                include ROOT_DIR . '/views/project_create.php';
                return;
            }
            
            $projectId = Project::create($title, $description, $owner_id);
            if ($projectId) {
                // Le créateur est automatiquement membre du projet
                ProjectMember::addMember($projectId, $owner_id, 'propriétaire');
                redirect('router.php?action=dashboard');
            } else {
                $error = "Erreur lors de la création du projet.";
                include ROOT_DIR . '/views/project_create.php';
            }
        } else {
            include ROOT_DIR . '/views/project_create.php';
        }
    }
    
    // Affichage d'un projet et de ses détails
    public function view() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if (!isset($_GET['id'])) {
            redirect('router.php?action=dashboard');
        }
        
        $projectId = intval($_GET['id']);
        $project = Project::findById($projectId);
        if (!$project) {
            $error = "Projet non trouvé.";
            include ROOT_DIR . '/views/error.php';
            return;
        }
        
        // Vérifier si l'utilisateur est membre du projet
        $user_role = ProjectMember::getMemberRole($projectId, $_SESSION['user_id']);
        if (!$user_role) {
            $error = "Vous n'êtes pas membre de ce projet.";
            include ROOT_DIR . '/views/error.php';
            return;
        }
        
        // Déterminer si l'utilisateur est propriétaire du projet
        $is_owner = ($user_role === 'propriétaire');
        
        // Récupérer les membres, tâches, etc.
        $members = ProjectMember::getMembers($projectId);
        
        // Récupérer tous les utilisateurs pour le formulaire d'invitation
        $all_users = User::getAll();
        
        // Initialiser les variables pour éviter les erreurs
        $tasks = [];
        $messages = [];
        $documents = [];
        
        // On pourrait récupérer ces données ici si nécessaire
        if (class_exists('Task')) {
            $tasks = Task::findByProject($projectId);
        }
        
        if (class_exists('Message')) {
            $messages = Message::findByProject($projectId);
        }
        
        if (class_exists('Document')) {
            $documents = Document::findByProject($projectId);
        }
        
        include ROOT_DIR . '/views/project_view.php';
    }
    
    // Recherche d'utilisateurs (pour l'invitation)
    public function searchUsers() {
        if (!isset($_SESSION['user_id'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Non autorisé']);
            exit;
        }
        
        $username = isset($_GET['username']) ? trim($_GET['username']) : '';
        $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
        
        if (empty($username) || $project_id === 0) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $users = User::searchByUsername($username);
        
        // Vérifier quels utilisateurs sont déjà membres du projet
        foreach ($users as &$user) {
            $user['is_member'] = ProjectMember::isMember($project_id, $user['id']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($users);
        exit;
    }
    
    // Invitation d'un membre à un projet
    public function inviteMember() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_id = intval($_POST['project_id']);
            $user_id    = intval($_POST['user_id']);
            
            // Vérification de la légitimité de l'invitation (propriétaire uniquement?)
            $userRole = ProjectMember::getMemberRole($project_id, $_SESSION['user_id']);
            if ($userRole !== 'propriétaire') {
                $invite_error = "Seul le propriétaire du projet peut inviter des membres.";
                $this->view();
                return;
            }
            
            // Vérifier si l'utilisateur est déjà membre
            if (ProjectMember::isMember($project_id, $user_id)) {
                $invite_error = "Cet utilisateur est déjà membre du projet.";
                $this->view();
                return;
            }

            $result = ProjectMember::addMember($project_id, $user_id);
            if ($result) {
                redirect('router.php?action=project_view&id=' . $project_id);
            } else {
                $invite_error = "Erreur lors de l'invitation du membre.";
                $this->view();
            }
        } else {
            redirect('router.php?action=dashboard');
        }
    }
    
    // Mise à jour du score d'un projet (logique à développer selon vos critères)
    public function updateScore() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_id = intval($_POST['project_id']);
            $score      = intval($_POST['score']);
            if (Project::updateScore($project_id, $score)) {
                echo "Score mis à jour.";
            } else {
                echo "Erreur lors de la mise à jour du score.";
            }
        }
    }
    
    // Liste des projets pour le tableau de bord
    public function listProjects() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        $userId = $_SESSION['user_id'];
        
        // Récupérer les projets dont l'utilisateur est membre
        $projects = [];
        
        // Si la méthode findByMember existe, utilisons-la
        if (method_exists('Project', 'findByMember')) {
            $projects = Project::findByMember($userId);
        } 
        // Sinon, essayons d'utiliser une autre méthode qui pourrait exister
        else if (method_exists('ProjectMember', 'getProjectsByUser')) {
            $projects = ProjectMember::getProjectsByUser($userId);
        }
        // Si aucune méthode n'existe, créons une méthode générique (à implémenter plus tard dans le modèle)
        else {
            // Pour l'instant, on simule une liste vide
            $projects = [];
        }
        
        // Récupérer le nom d'utilisateur pour l'affichage
        $username = $_SESSION['username'];
        
        include ROOT_DIR . '/views/dashboard.php';
    }
}
