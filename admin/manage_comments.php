<?php
// ============================================
// GÉRER LES COMMENTAIRES
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

$page_title = 'Gérer les Commentaires - Admin';

// Actions sur les commentaires
$action = isset($_GET['action']) ? $_GET['action'] : '';
$comment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($action && $comment_id) {
    try {
        switch ($action) {
            case 'approve':
                $stmt = $pdo->prepare("UPDATE comments SET status = 'approved' WHERE id = :id");
                $stmt->execute(['id' => $comment_id]);
                $_SESSION['success'] = 'Commentaire approuvé !';
                break;
            case 'reject':
                $stmt = $pdo->prepare("UPDATE comments SET status = 'pending' WHERE id = :id");
                $stmt->execute(['id' => $comment_id]);
                $_SESSION['success'] = 'Commentaire rejeté !';
                break;
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
                $stmt->execute(['id' => $comment_id]);
                $_SESSION['success'] = 'Commentaire supprimé !';
                break;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erreur : ' . $e->getMessage();
    }

    header('Location: manage_comments.php');
    exit();
}

// Filtrer par statut
$status_filter = isset($_GET['status']) ? cleanInput($_GET['status']) : 'all';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Récupérer les commentaires
if ($status_filter === 'all') {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM comments");
    $stmt = $pdo->prepare("
        SELECT c.*, p.title as post_title, p.slug as post_slug 
        FROM comments c
        JOIN posts p ON c.post_id = p.id
        ORDER BY c.created_at DESC
        LIMIT :offset, :per_page
    ");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
} else {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE status = :status");
    $countStmt->execute(['status' => $status_filter]);
    $stmt = $pdo->prepare("
        SELECT c.*, p.title as post_title, p.slug as post_slug 
        FROM comments c
        JOIN posts p ON c.post_id = p.id
        WHERE c.status = :status
        ORDER BY c.created_at DESC
        LIMIT :offset, :per_page
    ");
    $stmt->bindValue(':status', $status_filter, PDO::PARAM_STR);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':per_page', $per_page, PDO::PARAM_INT);
}

// Exécuter les requêtes
$total_comments = $countStmt->fetchColumn();
$stmt->execute();
$comments = $stmt->fetchAll();
$total_pages = ceil($total_comments / $per_page);

// Statistiques
$total_all = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$total_approved = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'approved'")->fetchColumn();
$total_pending = $pdo->query("SELECT COUNT(*) FROM comments WHERE status = 'pending'")->fetchColumn();
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
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home me-1"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_post.php"><i class="fas fa-plus me-1"></i> Nouvel Article</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_comments.php"><i class="fas fa-comments me-1"></i> Commentaires</a></li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-white"><i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                    <a href="../index.php" class="btn btn-outline-light btn-sm" target="_blank"><i class="fas fa-eye me-1"></i> Voir le site</a>
                    <a href="../logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt me-1"></i> Déconnexion</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card stat-primary">
                    <div class="stat-icon"><i class="fas fa-comments"></i></div>
                    <div class="stat-info"><h3><?= $total_all ?></h3><p>Total</p></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-success">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info"><h3><?= $total_approved ?></h3><p>Approuvés</p></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card stat-warning">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-info"><h3><?= $total_pending ?></h3><p>En attente</p></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Gestion des Commentaires</h4>
                    </div>
                    <div class="card-body">
                        <!-- Filter Tabs -->
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item"><a class="nav-link <?= $status_filter==='all'?'active':'' ?>" href="?status=all">Tous (<?= $total_all ?>)</a></li>
                            <li class="nav-item"><a class="nav-link <?= $status_filter==='pending'?'active':'' ?>" href="?status=pending">En attente (<?= $total_pending ?>)</a></li>
                            <li class="nav-item"><a class="nav-link <?= $status_filter==='approved'?'active':'' ?>" href="?status=approved">Approuvés (<?= $total_approved ?>)</a></li>
                        </ul>
                        
                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?= $_SESSION['success'] ?></div>
                            <?php unset($_SESSION['success']); ?>
                        <?php endif; ?>
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error'] ?></div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>
                        
                        <?php if(count($comments) > 0): ?>
                            <div class="comments-list">
                                <?php foreach($comments as $comment): ?>
                                    <div class="comment-item-admin p-4 mb-3">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i><?= htmlspecialchars($comment['author'] ?? 'Anonyme') ?></h5>
                                                    <span class="badge <?= $comment['status']==='approved'?'bg-success-custom':'bg-warning-custom' ?>">
                                                        <?= $comment['status']==='approved'?'Approuvé':'En attente' ?>
                                                    </span>
                                                </div>
                                                <p class="text-muted mb-2"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($comment['email'] ?? '') ?></p>
                                                <p class="text-muted mb-2"><i class="far fa-calendar me-2"></i><?= formatDate($comment['created_at'] ?? '') ?></p>
                                                <p class="mb-2"><strong><i class="fas fa-newspaper me-2"></i>Article:</strong>
                                                    <a href="../article.php?slug=<?= htmlspecialchars($comment['post_slug']) ?>" target="_blank"><?= htmlspecialchars($comment['post_title']) ?></a>
                                                </p>
                                                <div class="comment-message-admin p-3"><?= nl2br(htmlspecialchars($comment['content'] ?? '')) ?></div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="action-buttons">
                                                    <?php if($comment['status']==='pending'): ?>
                                                        <a href="?action=approve&id=<?= $comment['id'] ?>" class="btn btn-success w-100 mb-2"><i class="fas fa-check me-2"></i>Approuver</a>
                                                    <?php else: ?>
                                                        <a href="?action=reject&id=<?= $comment['id'] ?>" class="btn btn-warning w-100 mb-2"><i class="fas fa-times me-2"></i>Rejeter</a>
                                                    <?php endif; ?>
                                                    <a href="?action=delete&id=<?= $comment['id'] ?>" class="btn btn-danger w-100" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')"><i class="fas fa-trash me-2"></i>Supprimer</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Pagination -->
                            <?php if($total_pages>1): ?>
                                <nav aria-label="Pagination">
                                    <ul class="pagination">
                                        <?php if($page>1): ?><li class="page-item"><a class="page-link" href="?status=<?= $status_filter ?>&page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i></a></li><?php endif; ?>
                                        <?php for($i=1;$i<=$total_pages;$i++): ?>
                                            <?php if($i==$page): ?>
                                                <li class="page-item active"><span class="page-link"><?= $i ?></span></li>
                                            <?php else: ?>
                                                <li class="page-item"><a class="page-link" href="?status=<?= $status_filter ?>&page=<?= $i ?>"><?= $i ?></a></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                        <?php if($page<$total_pages): ?><li class="page-item"><a class="page-link" href="?status=<?= $status_filter ?>&page=<?= $page+1 ?>"><i class="fas fa-chevron-right"></i></a></li><?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-comment-slash fa-3x mb-3"></i>
                                <h5>Aucun commentaire</h5>
                                <p><?= $status_filter==='all'?'Aucun commentaire pour le moment.':'Aucun commentaire avec ce statut.' ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<style>
.comment-item-admin {
    background-color: #16213e;
    border-radius: var(--radius-lg);
    border-left: 4px solid var(--color-accent);
}

.comment-message-admin {
    background-color: #1a1a2e;
    border-radius: var(--radius-md);
    font-size: 0.95rem;
    line-height: 1.6;
}

.action-buttons .btn {
    margin-bottom: 0.5rem;
}
</style>
