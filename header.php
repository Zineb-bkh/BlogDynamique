
<?php
// ============================================
// HEADER INCLUSIF
// ============================================

// V\u00e9rifier si le mode sombre est activ\u00e9
$dark_mode = isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true';
?>
<!DOCTYPE html>
<html lang="fr" <?php if ($dark_mode): ?>data-bs-theme="dark"<?php endif; ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Blog Moderne' ?></title>
    <meta name="description" content="<?= $page_description ?? 'Blog moderne avec syst\u00e8me de commentaires et espace admin' ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="includes/assets/images/favicon.ico">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-feather-alt me-2"></i>
            <span>Blog</span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center gap-3">
                <!-- Mode sombre toggle -->
                <button class="btn btn-outline-light btn-sm" onclick="toggleDarkMode()" title="Mode sombre">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </button>
                
                <!-- Lien admin -->
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-tachometer-alt me-1"></i> Admin
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-lock me-1"></i> Admin
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Alert Container -->
<div id="alert-container"></div>
