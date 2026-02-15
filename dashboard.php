<?php
// ============================================
// DASHBOARD ADMIN - VERSION SÉCURISÉE
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

$page_title = 'Dashboard - Admin';

// Récupérer les statistiques
$total_posts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn() ?: 0;
$published_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'")->fetchColumn() ?: 0;
$draft_posts = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'draft'")->fetchColumn() ?: 0;
$pending_comments = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn() ?: 0;
$total_comments = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn() ?: 0;

// Récupérer les articles récents
$recent_posts = $pdo->query("
    SELECT p.*, 
           (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
    FROM posts p
    ORDER BY p.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Récupérer les commentaires récents
$recent_comments = $pdo->query("
    SELECT c.*, p.title as post_title 
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Récupérer les statistiques de vues
$total_views = $pdo->query("SELECT COUNT(*) FROM post_views")->fetchColumn() ?: 0;
$popular_posts = $pdo->query("
    SELECT p.*, COUNT(pv.id) as view_count
    FROM posts p
    LEFT JOIN post_views pv ON p.id = pv.post_id
    WHERE p.status = 'published'
    GROUP BY p.id
    ORDER BY view_count DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC) ?: [];

// Fonction pour tronquer le texte
function truncate($string, $length) {
    return strlen($string) > $length ? substr($string, 0, $length) . '...' : $string;
}

// Fonction pour formater la date
function formatDate($datetime) {
    return date('d M Y', strtotime($datetime));
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../includes/assets/css/style.css">
</head>
<body class="admin-body">

<!-- ================= Navbar ================= -->
<nav class="navbar navbar-expand-lg navbar-dark admin-navbar sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i> Admin Dashboard
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="create_post.php"><i class="fas fa-plus me-1"></i> Nouvel Article</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_comments.php">
                        <i class="fas fa-comments me-1"></i> Commentaires
                        <?php if($pending_comments > 0): ?>
                            <span class="badge bg-danger"><?= $pending_comments ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">
                    <i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
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

<!-- ================= Container ================= -->
<div class="container-fluid py-4">

    <!-- ====== Stats Cards ====== -->
    <div class="row">
        <?php
        $stats = [
            ['icon'=>'fa-newspaper','count'=>$total_posts,'label'=>'Total Articles','color'=>'primary'],
            ['icon'=>'fa-check-circle','count'=>$published_posts,'label'=>'Publiés','color'=>'success'],
            ['icon'=>'fa-file-alt','count'=>$draft_posts,'label'=>'Brouillons','color'=>'warning'],
            ['icon'=>'fa-comment-dots','count'=>$pending_comments,'label'=>'Commentaires en attente','color'=>'danger'],
            ['icon'=>'fa-eye','count'=>$total_views,'label'=>'Total Vues','color'=>'info'],
            ['icon'=>'fa-comments','count'=>$total_comments,'label'=>'Total Commentaires','color'=>'purple'],
        ];
        foreach($stats as $stat):
        ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card stat-<?= $stat['color'] ?>">
                <div class="stat-icon">
                    <i class="fas <?= $stat['icon'] ?>"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $stat['count'] ?></h3>
                    <p><?= $stat['label'] ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <!-- ====== Recent Posts ====== -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-newspaper me-2"></i> Articles Récents</h5>
                    <a href="create_post.php" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i> Nouvel Article</a>
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
                                <?php foreach($recent_posts as $post): ?>
                                <tr>
                                    <td>
                                        <?php if(!empty($post['image']) && $post['image']!=='default.jpg'): ?>
                                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" width="50" height="50" class="rounded">
                                        <?php else: ?>
                                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center" style="width:50px;height:50px">
                                                <i class="fas fa-image text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($post['title']) ?></strong><br><small class="text-muted"><?= formatDate($post['created_at']) ?></small></td>
                                    <td>
                                        <span class="badge <?= $post['status']==='published'?'bg-success-custom':'bg-warning-custom' ?>">
                                            <?= $post['status']==='published'?'Publié':'Brouillon' ?>
                                        </span>
                                    </td>
                                    <td><?= $post['comment_count'] ?></td>
                                    <td>
                                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                        <a href="delete_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')"><i class="fas fa-trash"></i></a>
                                        <?php if($post['status']==='published'): ?>
                                            <a href="../article.php?slug=<?= htmlspecialchars($post['slug']) ?>" target="_blank" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($recent_posts)): ?>
                                    <tr><td colspan="5" class="text-center text-muted">Aucun article</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ====== Popular Posts ====== -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-fire me-2"></i> Articles Populaires</h5></div>
                <div class="card-body">
                    <?php foreach($popular_posts as $post): ?>
                    <div class="popular-post-item mb-3 d-flex align-items-center">
                        <?php if(!empty($post['image']) && $post['image']!=='default.jpg'): ?>
                            <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" width="60" height="60" class="rounded me-3">
                        <?php else: ?>
                            <div class="rounded bg-secondary d-flex align-items-center justify-content-center me-3" style="width:60px;height:60px">
                                <i class="fas fa-image text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?= htmlspecialchars($post['title']) ?></h6>
                            <small class="text-muted"><i class="fas fa-eye me-1"></i><?= $post['view_count'] ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($popular_posts)) echo "<p class='text-center text-muted'>Aucun article</p>"; ?>
                </div>
            </div>
        </div>

        <!-- ====== Recent Comments ====== -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i> Commentaires Récents</h5>
                    <a href="manage_comments.php" class="btn btn-primary btn-sm">Gérer tous</a>
                </div>
                <div class="card-body">
                    <?php if(count($recent_comments) > 0): ?>
                        <?php foreach($recent_comments as $comment): ?>
                        <div class="comment-item mb-3 p-3 rounded bg-dark text-white">
                            <div class="d-flex justify-content-between mb-2">
                                <strong><?= htmlspecialchars($comment['author'] ?? 'Anonyme') ?></strong>
                                <span class="badge <?= $comment['status']==='approved'?'bg-success-custom':'bg-warning-custom' ?>">
                                    <?= $comment['status']==='approved'?'Approuvé':'En attente' ?>
                                </span>
                            </div>
                            <p class="mb-2 small text-muted">Sur: <?= htmlspecialchars($comment['post_title'] ?? '') ?></p>
                            <p class="mb-0 small"><?= truncate(htmlspecialchars($comment['content'] ?? ''), 100) ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Aucun commentaire</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ====== Quick Actions ====== -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0"><i class="fas fa-bolt me-2"></i> Actions Rapides</h5></div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="create_post.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Créer un nouvel article</a>
                        <a href="manage_comments.php" class="btn btn-info"><i class="fas fa-comments me-2"></i> Gérer les commentaires</a>
                        <a href="../index.php" target="_blank" class="btn btn-success"><i class="fas fa-eye me-2"></i> Voir le site public</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* ===== Styles Dashboard ===== */
.admin-body {background-color: #1a1a2e;}
.admin-navbar {background: linear-gradient(135deg, #44121C, #872E32);box-shadow: 0 4px 6px rgba(0,0,0,0.3);}
.stat-card {background: linear-gradient(135deg,#16213e,#1a1a2e);border-radius: .5rem;padding:1.5rem;display:flex;align-items:center;transition:all .3s ease;box-shadow:0 4px 6px rgba(0,0,0,0.3);}
.stat-card:hover{transform:translateY(-5px);box-shadow:0 8px 12px rgba(0,0,0,0.4);}
.stat-icon{width:60px;height:60px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;margin-right:1rem;}
.stat-primary .stat-icon{background: linear-gradient(135deg,#AC3940,#872E32);color:white;}
.stat-success .stat-icon{background: linear-gradient(135deg,#10b981,#059669);color:white;}
.stat-warning .stat-icon{background: linear-gradient(135deg,#f59e0b,#d97706);color:white;}
.stat-danger .stat-icon{background: linear-gradient(135deg,#ef4444,#dc2626);color:white;}
.stat-info .stat-icon{background: linear-gradient(135deg,#3b82f6,#2563eb);color:white;}
.stat-purple .stat-icon{background: linear-gradient(135deg,#8b5cf6,#7c3aed);color:white;}
.stat-info h3,.stat-info p{margin:0;color:#e0e0e0;}
.stat-info h3{font-size:2rem;font-weight:700;}
.stat-info p{color:#a0a0a0;font-size:.875rem;}
.popular-post-item{padding-bottom:.75rem;border-bottom:1px solid #2a2a4a;}
.popular-post-item:last-child{border-bottom:none;padding-bottom:0;}
.comment-item{background-color:#16213e !important;color:white;}
.bg-success-custom{background-color:#28a745 !important;}
.bg-warning-custom{background-color:#ffc107 !important;}
</style>
