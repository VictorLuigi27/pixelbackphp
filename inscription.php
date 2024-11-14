<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');

// Vérifier si l'utilisateur a soumis le formulaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données envoyées en POST
    $username = $_POST['username'];
    $password = $_POST['password'];  // Le mot de passe sera crypté
    $email = $_POST['email'];

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        // L'utilisateur existe déjà
        echo "Cet email est déjà utilisé.";
    } else {
        // Crypter le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $sql = "INSERT INTO utilisateurs (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['username' => $username, 'password' => $hashed_password, 'email' => $email]);

        echo "Inscription réussie.";
    }
}
?>

