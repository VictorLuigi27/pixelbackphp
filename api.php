<?php
// Configuration de la connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=pixelback', 'root', '270902102Luigi+');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Récupérer tous les jeux
    $query = $pdo->query('SELECT * FROM games');
    $games = $query->fetchAll(PDO::FETCH_ASSOC);

    // Renvoyer les jeux sous forme de JSON
    header('Content-Type: application/json');
    echo json_encode($games);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier l'action (ajouter, supprimer, mettre à jour)
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'ajouter':
                ajouterJeu($pdo);
                break;
            case 'supprimer':
                supprimerJeu($pdo);
                break;
            case 'mettre_a_jour':
                mettreAJourJeu($pdo); // Appel de la fonction pour mettre à jour un jeu
                break;
            default:
                echo "Action non reconnue.";
        }
    }
}


// Fonction pour ajouter un jeu
function ajouterJeu($pdo)
{
    // Récupérer les données envoyées en POST
    $title = $_POST['title'];
    $summary = $_POST['summary'];
    $release_date = $_POST['release_date'];
    $author = $_POST['author'];
    $categories = $_POST['categories'];
    $image = $_POST['picture'];

    // Requête SQL pour insérer un jeu
    $sql = "INSERT INTO games (title, summary, release_date, author, categories, picture) 
            VALUES (:title, :summary, :release_date, :author, :categories, :picture)";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête avec les données
    $stmt->execute([
        ':title' => $title,
        ':summary' => $summary,
        ':release_date' => $release_date,
        ':author' => $author,
        ':categories' => $categories,
        ':picture' => $image
    ]);

    echo "Le jeu a bien été ajouté.";
}

// Fonction pour supprimer un jeu
function supprimerJeu($pdo)
{
    // Récupérer l'ID du jeu à supprimer
    $id_jeu = $_POST['id'];

    // Requête SQL pour supprimer le jeu
    $sql = "DELETE FROM games WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête
    $stmt->execute([':id' => $id_jeu]);

    echo "Le jeu a été supprimé avec succès.";
}

// Fonction pour mettre à jour un jeu
function mettreAJourJeu($pdo)
{
    // Récupérer les données envoyées en POST
    $id_jeu = $_POST['id']; // L'ID du jeu à mettre à jour
    $title = $_POST['title'];
    $summary = $_POST['summary'];
    $release_date = $_POST['release_date'];
    $author = $_POST['author'];
    $categories = $_POST['categories'];
    $picture = $_POST['picture'];

    // Requête SQL pour mettre à jour un jeu
    $sql = "UPDATE jeux SET title = :title, summary = :summary, release_date = :release_date, 
            author = :author, categories = :categories, picture = :picture 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Exécuter la requête avec les nouvelles données
    $stmt->execute([
        ':id' => $id_jeu,
        ':title' => $title,
        ':summary' => $summary,
        ':release_date' => $release_date,
        ':author' => $author,
        ':categories' => $categories,
        ':picture_url' => $picture
    ]);

    echo "Le jeu a été mis à jour avec succès.";
}

?>
