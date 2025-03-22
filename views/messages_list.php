<!-- views/messages_list.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Messages du Projet - Projet Collaboratif</title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <div class="card shadow mb-4">
      <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-chat-dots"></i> Messages</h4>
        <a href="<?php echo BASE_URL; ?>/router.php?action=message_form&project_id=<?php echo $project_id ?? 0; ?>" class="btn btn-light btn-sm">
          <i class="bi bi-send"></i> Envoyer un message
        </a>
      </div>
      <div class="card-body">
        <?php if (isset($messages) && count($messages) > 0): ?>
          <div class="list-group">
            <?php foreach ($messages as $msg): ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between">
                  <h6><i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($msg->sender_username ?? 'Utilisateur inconnu'); ?></h6>
                  <small class="text-muted"><?php echo $msg->created_at; ?></small>
                </div>
                <p class="mb-1"><?php echo htmlspecialchars($msg->content); ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted">Aucun message pour ce projet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
