<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'preleveur') {
    header("Location: login.php");
    exit();
}

include_once "config.php";

$db = new PDO(
    'mysql:host='.Config::HOST.';dbname='.Config::DBNAME,
    Config::USER,
    Config::PASSWORD
);

// Récupérer les collectes
$req = $db->prepare('SELECT * FROM Collecte_des_donnes');
$req->execute();
$collectes = $req->fetchAll();

// Récupérer les espèces
$req = $db->prepare('SELECT * FROM Espece');
$req->execute();
$especes = $req->fetchAll();

// Enregistrer le comptage
if (isset($_POST['envoyer'])) {

    $req = $db->prepare(
        'INSERT INTO Comptage_espece
    (id_collecte, id_espece, nombre_individus)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE
    nombre_individus = VALUES(nombre_individus)'
    );

    $req->execute([
        $_POST['id_collecte'],
        $_POST['id_espece'],
        $_POST['nombre_individus']
    ]);

    echo "Comptage enregistré !";
}
?>

<h1>Comptage des espèces</h1>

<form method="POST">

    Collecte :
    <select name="id_collecte" required>
        <?php foreach ($collectes as $collecte) { ?>
            <option value="<?= $collecte['id_collecte'] ?>">
                Collecte n° <?= $collecte['id_collecte'] ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    Espèce :
    <select name="id_espece" required>
        <?php foreach ($especes as $espece) { ?>
            <option value="<?= $espece['id_espece'] ?>">
                <?= htmlspecialchars($espece['nom_espece']) ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    Nombre d'individus :
    <input type="number" name="nombre_individus" min="0" required>

    <br><br>

    <button type="submit" name="envoyer">Enregistrer</button>

</form>

<a href="dashboard.php">Retour</a>