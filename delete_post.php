
<?php
// ============================================
// SUPPRIMER UN ARTICLE
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

// R\u00e9cup\u00e9rer l'ID de l'article
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: dashboard.php');
    exit();
}

// R\u00e9cup\u00e9rer l'article
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
$stmt->execute(['id' => $id]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: dashboard.php');
    exit();
}

// Supprimer l'image si elle n'est pas default.jpg
if ($post['image'] !== 'default.jpg' && file_exists('../uploads/' . $post['image'])) {
    unlink('../uploads/' . $post['image']);
}

// Supprimer l'article (les commentaires seront supprim\u00e9s automatiquement gr\u00e2ce \u00e0 ON DELETE CASCADE)
try {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    $_SESSION['success'] = 'Article supprim\u00e9 avec succ\u00e8s !';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erreur lors de la suppression de l'article : ' . $e->getMessage();
}

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit();
?>
