<!-- views/task_edit.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Modifier une Tâche - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Modifier la Tâche</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/router.php?action=task_update">
              <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
              <input type="hidden" name="project_id" value="<?php echo $task->project_id; ?>">
              
              <div class="mb-3">
                <h5><?php echo htmlspecialchars($task->title); ?></h5>
                <p class="text-muted"><?php echo htmlspecialchars($task->description); ?></p>
              </div>
              
              <div class="mb-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                  <option value="À faire" <?php echo $task->status === 'À faire' ? 'selected' : ''; ?>>À faire</option>
                  <option value="En cours" <?php echo $task->status === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                  <option value="Terminé" <?php echo $task->status === 'Terminé' ? 'selected' : ''; ?>>Terminé</option>
                </select>
              </div>
              
              <?php if (isset($members) && count($members) > 0): ?>
              <div class="mb-3">
                <label for="assigned_to" class="form-label">Attribuer à</label>
                <select class="form-select" id="assigned_to" name="assigned_to">
                  <option value="">-- Non attribué --</option>
                  <?php foreach ($members as $member): ?>
                    <option value="<?php echo $member['user_id']; ?>" <?php echo $task->assigned_to == $member['user_id'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($member['username']); ?> 
                      (<?php echo htmlspecialchars(ucfirst($member['role'])); ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
              
              <div class="mb-3">
                <label class="form-label">Date d'échéance</label>
                <p class="form-control-static"><?php echo $task->due_date ? date('d/m/Y', strtotime($task->due_date)) : 'Non définie'; ?></p>
              </div>
              
              <div class="d-flex justify-content-between">
                <a href="<?php echo BASE_URL; ?>/router.php?action=project_view&id=<?php echo $task->project_id; ?>" class="btn btn-secondary">
                  <i class="bi bi-arrow-left"></i> Retour
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