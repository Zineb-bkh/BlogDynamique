<?php
$host = "sql312.infinityfree.com";  // host exact donné
$user = "if0_41167157"; 
$password = "45Xju.YIO@xYy"; // même que MySQL
$dbname = "if0_41167157_blog_system";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connexion échouée: " . $conn->connect_error);
}
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur connexion : " . $e->getMessage());
}
?>
