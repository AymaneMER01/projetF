<!-- views/documents_list.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Documents du Projet - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <div class="card shadow mb-4">
      <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-file-earmark"></i> Documents</h4>
        <a href="<?php echo BASE_URL; ?>/router.php?action=document_upload&project_id=<?php echo $project_id ?? 0; ?>" class="btn btn-light btn-sm">
          <i class="bi bi-upload"></i> Ajouter un document
        </a>
      </div>
      <div class="card-body">
        <?php if (isset($documents) && count($documents) > 0): ?>
          <div class="list-group">
            <?php foreach ($documents as $doc): ?>
              <a href="<?php echo htmlspecialchars($doc->file_path); ?>" class="list-group-item list-group-item-action" target="_blank">
                <i class="bi bi-file-earmark-text"></i> <?php echo htmlspecialchars($doc->filename); ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted">Aucun document trouvé.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
