<?php
// ============================================
// PAGE DÉTAIL ARTICLE
// ============================================

session_start();
require_once 'includes/db.php'; // contient $pdo et toutes les fonctions utilitaires

// Récupérer l'article via le slug
$slug = isset($_GET['slug']) ? cleanInput($_GET['slug']) : '';

if (empty($slug)) {
    header('Location: index.php');
    exit();
}

// Récupérer l'article
$stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = :slug AND status = 'published'");
$stmt->execute(['slug' => $slug]);
$article = $stmt->fetch();

if (!$article) {
    header('Location: index.php');
    exit();
}

// Incrémenter le nombre de vues
$ip_address = $_SERVER['REMOTE_ADDR'];
$viewStmt = $pdo->prepare("
    INSERT INTO post_views (post_id, user_ip) 
    VALUES (:post_id, :user_ip)
");
$viewStmt->execute([
    'post_id' => $article['id'],
    'user_ip' => $ip_address
]);

// Récupérer le nombre de vues
$viewCountStmt = $pdo->prepare("SELECT COUNT(*) FROM post_views WHERE post_id = :post_id");
$viewCountStmt->execute(['post_id' => $article['id']]);
$view_count = $viewCountStmt->fetchColumn();

// Traitement du formulaire de commentaire
$comment_error = '';
$comment_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $author = cleanInput($_POST['name']);    // correspond à la colonne "author"
    $content = cleanInput($_POST['message']); // correspond à la colonne "content"

    // Validation
    if (empty($author) || empty($content)) {
        $comment_error = 'Tous les champs sont obligatoires.';
    } elseif (strlen($content) < 10) {
        $comment_error = 'Le commentaire doit contenir au moins 10 caractères.';
    } else {
        try {
            $commentStmt = $pdo->prepare("
                INSERT INTO comments (post_id, author, content, status) 
                VALUES (:post_id, :author, :content, 'pending')
            ");
            $commentStmt->execute([
                'post_id' => $article['id'],
                'author' => $author,
                'content' => $content
            ]);
            $comment_success = 'Votre commentaire a été soumis et sera visible après modération.';
        } catch (PDOException $e) {
            $comment_error = "Erreur lors de l'enregistrement du commentaire : " . $e->getMessage();
        }
    }
}

// Récupérer les commentaires approuvés
$commentsStmt = $pdo->prepare("
    SELECT * FROM comments 
    WHERE post_id = :post_id AND status = 'approved'
    ORDER BY created_at DESC
");
$commentsStmt->execute(['post_id' => $article['id']]);
$comments = $commentsStmt->fetchAll();

// Préparer le titre et la description de la page
$page_title = htmlspecialchars($article['title']) . ' - Blog Moderne';
$page_description = truncate(strip_tags($article['content']), 150);

require_once 'includes/header.php';
?>

<!-- Article Detail -->
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Article Header -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($article['title']) ?></li>
                </ol>
            </nav>
            
            <article class="article-detail">
                <h1 class="article-title-large mb-4"><?= htmlspecialchars($article['title']) ?></h1>
                
                <div class="article-meta-large mb-4">
                    <span class="me-4">
                        <i class="far fa-calendar-alt"></i>
                        <?= formatDate($article['created_at']) ?>
                    </span>
                    <span class="me-4">
                        <i class="far fa-eye"></i>
                        <?= $view_count ?> vue<?= $view_count > 1 ? 's' : '' ?>
                    </span>
                    <span>
                        <i class="far fa-comment"></i>
                        <?= count($comments) ?> commentaire<?= count($comments) > 1 ? 's' : '' ?>
                    </span>
                </div>
                
                <!-- Article Image -->
                <?php if (!empty($article['image']) && $article['image'] !== 'default.jpg'): ?>
                    <div class="article-image-large mb-4">
                        <img src="uploads/<?= htmlspecialchars($article['image']) ?>" 
                             alt="<?= htmlspecialchars($article['title']) ?>" 
                             class="img-fluid rounded">
                    </div>
                <?php endif; ?>
                
                <!-- Article Content -->
                <div class="article-content-text mb-5">
                    <?= nl2br(htmlspecialchars($article['content'])) ?>
                </div>
                
                <!-- Share Buttons -->
                <div class="share-buttons mb-5">
                    <h5 class="mb-3">Partager cet article :</h5>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                           target="_blank" class="btn btn-primary">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>&text=<?= urlencode($article['title']) ?>" 
                           target="_blank" class="btn btn-info">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) ?>" 
                           target="_blank" class="btn btn-primary">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <button onclick="copyToClipboard(window.location.href)" class="btn btn-secondary">
                            <i class="fas fa-link"></i>
                        </button>
                    </div>
                </div>
            </article>
            
            <!-- Comments Section -->
            <div class="comment-section">
                <h2 class="mb-4">
                    <i class="fas fa-comments me-2"></i>
                    Commentaires (<?= count($comments) ?>)
                </h2>
                
                <!-- Comment Form -->
                <div class="comment-form">
                    <?php if (!empty($comment_error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?= $comment_error ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($comment_success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?= $comment_success ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="mb-3">Laisser un commentaire</h5>
                    <form method="POST" action="">
                        <input type="hidden" name="add_comment" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">Message * (min. 10 caractères)</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required minlength="10"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer le commentaire
                        </button>
                    </form>
                </div>
                
                <!-- Comments List -->
                <?php if (count($comments) > 0): ?>
                    <div class="comments-list">
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-card fade-in">
                                <div class="comment-header">
                                    <div>
                                        <span class="comment-author">
                                            <i class="fas fa-user-circle me-2"></i>
                                            <?= htmlspecialchars($comment['author']) ?>
                                        </span>
                                        <small class="text-muted ms-2">
                                            <?= formatDate($comment['created_at']) ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="comment-message">
                                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-comment-slash fa-2x mb-3"></i>
                        <h5>Aucun commentaire pour le moment</h5>
                        <p>Soyez le premier à laisser un commentaire !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
.article-title-large {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--color-primary);
    font-family: var(--font-heading);
}

.article-meta-large {
    font-size: 1rem;
    color: var(--text-secondary);
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--color-light-2);
}

.article-meta-large i {
    color: var(--color-secondary);
}

.article-image-large img {
    width: 100%;
    max-height: 500px;
    object-fit: cover;
    box-shadow: var(--shadow-lg);
}

.article-content-text {
    font-size: 1.1rem;
    line-height: 1.8;
    color: var(--text-primary);
}

.share-buttons h5 {
    color: var(--color-primary);
}

.share-buttons .btn {
    width: 45px;
    height: 45px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

<?php require_once 'includes/footer.php'; ?>
