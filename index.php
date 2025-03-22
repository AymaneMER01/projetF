<?php
require_once 'config/init.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Plateforme de Gestion de Projets</title>
    <?php include ROOT_DIR . '/views/partials/header.php'; ?>
    <style>
        .hero-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .feature-card {
            transition: transform 0.3s;
            margin-bottom: 30px;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #667eea;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>/index.php">
                <i class="bi bi-kanban"></i> Gestion Projets
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/router.php?action=login">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/router.php?action=register">
                            <i class="bi bi-person-plus"></i> Inscription
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Gestion de Projets Collaboratifs</h1>
            <p class="lead mb-5">Une plateforme complète pour gérer vos projets, assigner des tâches et collaborer efficacement.</p>
            <a href="<?php echo BASE_URL; ?>/router.php?action=register" class="btn btn-light btn-lg px-5 py-3">
                <i class="bi bi-rocket-takeoff"></i> Commencer maintenant
            </a>
        </div>
    </section>
    
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Fonctionnalités principales</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-kanban"></i>
                            </div>
                            <h4>Gestion de projets</h4>
                            <p>Créez, organisez et suivez l'avancement de vos projets en temps réel.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-check2-square"></i>
                            </div>
                            <h4>Gestion des tâches</h4>
                            <p>Assignez des tâches, définissez des échéances et suivez leur avancement.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 feature-card shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <h4>Messagerie intégrée</h4>
                            <p>Communiquez facilement avec tous les membres de votre équipe.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?php echo date('Y'); ?> Plateforme de Gestion de Projets Collaboratifs</p>
        </div>
    </footer>

    <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
