<!-- views/dashboard.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Tableau de Bord - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <h1 class="mb-4">Tableau de Bord</h1>
    <h3>Bienvenue, <?php echo htmlspecialchars($username); ?></h3>
    
    <div class="row mt-4">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Mes Projets</h5>
          </div>
          <div class="card-body">
            <?php if (isset($projects) && count($projects) > 0): ?>
              <div class="list-group">
                <?php foreach ($projects as $project): ?>
                  <a href="<?php echo BASE_URL; ?>/router.php?action=project_view&id=<?php echo $project->id; ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                      <h5 class="mb-1"><?php echo htmlspecialchars($project->title); ?></h5>
                      <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($project->description, 0, 100)) . (strlen($project->description) > 100 ? '...' : ''); ?></p>
                    </div>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <p class="text-muted">Vous n'avez aucun projet pour le moment.</p>
              <a href="<?php echo BASE_URL; ?>/router.php?action=project_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Créer un projet
              </a>
            <?php endif; ?>
          </div>
          <?php if (isset($projects) && count($projects) > 0): ?>
            <div class="card-footer">
              <a href="<?php echo BASE_URL; ?>/router.php?action=project_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau Projet
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
      
      <div class="col-md-4">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h5 class="mb-0">Activités Récentes</h5>
          </div>
          <div class="card-body">
            <p class="text-muted">Aucune activité récente.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
