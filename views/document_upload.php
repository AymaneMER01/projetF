<!-- views/document_upload.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Ajouter un Document - Projet Collaboratif</title>
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
          <div class="card-header bg-secondary text-white">
            <h4 class="mb-0"><i class="bi bi-file-earmark"></i> Ajouter un Document</h4>
          </div>
          <div class="card-body">
            <?php if (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo BASE_URL; ?>/router.php?action=document_upload" enctype="multipart/form-data">
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
              <div class="mb-3">
                <label for="document" class="form-label">Choisir un fichier</label>
                <input type="file" class="form-control" id="document" name="document" required>
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-secondary">
                  <i class="bi bi-upload"></i> Uploader
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
