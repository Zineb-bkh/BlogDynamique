<?php
$page_title = 'Accueil - Blog Moderne';
$page_description = 'Découvrez nos derniers articles sur notre blog moderne';

require_once 'includes/db.php';

// Récupérer le terme de recherche
$search = isset($_GET['search']) ? cleanInput($_GET['search']) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 6;
$offset = ($page - 1) * $per_page;

// Count total articles
if ($search) {
    $countStmt = $pdo->prepare("
        SELECT COUNT(*) FROM posts 
        WHERE status = 'published' AND (title LIKE :search OR content LIKE :search2)
    ");
    $countStmt->execute([
        ':search'  => "%$search%",
        ':search2' => "%$search%"
    ]);
} else {
    $countStmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
}
$total_articles = $countStmt->fetchColumn();
$total_pages = ceil($total_articles / $per_page);

// Récupérer les articles
if ($search) {
    $stmt = $pdo->prepare("
        SELECT p.*, 
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count
        FROM posts p
        WHERE p.status = 'published' AND (p.title LIKE :search OR p.content LIKE :search2)
        ORDER BY p.created_at DESC
        LIMIT $offset, $per_page
    ");
    $stmt->execute([
        ':search'  => "%$search%",
        ':search2' => "%$search%"
    ]);
} else {
    $stmt = $pdo->prepare("
        SELECT p.*, 
               (SELECT COUNT(*) FROM comments WHERE post_id = p.id AND status = 'approved') as comment_count
        FROM posts p
        WHERE p.status = 'published'
        ORDER BY p.created_at DESC
        LIMIT $offset, $per_page
    ");
    $stmt->execute();
}

$articles = $stmt->fetchAll();


require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="display-4 fw-bold mb-3">Bienvenue sur notre Blog</h1>
                <p class="lead mb-4">Découvrez nos derniers articles et partagez vos idées avec notre communauté.</p>
                <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                    <a href="admin/dashboard.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Créer un article
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center mt-4 mt-lg-0">
                <i class="fas fa-newspaper fa-7x text-primary opacity-25"></i>
            </div>
        </div>
    </div>
</section>

<!-- Search Box -->
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="search-box">
                <form method="GET" action="">
                    <div class="input-group input-group-lg">
                        <input type="text" 
                               class="form-control" 
                               name="search" 
                               placeholder="Rechercher un article..." 
                               value="<?= htmlspecialchars($search) ?>"
                               autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Articles Grid -->
<div class="container">
    <div class="row">
        <div class="col-12 mb-4">
            <h2 class="section-title">
                <i class="fas fa-newspaper me-2"></i>
                <?= $search ? 'Résultats de recherche' : 'Derniers articles' ?>
                <?php if ($search): ?>
                    <span class="badge bg-info-custom ms-2"><?= $total_articles ?></span>
                <?php endif; ?>
            </h2>
        </div>
    </div>
    
    <?php if (count($articles) > 0): ?>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <article class="article-card">
                        <div class="article-image">
                            <?php if ($article['image'] && $article['image'] !== 'default.jpg'): ?>
                                <img src="uploads/<?= htmlspecialchars($article['image']) ?>" 
                                     alt="<?= htmlspecialchars($article['title']) ?>">
                            <?php else: ?>
                                <img src="includes/assets/images/default-article.jpg" 
                                     alt="<?= htmlspecialchars($article['title']) ?>"
                                     onerror="this.src='https://via.placeholder.com/800x400/AC3940/ffffff?text=Article'">
                            <?php endif; ?>
                        </div>
                        
                        <div class="article-content">
                            <h3 class="article-title">
                                <a href="article.php?slug=<?= htmlspecialchars($article['slug']) ?>">
                                    <?= htmlspecialchars($article['title']) ?>
                                </a>
                            </h3>
                            
                            <div class="article-meta">
                                <span>
                                    <i class="far fa-calendar"></i>
                                    <?= formatDate($article['created_at']) ?>
                                </span>
                                <span>
                                    <i class="far fa-comment"></i>
                                    <?= $article['comment_count'] ?> commentaire<?= $article['comment_count'] > 1 ? 's' : '' ?>
                                </span>
                            </div>
                            
                            <p class="article-excerpt">
                                <?= truncate(strip_tags($article['content']), 150) ?>
                            </p>
                            
                            <div class="article-footer">
                                <a href="article.php?slug=<?= htmlspecialchars($article['slug']) ?>" 
                                   class="btn btn-primary">
                                    <i class="fas fa-book-open me-1"></i> Lire la suite
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-center mt-5">
                <nav aria-label="Pagination">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?><?php if ($search) echo '&search=' . urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <li class="page-item active">
                                    <span class="page-link"><?= $i ?></span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $i ?><?php if ($search) echo '&search=' . urlencode($search); ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?><?php if ($search) echo '&search=' . urlencode($search); ?>">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <h4>Aucun article trouvé</h4>
            <p><?= $search ? 'Aucun article ne correspond à votre recherche.' : "Aucun article n'est publié pour le moment." ?></p>
            <?php if ($search): ?>
                <a href="index.php" class="btn btn-primary mt-3">
                    <i class="fas fa-times me-2"></i>Effacer la recherche
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
