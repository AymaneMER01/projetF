<!-- views/error.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Erreur - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Erreur</h4>
          </div>
          <div class="card-body">
            <div class="alert alert-danger">
              <?php echo isset($error) ? htmlspecialchars($error) : "Une erreur est survenue."; ?>
            </div>
            <div class="d-grid">
              <a href="<?php echo BASE_URL; ?>/router.php?action=dashboard" class="btn btn-primary">
                <i class="bi bi-house"></i> Retour au Dashboard
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
