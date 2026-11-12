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

// Ajouter une plage
if (isset($_POST['envoyer'])) {

    $nom_plage = $_POST['nom_plage'];

    $req = $db->prepare(
        'INSERT INTO Plage (nom_plage) VALUES (?)'
    );

    $req->execute([$nom_plage]);

    $message = "Plage ajoutée avec succès !";
}

// Récupérer les plages
$req = $db->prepare('SELECT * FROM Plage');
$req->execute();
$plages = $req->fetchAll();
?>

<h1>Gestion des plages</h1>

<?php if (isset($message)) { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<h2>Ajouter une plage</h2>

<form method="POST">

    <label>Nom de la plage :</label>
    <input type="text" name="nom_plage" required>

    <button type="submit" name="envoyer">Ajouter</button>

</form>

<h2>Liste des plages</h2>

<ul>
    <?php foreach ($plages as $plage) { ?>
        <li>
            <?php echo htmlspecialchars($plage['nom_plage']); ?>
        </li>
    <?php } ?>
</ul>

<br>

<a href="dashboard.php">Retour au tableau de bord</a>