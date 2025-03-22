<?php
// controllers/DocumentController.php
require_once __DIR__ . '/../models/Document.php';

class DocumentController {
    // Upload d'un document pour un projet
    public function upload() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
            $project_id = intval($_POST['project_id']);
            $uploaded_by = $_SESSION['user_id'];
            $filename = basename($_FILES['document']['name']);
            $targetDir = ROOT_DIR . '/uploads/';
            
            // Créer le répertoire d'uploads s'il n'existe pas
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            $targetFile = $targetDir . $filename;
            
            // Vérification simple du type et de la taille peut être ajoutée ici
            if (move_uploaded_file($_FILES['document']['tmp_name'], $targetFile)) {
                $result = Document::create($project_id, $filename, 'uploads/' . $filename, $uploaded_by);
                if ($result) {
                    redirect('router.php?action=project_view&id=' . $project_id);
                } else {
                    $error = "Erreur lors de l'enregistrement du document dans la base.";
                    include ROOT_DIR . '/views/document_upload.php';
                }
            } else {
                $error = "Erreur lors de l'upload du fichier.";
                include ROOT_DIR . '/views/document_upload.php';
            }
        } else {
            include ROOT_DIR . '/views/document_upload.php';
        }
    }
    
    // Affichage des documents d'un projet
    public function listByProject() {
        if (!isset($_SESSION['user_id'])) {
            redirect('router.php?action=login');
        }
        
        if (!isset($_GET['project_id'])) {
            redirect('router.php?action=dashboard');
        }
        
        $project_id = intval($_GET['project_id']);
        $documents = Document::findByProject($project_id);
        include ROOT_DIR . '/views/documents_list.php';
    }
}
