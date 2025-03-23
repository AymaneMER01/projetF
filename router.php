<?php
// Point d'entrée unique pour toutes les requêtes
require_once 'config/init.php';

// Récupérer l'action demandée
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

// Routage simple
switch ($action) {
    // Authentification
    case 'register':
        $controller = new UserController();
        $controller->register();
        break;
    
    case 'login':
        $controller = new UserController();
        $controller->login();
        break;
    
    case 'logout':
        $controller = new UserController();
        $controller->logout();
        break;
    
    // Projets
    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new ProjectController();
        $controller->listProjects();
        break;
    
    case 'project_create':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new ProjectController();
        $controller->create();
        break;
    
    case 'project_view':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new ProjectController();
        $controller->view();
        break;
    
    // Recherche d'utilisateurs et invitation de membres
    case 'search_users':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new ProjectController();
        $controller->searchUsers();
        break;
        
    case 'invite_member':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new ProjectController();
        $controller->inviteMember();
        break;
    
    // Tâches
    case 'task_create':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->create();
        break;
    
    case 'tasks_list':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->listByProject();
        break;
    
    case 'task_edit':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->edit();
        break;
    
    case 'task_update':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->update();
        break;
    
    case 'task_update_status':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->updateStatus();
        break;
    
    case 'task_assign':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new TaskController();
        $controller->assignTask();
        break;
    
    // Messages
    case 'message_form':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new MessageController();
        $controller->send();
        break;
    
    case 'messages_list':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new MessageController();
        $controller->listByProject();
        break;
    
    // Documents
    case 'document_upload':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new DocumentController();
        $controller->upload();
        break;
    
    case 'documents_list':
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        $controller = new DocumentController();
        $controller->listByProject();
        break;
    
    // Page d'accueil par défaut
    case 'home':
    default:
        include 'index.php';
        break;
} 