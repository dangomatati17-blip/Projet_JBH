<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Vérifier que l'utilisateur est un préleveur
if ($_SESSION['user']['role'] != 'preleveur') {
    header("Location: dashboard.php");
    exit();
}

include_once "config.php";

$db = new PDO(
    'mysql:host='.Config::HOST.';dbname='.Config::DBNAME.';charset=utf8',
    Config::USER,
    Config::PASSWORD
);

// Récupérer les plages
$req = $db->prepare('SELECT * FROM Plage');
$req->execute();
$plages = $req->fetchAll();

// Récupérer les études
$req = $db->prepare('SELECT * FROM Etudes');
$req->execute();
$etudes = $req->fetchAll();

// Enregistrer le relevé GPS
if (isset($_POST['envoyer'])) {

    $date = $_POST['date_collecte'];
    $plage = $_POST['id_plage'];
    $etude = $_POST['id_etude'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $req = $db->prepare(
        'INSERT INTO Collecte_des_donnes
        (date_collecte, id_plage, id_etude, latitude, longitude)
        VALUES (?, ?, ?, ?, ?)'
    );

    $req->execute([$date, $plage, $etude, $latitude, $longitude]);

    $message = "Le relevé GPS a bien été enregistré.";
}
?>

<h1>Saisie des relevés GPS</h1>

<?php if (isset($message)) { ?>
    <p><?php echo $message; ?></p>
<?php } ?>

<form method="POST">

    <label>Date de collecte :</label>
    <input type="date" name="date_collecte" required>

    <br><br>

    <label>Plage :</label>
    <select name="id_plage" required>
        <option value="">Choisir une plage</option>

        <?php foreach ($plages as $plage) { ?>
            <option value="<?php echo $plage['id_plage']; ?>">
                <?php echo htmlspecialchars($plage['nom_plage']); ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Étude :</label>
    <select name="id_etude" required>
        <option value="">Choisir une étude</option>

        <?php foreach ($etudes as $etude) { ?>
            <option value="<?php echo $etude['id_etude']; ?>">
                <?php echo htmlspecialchars($etude['nom_etude']); ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Latitude :</label>
    <input type="number" step="any" name="latitude" required>

    <br><br>

    <label>Longitude :</label>
    <input type="number" step="any" name="longitude" required>

    <br><br>

    <button type="submit" name="envoyer">Enregistrer</button>

</form>

<br>

<a href="dashboard.php">Retour au tableau de bord</a>