<?php
// ============================================
// MIDDLEWARE D'AUTHENTIFICATION ADMIN
// ============================================

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Vérification supplémentaire du rôle
if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
    session_destroy();
    header('Location: ../login.php');
    exit();
}
?>