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
    
    /**
     * Supprime un projet
     */
    public function delete() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        // Récupère l'ID du projet à supprimer
        $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$project_id) {
            redirect('router.php?action=dashboard&error=Identifiant de projet invalide');
        }
        
        // Vérifie si l'utilisateur est le propriétaire du projet
        if (!Project::isOwner($project_id, $_SESSION['user_id'])) {
            redirect('router.php?action=dashboard&error=Vous n\'êtes pas autorisé à supprimer ce projet');
        }
        
        // Supprime le projet
        if (Project::delete($project_id)) {
            redirect('router.php?action=dashboard&success=Projet supprimé avec succès');
        } else {
            redirect('router.php?action=dashboard&error=Une erreur est survenue lors de la suppression du projet');
        }
    }
    
    /**
     * Affiche le formulaire de modification d'un projet
     */
    public function edit() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        // Récupère l'ID du projet à modifier
        $project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if (!$project_id) {
            redirect('router.php?action=dashboard&error=Identifiant de projet invalide');
        }
        
        // Récupère les informations du projet
        $project = Project::findById($project_id);
        
        if (!$project) {
            redirect('router.php?action=dashboard&error=Projet introuvable');
        }
        
        // Vérifie si l'utilisateur est le propriétaire du projet
        if (!Project::isOwner($project_id, $_SESSION['user_id'])) {
            redirect('router.php?action=project_view&id=' . $project_id . '&error=Vous n\'êtes pas autorisé à modifier ce projet');
        }
        
        // Affiche la vue
        include ROOT_DIR . '/views/project_edit.php';
    }
    
    /**
     * Traite la modification d'un projet
     */
    public function update() {
        // Vérifie si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        // Vérifie si la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('router.php?action=dashboard');
        }
        
        // Récupère les données du formulaire
        $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        
        // Vérifie l'ID du projet
        if (!$project_id) {
            redirect('router.php?action=dashboard&error=Identifiant de projet invalide');
        }
        
        // Vérifie si l'utilisateur est le propriétaire du projet
        if (!Project::isOwner($project_id, $_SESSION['user_id'])) {
            redirect('router.php?action=project_view&id=' . $project_id . '&error=Vous n\'êtes pas autorisé à modifier ce projet');
        }
        
        // Vérifie que le titre n'est pas vide
        if (empty($title)) {
            $project = Project::findById($project_id);
            $error = "Le titre du projet est obligatoire.";
            include ROOT_DIR . '/views/project_edit.php';
            return;
        }
        
        // Met à jour le projet
        if (Project::update($project_id, $title, $description)) {
            redirect('router.php?action=project_view&id=' . $project_id . '&success=Projet modifié avec succès');
        } else {
            $project = Project::findById($project_id);
            $error = "Une erreur est survenue lors de la modification du projet.";
            include ROOT_DIR . '/views/project_edit.php';
        }
    }
}
