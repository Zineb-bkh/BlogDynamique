
<?php
// ============================================
// PAGE DE D\u00c9CONNEXION
// ============================================

session_start();

// D\u00e9truire toutes les variables de session
$_SESSION = array();

// D\u00e9truire le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// D\u00e9truire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: login.php');
exit();
?>
