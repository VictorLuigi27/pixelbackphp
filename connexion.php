<?php
session_start();

// Génère un token CSRF s'il n'existe pas déjà dans la session
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));  // Génère un token sécurisé
}

// Le token à envoyer avec le formulaire
$csrf_token = $_SESSION['csrf_token'];

// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');

// Vérifier si l'utilisateur a soumis le formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données envoyées en POST
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Si les informations sont correctes, démarrer la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo "Connexion réussie.";
        // Rediriger vers la page principale après connexion
        header('Location: api.php');
        exit;
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>
