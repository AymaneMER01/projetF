<!-- views/message_form.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Envoyer un Message - Projet Collaboratif</title>
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
          <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-chat-dots"></i> Envoyer un Message</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/router.php?action=message_form">
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
              <div class="mb-3">
                <label for="content" class="form-label">Contenu du Message</label>
                <textarea class="form-control" id="content" name="content" rows="4" required></textarea>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-info text-white">
                  <i class="bi bi-send"></i> Envoyer
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
