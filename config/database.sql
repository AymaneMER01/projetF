-- Création de la table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('etudiant', 'professionnel', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table des projets
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    owner_id INT NOT NULL,
    score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_projects_owner FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table de liaison entre projets et utilisateurs (membres du projet)
CREATE TABLE IF NOT EXISTS project_members (
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role VARCHAR(50) DEFAULT 'collaborateur', -- Pour éventuellement préciser le rôle dans le projet
    PRIMARY KEY (project_id, user_id),
    CONSTRAINT fk_pm_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_pm_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des tâches associées aux projets
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('À faire', 'En cours', 'Terminé') DEFAULT 'À faire',
    due_date DATE,
    assigned_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_tasks_assigned FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

-- Création de la table de messagerie interne
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    sender_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_messages_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Création de la table des documents liés aux projets
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_documents_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_documents_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);
