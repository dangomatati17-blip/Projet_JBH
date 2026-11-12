<?php
session_start();

// Vérifier si l'utilisateur est administrateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include_once "config.php";

$db = new PDO(
    'mysql:host='.Config::HOST.';dbname='.Config::DBNAME,
    Config::USER,
    Config::PASSWORD
);

// Ajouter une étude
if (isset($_POST['envoyer'])) {

    $nom_etude = $_POST['nom_etude'];

    $req = $db->prepare(
        'INSERT INTO Etudes (nom_etude) VALUES (?)'
    );

    $req->execute([$nom_etude]);

    $message = "Étude ajoutée avec succès !";
}

// Récupérer les études
$req = $db->prepare('SELECT * FROM Etudes');
$req->execute();
$etudes = $req->fetchAll();
?>

<h1>Gestion des études</h1>

<?php if (isset($message)) { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<h2>Ajouter une étude</h2>

<form method="POST">

    <label>Nom de l'étude :</label>
    <input type="text" name="nom_etude" required>

    <button type="submit" name="envoyer">Ajouter</button>

</form>

<h2>Liste des études</h2>

<ul>
    <?php foreach ($etudes as $etude) { ?>
        <li>
            <?php echo htmlspecialchars($etude['nom_etude']); ?>
        </li>
    <?php } ?>
</ul>

<a href="dashboard.php">Retour au tableau de bord</a>