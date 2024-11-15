<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173"); // Permet l'accès depuis votre front-end.
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");


// Pré-vol pour OPTIONS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');
    // echo "Connexion réussie."; // Supprimez ceci pour éviter de renvoyer un contenu inutile
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    http_response_code(500);
    exit();
}

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $email = $_POST['email'] ?? null;

    // Vérifier si tous les champs sont remplis
    if (!$username || !$password || !$email) {
        echo "Tous les champs sont requis.";
        http_response_code(400);
        exit();
    }

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        echo "Cet email est déjà utilisé.";
        http_response_code(409); // Code pour "conflit"
    } else {
        // Crypter le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
        $stmt = $pdo->prepare($sql);

        // Inside the registration block
        if ($stmt->execute(['username' => $username, 'password' => $hashed_password, 'email' => $email])) {
            echo json_encode(["success" => true, "message" => "Inscription réussie."]);
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "Erreur lors de l'inscription."]);
            http_response_code(500);
        }

        error_log(print_r($_POST, true)); // This will log the received POST data
        error_log("User registration: " . json_encode($user)); // Log user data for debugging


    }
}
?>


