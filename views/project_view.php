<!-- views/project_view.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
  <title>Détails du Projet - <?php echo htmlspecialchars($project->title); ?></title>
  <?php include ROOT_DIR . '/views/partials/header.php'; ?>
</head>
<body>
  <?php include ROOT_DIR . '/views/partials/navbar.php'; ?>
  
  <div class="container mt-4">
    <?php if (isset($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" id="error-alert" role="alert">
        <?php echo htmlspecialchars($_GET['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" id="success-alert" role="alert">
        <?php echo htmlspecialchars($_GET['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>
    
    <h1 class="mb-4"><?php echo htmlspecialchars($project->title); ?></h1>
    <p class="lead mb-4"><?php echo htmlspecialchars($project->description); ?></p>
    
    <!-- Section Membres -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-people-fill"></i> Membres</h4>
        <?php if (ProjectMember::getMemberRole($project->id, $_SESSION['user_id']) === 'propriétaire'): ?>
          <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#inviteMemberModal">
            <i class="bi bi-person-plus"></i> Inviter un membre
          </button>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <?php if (isset($members) && count($members) > 0): ?>
          <ul class="list-group">
            <?php foreach ($members as $member): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($member['username'] ?? 'Utilisateur inconnu'); ?>
                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars(ucfirst($member['role'])); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p class="text-muted">Aucun membre ajouté.</p>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Section Tâches -->
    <div class="card mb-4">
      <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-check2-square"></i> Tâches</h4>
        <?php if (ProjectMember::getMemberRole($project->id, $_SESSION['user_id']) === 'propriétaire'): ?>
          <a href="<?php echo BASE_URL; ?>/router.php?action=task_create&project_id=<?php echo $project->id; ?>" class="btn btn-light btn-sm">
            <i class="bi bi-plus-circle"></i> Ajouter une tâche
          </a>
        <?php endif; ?>
      </div>
      <div class="card-body">
        <?php if (isset($tasks) && count($tasks) > 0): ?>
          <div class="list-group">
            <?php foreach ($tasks as $task): ?>
              <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <h5 class="mb-0">
                    <?php 
                    // Icône de statut
                    $statusIcon = '';
                    $statusClass = '';
                    switch ($task->status) {
                      case 'À faire':
                        $statusIcon = 'bi-circle';
                        $statusClass = 'text-danger';
                        break;
                      case 'En cours':
                        $statusIcon = 'bi-play-circle-fill';
                        $statusClass = 'text-warning';
                        break;
                      case 'Terminé':
                        $statusIcon = 'bi-check-circle-fill';
                        $statusClass = 'text-success';
                        break;
                      default:
                        $statusIcon = 'bi-question-circle';
                        $statusClass = 'text-secondary';
                    }
                    ?>
                    <i class="bi <?php echo $statusIcon; ?> <?php echo $statusClass; ?>"></i>
                    <?php echo htmlspecialchars($task->title); ?>
                  </h5>
                  
                  <?php if (ProjectMember::getMemberRole($project->id, $_SESSION['user_id']) === 'propriétaire'): ?>
                    <a href="<?php echo BASE_URL; ?>/router.php?action=task_edit&id=<?php echo $task->id; ?>" class="btn btn-warning btn-sm text-white">
                      <i class="bi bi-pencil-square"></i>
                    </a>
                  <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                  <div>
                    <?php if (!empty($task->description)): ?>
                      <p class="mb-1 text-muted"><?php echo htmlspecialchars($task->description); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($task->due_date)): ?>
                      <span class="badge bg-secondary">
                        <i class="bi bi-calendar"></i> 
                        Échéance: <?php echo date('d/m/Y', strtotime($task->due_date)); ?>
                      </span>
                    <?php endif; ?>
                  </div>
                  <span class="badge bg-<?php echo $task->status === 'À faire' ? 'danger' : ($task->status === 'En cours' ? 'warning' : 'success'); ?>">
                    <?php echo htmlspecialchars($task->status); ?>
                  </span>
                </div>
                
                <?php if ($task->assigned_to): ?>
                  <div class="mt-2 small">
                    <i class="bi bi-person-badge"></i> 
                    Assignée à: <strong><?php echo htmlspecialchars($task->assigned_username ?? 'Utilisateur inconnu'); ?></strong>
                  </div>
                <?php endif; ?>
                
                <?php if (ProjectMember::getMemberRole($project->id, $_SESSION['user_id']) === 'propriétaire'): ?>
                  <div class="mt-2 d-flex gap-2">
                    <!-- Formulaires de changement rapide de statut -->
                    <form action="<?php echo BASE_URL; ?>/router.php?action=task_update_status" method="POST" class="d-inline-block">
                      <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                      <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                      <input type="hidden" name="status" value="À faire">
                      <button type="submit" class="btn btn-outline-danger btn-sm <?php echo $task->status === 'À faire' ? 'active' : ''; ?>">
                        <i class="bi bi-circle"></i> À faire
                      </button>
                    </form>
                    
                    <form action="<?php echo BASE_URL; ?>/router.php?action=task_update_status" method="POST" class="d-inline-block">
                      <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                      <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                      <input type="hidden" name="status" value="En cours">
                      <button type="submit" class="btn btn-outline-warning btn-sm <?php echo $task->status === 'En cours' ? 'active' : ''; ?>">
                        <i class="bi bi-play-circle-fill"></i> En cours
                      </button>
                    </form>
                    
                    <form action="<?php echo BASE_URL; ?>/router.php?action=task_update_status" method="POST" class="d-inline-block">
                      <input type="hidden" name="task_id" value="<?php echo $task->id; ?>">
                      <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                      <input type="hidden" name="status" value="Terminé">
                      <button type="submit" class="btn btn-outline-success btn-sm <?php echo $task->status === 'Terminé' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle-fill"></i> Terminé
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-muted">Aucune tâche enregistrée.</p>
        <?php endif; ?>
      </div>
    </div>
    
    <!-- Section Messages -->
    <div class="card mb-4">
      <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-chat-dots"></i> Messages</h4>
        <a href="<?php echo BASE_URL; ?>/router.php?action=message_form&project_id=<?php echo $project->id; ?>" class="btn btn-light btn-sm">
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
    
    <!-- Section Documents -->
    <div class="card mb-4">
      <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-file-earmark"></i> Documents</h4>
        <a href="<?php echo BASE_URL; ?>/router.php?action=document_upload&project_id=<?php echo $project->id; ?>" class="btn btn-light btn-sm">
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
          <p class="text-muted">Aucun document ajouté.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <!-- Modal d'invitation de membres (visible uniquement pour le propriétaire) -->
  <?php if (ProjectMember::getMemberRole($project->id, $_SESSION['user_id']) === 'propriétaire'): ?>
  <div class="modal fade" id="inviteMemberModal" tabindex="-1" aria-labelledby="inviteMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="inviteMemberModalLabel"><i class="bi bi-person-plus"></i> Inviter un membre</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <?php if (isset($invite_error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($invite_error); ?></div>
          <?php endif; ?>
          
          <ul class="nav nav-tabs mb-3" id="inviteTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="search-tab" data-bs-toggle="tab" data-bs-target="#search-users" type="button" role="tab" aria-controls="search-users" aria-selected="true">Rechercher</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="all-users-tab" data-bs-toggle="tab" data-bs-target="#all-users" type="button" role="tab" aria-controls="all-users" aria-selected="false">Tous les utilisateurs</button>
            </li>
          </ul>
          
          <div class="tab-content" id="inviteTabContent">
            <!-- Onglet de recherche -->
            <div class="tab-pane fade show active" id="search-users" role="tabpanel" aria-labelledby="search-tab">
              <form class="mb-3">
                <div class="input-group">
                  <input type="text" class="form-control" id="search-username" placeholder="Rechercher un utilisateur...">
                  <button class="btn btn-primary" type="button" id="search-username-btn">
                    <i class="bi bi-search"></i>
                  </button>
                </div>
              </form>
              <div id="search-results" class="list-group">
                <!-- Les résultats de recherche seront insérés ici via JavaScript -->
                <div class="text-center text-muted py-3">
                  <i class="bi bi-search"></i> Saisissez un nom d'utilisateur pour commencer la recherche
                </div>
              </div>
            </div>
            
            <!-- Onglet de tous les utilisateurs -->
            <div class="tab-pane fade" id="all-users" role="tabpanel" aria-labelledby="all-users-tab">
              <div class="list-group">
                <?php if (isset($all_users) && count($all_users) > 0): ?>
                  <?php foreach ($all_users as $user): ?>
                    <form action="<?php echo BASE_URL; ?>/router.php?action=invite_member" method="POST" class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <i class="bi bi-person"></i> <?php echo htmlspecialchars($user['username']); ?>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($user['role']); ?></span>
                      </div>
                      <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                      <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                      <?php if (ProjectMember::isMember($project->id, $user['id'])): ?>
                        <span class="badge bg-success">Déjà membre</span>
                      <?php else: ?>
                        <button type="submit" class="btn btn-sm btn-primary">
                          <i class="bi bi-person-plus"></i> Inviter
                        </button>
                      <?php endif; ?>
                    </form>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="text-center text-muted py-3">
                    <i class="bi bi-exclamation-circle"></i> Aucun utilisateur trouvé
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Script pour la recherche d'utilisateurs par AJAX -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const searchBtn = document.getElementById('search-username-btn');
      const searchInput = document.getElementById('search-username');
      const searchResults = document.getElementById('search-results');
      
      searchBtn.addEventListener('click', function() {
        searchUsers();
      });
      
      searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          searchUsers();
        }
      });
      
      function searchUsers() {
        const username = searchInput.value.trim();
        if (username === '') {
          searchResults.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-exclamation-circle"></i> Veuillez saisir un nom d\'utilisateur</div>';
          return;
        }
        
        searchResults.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>';
        
        // Requête AJAX pour rechercher des utilisateurs
        fetch(`<?php echo BASE_URL; ?>/router.php?action=search_users&username=${encodeURIComponent(username)}&project_id=<?php echo $project->id; ?>`)
          .then(response => response.json())
          .then(data => {
            if (data.length === 0) {
              searchResults.innerHTML = '<div class="text-center text-muted py-3"><i class="bi bi-exclamation-circle"></i> Aucun utilisateur trouvé</div>';
            } else {
              let html = '';
              data.forEach(user => {
                let buttonHtml = '';
                if (user.is_member) {
                  buttonHtml = '<span class="badge bg-success">Déjà membre</span>';
                } else {
                  buttonHtml = `
                    <form action="<?php echo BASE_URL; ?>/router.php?action=invite_member" method="POST" class="d-inline">
                      <input type="hidden" name="project_id" value="<?php echo $project->id; ?>">
                      <input type="hidden" name="user_id" value="${user.id}">
                      <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-person-plus"></i> Inviter
                      </button>
                    </form>
                  `;
                }
                
                html += `
                  <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                      <i class="bi bi-person"></i> ${user.username}
                      <span class="badge bg-secondary">${user.role}</span>
                    </div>
                    ${buttonHtml}
                  </div>
                `;
              });
              searchResults.innerHTML = html;
            }
          })
          .catch(error => {
            searchResults.innerHTML = '<div class="text-center text-danger py-3"><i class="bi bi-exclamation-triangle"></i> Erreur lors de la recherche</div>';
            console.error('Erreur de recherche:', error);
          });
      }
    });
  </script>
  <?php endif; ?>
  
  <!-- Script pour faire disparaître les alertes après 5 secondes -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Récupérer les alertes
      const errorAlert = document.getElementById('error-alert');
      const successAlert = document.getElementById('success-alert');
      
      // Faire disparaître les alertes après 5 secondes
      if (errorAlert) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(errorAlert);
          bsAlert.close();
        }, 5000);
      }
      
      if (successAlert) {
        setTimeout(function() {
          const bsAlert = new bootstrap.Alert(successAlert);
          bsAlert.close();
        }, 5000);
      }
    });
  </script>
  
  <?php include ROOT_DIR . '/views/partials/footer.php'; ?>
</body>
</html>
