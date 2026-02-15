<?php
// ============================================
// SUPPRIMER UN ARTICLE
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

// Récupérer l'ID de l'article
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header('Location: dashboard.php');
    exit();
}

// Récupérer l'article
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

// Supprimer l'article (les commentaires seront supprimés automatiquement grâce à ON DELETE CASCADE)
try {
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);
    
    $_SESSION['success'] = 'Article supprimé avec succès !';
    
} catch (PDOException $e) {
    $_SESSION['error'] = 'Erreur lors de la suppression de l\'article : ' . $e->getMessage();
}

// Rediriger vers le dashboard
header('Location: dashboard.php');
exit();
?>