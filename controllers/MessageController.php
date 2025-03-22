<?php
// controllers/MessageController.php
require_once __DIR__ . '/../models/Message.php';

class MessageController {
    // Envoi d'un message dans un projet
    public function send() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_id = intval($_POST['project_id']);
            $sender_id  = $_SESSION['user_id'];
            $content    = trim($_POST['content']);
            
            if (empty($content)) {
                $error = "Le contenu du message ne peut pas être vide.";
                include ROOT_DIR . '/views/message_form.php';
                return;
            }
            
            $messageId = Message::create($project_id, $sender_id, $content);
            if ($messageId) {
                redirect('router.php?action=project_view&id=' . $project_id);
            } else {
                $error = "Erreur lors de l'envoi du message.";
                include ROOT_DIR . '/views/message_form.php';
            }
        } else {
            include ROOT_DIR . '/views/message_form.php';
        }
    }
    
    // Affichage des messages d'un projet
    public function listByProject() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if (!isset($_GET['project_id'])) {
            redirect('router.php?action=dashboard');
        }
        
        $project_id = intval($_GET['project_id']);
        $messages = Message::findByProject($project_id);
        include ROOT_DIR . '/views/messages_list.php';
    }
}
