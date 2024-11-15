<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $bio = $_POST['bio'];

        // Mettre à jour les données de l'utilisateur
        $stmt = $pdo->prepare("UPDATE utilisateurs SET username = :username, email = :email, bio = :bio WHERE id = :id");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'bio' => $bio,
            'id' => $userId
        ]);

        echo "Profil mis à jour avec succès.";
    }
}
?>
