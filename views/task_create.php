<!-- views/task_create.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Créer une Tâche - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php 
    include ROOT_DIR . '/views/partials/navbar.php'; 
    $project_id = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;
  ?>
  
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-check2-square"></i> Ajouter une Tâche</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/router.php?action=task_create">
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
              <div class="mb-3">
                <label for="title" class="form-label">Titre de la Tâche</label>
                <input type="text" class="form-control" id="title" name="title" required>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
              </div>
              <div class="mb-3">
                <label for="due_date" class="form-label">Date d'échéance</label>
                <input type="date" class="form-control" id="due_date" name="due_date">
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-success">
                  <i class="bi bi-plus-circle"></i> Créer la Tâche
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
