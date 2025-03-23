<!-- views/partials/navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?php echo BASE_URL; ?>/router.php?action=dashboard">
      <i class="bi bi-kanban"></i> Projet Collaboratif
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/router.php?action=dashboard">
              <i class="bi bi-speedometer2"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/router.php?action=project_create">
              <i class="bi bi-plus-circle"></i> Créer un projet
            </a>
          </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
          <li class="nav-item">
            <span class="navbar-text me-3">
              <i class="bi bi-person-circle"></i> Bonjour, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL; ?>/router.php?action=logout">
              <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
          </li>
        <?php else: ?>
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
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
