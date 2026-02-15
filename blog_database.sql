-- ============================================
-- BLOG DYNAMIQUE - STRUCTURE DE LA BASE DE DONN\u00c9ES
-- ============================================


CREATE DATABASE IF NOT EXISTS blog_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_system;


-- ============================================
-- TABLE USERS (Administrateurs)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- TABLE POSTS (Articles)
-- ============================================
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT NOT NULL,
    image VARCHAR(255) DEFAULT 'default.jpg',
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_status (status),
    KEY idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- TABLE COMMENTS (Commentaires)
-- ============================================
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'approved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    KEY idx_post_id (post_id),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- COMPTEUR DE VUES (Optionnel - Bonus)
-- ============================================
CREATE TABLE IF NOT EXISTS post_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    UNIQUE KEY unique_view (post_id, ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================
-- UTILISATEUR ADMIN PAR D\u00c9FAUT
-- Mot de passe: admin123
-- ============================================
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE username=username;


-- ============================================
-- ARTICLES D'EXEMPLE (Optionnel)
-- ============================================
INSERT INTO posts (title, slug, content, image, status) VALUES
('Bienvenue sur votre nouveau blog', 'bienvenue-sur-votre-nouveau-blog', 
'Ceci est le premier article de votre blog. Vous pouvez le modifier ou le supprimer depuis le panneau d''administration. Profitez de toutes les fonctionnalit\u00e9s offertes par ce syst\u00e8me de blog moderne!', 
'default.jpg', 'published'),
('Guide pour d\u00e9marrer', 'guide-pour-demarrer', 
'D\u00e9couvrez comment cr\u00e9er, modifier et g\u00e9rer vos articles facilement. Utilisez le panneau d''administration pour un contr\u00f4le total sur votre contenu.', 
'default.jpg', 'published')
ON DUPLICATE KEY UPDATE title=title;


-- ============================================
-- FIN DE LA STRUCTURE
-- ============================================