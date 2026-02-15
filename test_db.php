<?php
require_once __DIR__ . '/includes/db.php';

try {
    $stmt = $pdo->query("SELECT NOW() AS time_now");
    $row = $stmt->fetch();
    echo "Connexion OK ! Serveur MySQL : " . $row['time_now'];
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
