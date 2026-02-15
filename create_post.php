
<?php
// ============================================
// CR\u00c9ER UN ARTICLE
// ============================================

require_once '../includes/auth.php';
require_once '../includes/db.php';

$page_title = 'Créer un Article - Admin';

$error = '';
$success = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = cleanInput($_POST['title']);
    $content = $_POST['content'];
    $status = cleanInput($_POST['status']);
    $slug = createSlug($title);
    
    // Validation
    if (empty($title) || empty($content)) {
        $error = 'Le titre et le contenu sont obligatoires.';
    } else {
        // Traitement de l'image
        $image = 'default.jpg';
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $filename = $_FILES['image']['name'];
            $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            // Vérifier l'extension
            if (!in_array($file_ext, $allowed)) {
                $error = 'Extension de fichier non autorisée. Extensions acceptées: jpg, jpeg, png, gif, webp';
            } else {
                // Vérifier la taille (max 5MB)
                if ($_FILES['image']['size'] > 5242880) {
                    $error = 'Le fichier est trop volumineux (max 5MB)';
                } else {
                    // Créer le dossier uploads s'il n'existe pas
                    if (!file_exists('../uploads')) {
                        mkdir('../uploads', 0777, true);
                    }
                    
                    // Générer un nom de fichier unique
                    $new_filename = uniqid('post_', true) . '.' . $file_ext;
                    $upload_path = '../uploads/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        $image = $new_filename;
                    } else {
                        $error = "Erreur lors du téléchargement de l'image.";
                    }
                }
            }
        }
        
        if (!$error) {
            try {
                // Vérifier si le slug existe déjà
                $checkStmt = $pdo->prepare("SELECT id FROM posts WHERE slug = :slug");
                $checkStmt->execute(['slug' => $slug]);
                
                if ($checkStmt->rowCount() > 0) {
                    $slug .= '-' . time();
                }
                
                // Insérer l'article
                $stmt = $pdo->prepare("
                    INSERT INTO posts (title, slug, content, image, status) 
                    VALUES (:title, :slug, :content, :image, :status)
                ");
                $stmt->execute([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'image' => $image,
                    'status' => $status
                ]);
                
                $success = 'Article créé avec succès !';
                
                // Rediriger après 2 secondes
                header('refresh:2;url=dashboard.php');
                
            } catch (PDOException $e) {
                $error = "Erreur lors de la création de l'article : " . $e->getMessage();
            }
        }
    }
}
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
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-home me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="create_post.php">
                            <i class="fas fa-plus me-1"></i> Nouvel Article
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_comments.php">
                            <i class="fas fa-comments me-1"></i> Commentaires
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
                        <i class="fas fa-sign-out-alt me-1"></i> Se déconnecter
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Créer un nouvel article
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?= $success ?>
                                <br><small>Redirection vers le dashboard...</small>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre *</label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="title" 
                                       name="title" 
                                       required
                                       placeholder="Entrez le titre de l'article">
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Image de couverture</label>
                                <input type="file" 
                                       class="form-control" 
                                       id="image" 
                                       name="image" 
                                       accept="image/jpeg,image/png,image/gif,image/webp">
                                <div class="form-text">Formats acceptés: JPG, PNG, GIF, WEBP (max 5MB)</div>
                                
                                <div id="imagePreview" class="mt-3" style="display: none;">
                                    <img id="preview" src="" alt="Aperçu" class="img-fluid rounded" style="max-height: 300px;">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="content" class="form-label">Contenu *</label>
                                <textarea class="form-control" 
                                          id="content" 
                                          name="content" 
                                          rows="15" 
                                          required></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Statut</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="status" 
                                           id="draft" 
                                           value="draft" 
                                           checked>
                                    <label class="form-check-label" for="draft">
                                        <i class="fas fa-file-alt me-1"></i> Brouillon
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" 
                                           type="radio" 
                                           name="status" 
                                           id="published" 
                                           value="published">
                                    <label class="form-check-label" for="published">
                                        <i class="fas fa-globe me-1"></i> Publier
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i>Créer l'article
                                </button>
                                <a href="dashboard.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialiser TinyMCE
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic backcolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
            language: 'fr_FR'
        });
        
        // Pr\u00e9visualisation de l'image
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
