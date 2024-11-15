<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");  // Autorise l'origine spécifique
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Méthodes autorisées
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token"); // En-têtes autorisés
header("Access-Control-Allow-Credentials: true"); // Autorise l'envoi de cookies

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
    // Récupérer les données envoyées par le front-end
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // Vérifier si les champs sont remplis
    if (!$email || !$password) {
        echo "Email et mot de passe sont requis.";
        http_response_code(400); // Mauvaise requête
        exit();
    }

    // Rechercher l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Utilisateur non trouvé
        echo "Utilisateur non trouvé.";
        http_response_code(404); // Not found
        exit();
    } else {
        // Vérifier si le mot de passe correspond
        if (password_verify($password, $user['password'])) {
            // Connexion réussie
            echo json_encode(["success" => true, "message" => "Connexion réussie."]);
        } else {
            // Mot de passe incorrect
            echo json_encode(["success" => false, "message" => "Mot de passe incorrect."]);
            http_response_code(401); // Non autorisé
        }
    }
}
?>
