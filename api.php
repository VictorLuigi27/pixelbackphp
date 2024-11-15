<?php
// Configuration de la connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ajouter les en-têtes CORS pour permettre l'accès depuis ton front-end React
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');  // Ajouter OPTIONS ici
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Si la méthode est OPTIONS (pré-vérification CORS), on répond juste
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);  // OK
    exit();
}

// Vérification de la méthode et de la route
$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Log de la route pour déboguer
error_log("Route demandée : $request_uri");

// Routes pour les jeux et les catégories
if (strpos($request_uri, 'api.php') !== false) {  // Vérifie si l'URL contient api.php
    switch ($request_method) {
        case 'GET':
            // Vérifie si une route spécifique pour les catégories est demandée
            if (isset($_GET['endpoint']) && $_GET['endpoint'] === 'categories') {
                getCategories($pdo); // Récupérer toutes les catégories
            } elseif (isset($_GET['endpoint']) && $_GET['endpoint'] === 'game' && isset($_GET['id'])) {
                getGameById($pdo, $_GET['id']); // Récupérer un jeu spécifique par son id
            } else {
                getGames($pdo); // Récupérer tous les jeux
            }
            break;
        case 'POST':
            addGame($pdo); // Ajouter un jeu
            break;
        default:
            header("HTTP/1.1 405 Method Not Allowed");
            echo json_encode(['error' => 'Méthode non autorisée']);
            break;
    }
} else {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Route non trouvée']);
}

// Fonction pour récupérer un jeu spécifique par son id
function getGameById($pdo, $id)
{
    // Préparer la requête pour récupérer le jeu par son id
    $sql = "SELECT * FROM games WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id]);

    // Récupérer les résultats
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($game) {
        echo json_encode($game);  // Si le jeu existe, le renvoyer en JSON
    } else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(['error' => 'Jeu non trouvé']);
    }
}


// Fonction pour récupérer tous les jeux
function getGames($pdo)
{
    $query = $pdo->query('SELECT * FROM games');
    $games = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($games);
}

// Fonction pour récupérer toutes les catégories
function getCategories($pdo)
{
    $query = $pdo->query('SELECT * FROM categories');
    $categories = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['categories' => $categories]);
}

// Fonction pour ajouter un jeu
function addGame($pdo)
{
    $data = json_decode(file_get_contents("php://input"), true); 

    if (isset($data['title'], $data['summary'], $data['release_date'], $data['author'], $data['categories'], $data['picture'], $data['price'])) {
        $sql = "INSERT INTO games (title, summary, release_date, author, categories, picture, price) 
                VALUES (:title, :summary, :release_date, :author, :categories, :picture, :price)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $data['title'],
            ':summary' => $data['summary'],
            ':release_date' => $data['release_date'],
            ':author' => $data['author'],
            ':categories' => $data['categories'],
            ':picture' => $data['picture'],
            ':price' => $data['price']  // Assure-toi que price est envoyé dans les données
        ]);
        echo json_encode(['message' => 'Le jeu a bien été ajouté.']);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'Données manquantes pour l\'ajout du jeu']);
    }
}


// Fonction pour supprimer un jeu
function deleteGame($pdo)
{
    $data = json_decode(file_get_contents("php://input"), true); 

    if (isset($data['id'])) {
        $sql = "DELETE FROM games WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $data['id']]);
        echo json_encode(['message' => 'Le jeu a été supprimé avec succès.']);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(['error' => 'ID du jeu manquant']);
    }
}   
?>
