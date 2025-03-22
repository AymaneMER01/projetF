<!-- views/dashboard.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Dashboard - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  <div class="container mt-4">
    <h2>Bienvenue, <?php echo htmlspecialchars($username); ?></h2>
    <p>Voici vos projets en cours :</p>
    
    <?php if (isset($projects) && count($projects) > 0): ?>
      <div class="list-group">
        <?php foreach ($projects as $project): ?>
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <a href="<?php echo BASE_URL; ?>/router.php?action=project_view&id=<?php echo $project->id; ?>" class="text-decoration-none">
              <?php echo htmlspecialchars($project->title); ?>
            </a>
            <span class="badge bg-primary rounded-pill">Score: <?php echo $project->score; ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        <p>Aucun projet à afficher.</p>
        <hr>
        <a href="<?php echo BASE_URL; ?>/router.php?action=project_create" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Créer un projet
        </a>
      </div>
    <?php endif; ?>
    
    <?php if (count($projects) > 0): ?>
      <div class="mt-4">
        <a href="<?php echo BASE_URL; ?>/router.php?action=project_create" class="btn btn-primary">
          <i class="bi bi-plus-circle"></i> Créer un nouveau projet
        </a>
      </div>
    <?php endif; ?>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
