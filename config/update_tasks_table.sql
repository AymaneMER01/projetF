-- Mettre à jour la table tasks pour ajouter la colonne assigned_to
ALTER TABLE tasks 
MODIFY COLUMN status ENUM('À faire', 'En cours', 'Terminé') DEFAULT 'À faire';

-- Vérifier si la colonne assigned_to existe déjà, et l'ajouter si elle n'existe pas
SET @exists := (
    SELECT COUNT(*)
    FROM information_schema.columns
    WHERE table_name = 'tasks'
    AND table_schema = 'projet'
    AND column_name = 'assigned_to'
);

SET @query = IF(@exists = 0, 
    'ALTER TABLE tasks ADD COLUMN assigned_to INT DEFAULT NULL, ADD CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL',
    'SELECT "La colonne assigned_to existe déjà."'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt; 