<?php
// ============================================
// DASHBOARD ADMIN
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

$page_title = 'Dashboard - Admin';

// Récupérer les statistiques
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$published_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn();
$draft_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn();
$pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
$total_comments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();

// Récupérer les articles récents
$recent_posts = $pdo->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p
    ORDER BY p.created_at DESC
    LIMIT 5
")->fetchAll();

// Récupérer les commentaires récents
$recent_comments = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
")->fetchAll();

// Récupérer les statistiques de vues
$total_views = $pdo->query("SELECT COUNT(*) FROM post_views")->fetchColumn();
$popular_posts = $pdo->query("
    SELECT p.*, COUNT(pv.id) as view_count
    FROM posts p
    LEFT JOIN post_views pv ON p.id = pv.post_id
    WHERE p.status = 'published'
    GROUP BY p.id
    ORDER BY view_count DESC
    LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../includes/assets/css/style.css">
</head>
<body class="admin-body">
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>
                Admin Dashboard
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_post.php">
                            <i class="fas fa-plus me-1"></i> Nouvel Article
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_comments.php">
                            <i class="fas fa-comments me-1"></i> Commentaires
                            <?php if ($pending_comments > 0): ?>
                                <span class="badge bg-danger"><?= $pending_comments ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white">
                        <i class="fas fa-user-circle me-2"></i>
                        <?= htmlspecialchars($_SESSION['admin_username']) ?>
                    </span>
                    <a href="../index.php" class="btn btn-outline-light btn-sm" target="_blank">
                        <i class="fas fa-eye me-1"></i> Voir le site
                    </a>
                    <a href="../logout.php" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Stats Cards -->
            <div class="col-12 mb-4">
                <h2 class="admin-title mb-4">
                    <i class="fas fa-chart-line me-2"></i> Statistiques
                </h2>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $total_posts ?></h3>
                        <p>Total Articles</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $published_posts ?></h3>
                        <p>Publiés</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $draft_posts ?></h3>
                        <p>Brouillons</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <i class="fas fa-comment-dots"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $pending_comments ?></h3>
                        <p>Commentaires en attente</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $total_views ?></h3>
                        <p>Total Vues</p>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stat-card stat-purple">
                    <div class="stat-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $total_comments ?></h3>
                        <p>Total Commentaires</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Recent Posts -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-newspaper me-2"></i>Articles Récents</h5>
                        <a href="create_post.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Nouvel Article
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Titre</th>
                                        <th>Statut</th>
                                        <th>Commentaires</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <?php if ($post['image'] && $post['image'] !== 'default.jpg'): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" 
                                                         alt="" class="rounded" width="50" height="50">
                                                <?php else: ?>
                                                    <div class="rounded bg-secondary d-flex align-items-center justify-content-center" 
                                                         style="width:50px;height:50px">
                                                        <i class="fas fa-image text-white"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($post['title']) ?></strong>
                                                <br><small class="text-muted"><?= formatDate($post['created_at']) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($post['status'] === 'published'): ?>
                                                    <span class="badge bg-success-custom">Publié</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning-custom">Brouillon</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $post['comment_count'] ?></td>
                                            <td>
                                                <a href="edit_post.php?id=<?= $post['id'] ?>" 
                                                   class="btn btn-sm btn-info" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_post.php?id=<?= $post['id'] ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirmDelete('Êtes-vous sûr de vouloir supprimer cet article ?')"
                                                   title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <?php if ($post['status'] === 'published'): ?>
                                                    <a href="../article.php?slug=<?= htmlspecialchars($post['slug']) ?>" 
                                                       target="_blank" class="btn btn-sm btn-success" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Popular Posts -->
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Articles Populaires</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($popular_posts as $post): ?>
                            <div class="popular-post-item mb-3">
                                <div class="d-flex align-items-center">
                                    <?php if ($post['image'] && $post['image'] !== 'default.jpg'): ?>
                                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" 
                                             alt="" class="rounded me-3" width="60" height="60">
                                    <?php else: ?>
                                        <div class="rounded bg-secondary d-flex align-items-center justify-content-center me-3" 
                                             style="width:60px;height:60px">
                                            <i class="fas fa-image text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i><?= $post['view_count'] ?> vues
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Comments -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Commentaires Récents</h5>
                        <a href="manage_comments.php" class="btn btn-primary btn-sm">
                            Gérer tous
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (count($recent_comments) > 0): ?>
                            <?php foreach ($recent_comments as $comment): ?>
                                <div class="comment-item mb-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong><?= htmlspecialchars($comment['author'] ?? 'Anonyme') ?></strong>
                                        <span class="badge <?= $comment['status'] === 'approved' ? 'bg-success-custom' : 'bg-warning-custom' ?>">
                                            <?= $comment['status'] === 'approved' ? 'Approuvé' : 'En attente' ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small text-muted">
    <?= htmlspecialchars($comment['email'] ?? '') ?>
</p>
<p class="mb-0 small"><?= truncate(htmlspecialchars($comment['content'] ?? ''), 100) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">Aucun commentaire</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Actions Rapides</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="create_post.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Créer un nouvel article
                            </a>
                            <a href="manage_comments.php" class="btn btn-info">
                                <i class="fas fa-comments me-2"></i>Gérer les commentaires
                            </a>
                            <a href="../index.php" target="_blank" class="btn btn-success">
                                <i class="fas fa-eye me-2"></i>Voir le site public
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../includes/assets/js/script.js"></script>
</body>
</html>

<style>
.admin-body {
    background-color: #1a1a2e;
}

.admin-navbar {
    background: linear-gradient(135deg, #44121C, #872E32);
    box-shadow: var(--shadow-md);
}

.admin-title {
    color: #FDC7AE;
    font-weight: 600;
}

.stat-card {
    background: linear-gradient(135deg, #16213e, #1a1a2e);
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.stat-primary .stat-icon {
    background: linear-gradient(135deg, #AC3940, #872E32);
    color: white;
}

.stat-success .stat-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stat-warning .stat-icon {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
}

.stat-danger .stat-icon {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.stat-info .stat-icon {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
}

.stat-purple .stat-icon {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
}

.stat-info h3,
.stat-info p {
    margin: 0;
    color: #e0e0e0;
}

.stat-info h3 {
    font-size: 2rem;
    font-weight: 700;
}

.stat-info p {
    color: #a0a0a0;
    font-size: 0.875rem;
}

.popular-post-item {
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #2a2a4a;
}

.popular-post-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.comment-item {
    background-color: #16213e !important;
}
</style>