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
    <?php if (isset($username)): ?>
      <h3>Bienvenue, <?php echo htmlspecialchars($username); ?></h3>
    <?php else: ?>
      <h3>Bienvenue sur votre tableau de bord</h3>
    <?php endif; ?>
    
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
                  <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <a href="<?php echo BASE_URL; ?>/router.php?action=project_view&id=<?php echo $project->id; ?>" class="text-decoration-none flex-grow-1">
                      <div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($project->title); ?></h5>
                        <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($project->description, 0, 100)) . (strlen($project->description) > 100 ? '...' : ''); ?></p>
                      </div>
                    </a>
                    <?php if (Project::isOwner($project->id, $_SESSION['user_id'])): ?>
                      <div class="ms-2">
                        <a href="<?php echo BASE_URL; ?>/router.php?action=project_edit&id=<?php echo $project->id; ?>" class="btn btn-sm btn-warning text-white me-1">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal<?php echo $project->id; ?>">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    <?php endif; ?>
                  </div>
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
  
  <!-- Modales de confirmation de suppression pour chaque projet -->
  <?php if (isset($projects) && count($projects) > 0): ?>
    <?php foreach ($projects as $project): ?>
      <?php if (Project::isOwner($project->id, $_SESSION['user_id'])): ?>
        <div class="modal fade" id="deleteProjectModal<?php echo $project->id; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le projet <strong><?php echo htmlspecialchars($project->title); ?></strong> ?</p>
                <p class="text-danger"><strong>Attention :</strong> Cette action est irréversible et supprimera toutes les tâches, messages et documents associés à ce projet.</p>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="<?php echo BASE_URL; ?>/router.php?action=project_delete&id=<?php echo $project->id; ?>" class="btn btn-danger">
                  <i class="bi bi-trash"></i> Supprimer définitivement
                </a>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
