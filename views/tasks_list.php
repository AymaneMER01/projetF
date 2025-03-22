<!-- views/tasks_list.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Liste des Tâches - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <div class="card shadow mb-4">
      <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-check2-square"></i> Tâches du Projet</h4>
        <a href="<?php echo BASE_URL; ?>/router.php?action=task_create&project_id=<?php echo $project_id ?? 0; ?>" class="btn btn-light btn-sm">
          <i class="bi bi-plus-circle"></i> Ajouter une tâche
        </a>
      </div>
      <div class="card-body">
        <?php if (isset($tasks) && count($tasks) > 0): ?>
          <div class="list-group">
            <?php foreach ($tasks as $task): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <h5><?php echo htmlspecialchars($task->title); ?></h5>
                  <small class="text-muted">Date d'échéance: <?php echo htmlspecialchars($task->due_date); ?></small>
                </div>
                <span class="badge bg-info"><?php echo htmlspecialchars($task->status); ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted">Aucune tâche trouvée.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
