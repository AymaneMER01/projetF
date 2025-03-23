<!-- views/project_edit.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Modifier le Projet - <?php echo htmlspecialchars($project->title); ?></title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier le Projet</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/router.php?action=project_update">
              <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
              
              <div class="mb-3">
                <label for="title" class="form-label">Titre du Projet</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($project->title); ?>" required>
              </div>
              
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($project->description); ?></textarea>
              </div>
              
              <div class="d-flex justify-content-between">
                <a href="<?php echo BASE_URL; ?>/router.php?action=project_view&id=<?php echo $project->id; ?>" class="btn btn-secondary">
                  <i class="bi bi-arrow-left"></i> Annuler
                </a>
                <button type="submit" class="btn btn-warning text-white">
                  <i class="bi bi-save"></i> Enregistrer les modifications
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html> 