<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Récupérer les informations de l'utilisateur
    $stmt = $pdo->prepare("SELECT username, email, bio, avatar FROM utilisateurs WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
} else {
    echo json_encode(['error' => 'Utilisateur non connecté']);
}
?>
