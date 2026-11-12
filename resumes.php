<?php
session_start();

// Vérifier si l'utilisateur est connecté et administrateur
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

// Nombre total de collectes
$req = $db->prepare('SELECT COUNT(*) AS total FROM Collecte_des_donnes');
$req->execute();
$total_collectes = $req->fetch();

// Nombre total d'individus comptés
$req = $db->prepare('SELECT SUM(nombre_individus) AS total FROM Comptage_espece');
$req->execute();
$total_individus = $req->fetch();

// Résultats par espèce
$req = $db->prepare(
    'SELECT Espece.nom_espece, SUM(Comptage_espece.nombre_individus) AS total
    FROM Comptage_espece
    INNER JOIN Espece ON Comptage_espece.id_espece = Espece.id_espece
    GROUP BY Espece.id_espece, Espece.nom_espece'
);
$req->execute();
$especes = $req->fetchAll();

// Résultats par étude
$req = $db->prepare(
    'SELECT Etudes.nom_etude, COUNT(DISTINCT Collecte_des_donnes.id_collecte) AS total
    FROM Etudes
    LEFT JOIN Collecte_des_donnes
    ON Etudes.id_etude = Collecte_des_donnes.id_etude
    GROUP BY Etudes.id_etude, Etudes.nom_etude'
);
$req->execute();
$etudes = $req->fetchAll();
?>

<h1>Consultation des résumés</h1>

<h2>Statistiques générales</h2>

<p>Total des collectes : <?php echo $total_collectes['total']; ?></p>

<p>Total des individus comptés : <?php echo $total_individus['total'] ?? 0; ?></p>

<h2>Résultats par espèce</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Espèce</th>
        <th>Nombre d'individus</th>
    </tr>

    <?php foreach ($especes as $espece) { ?>
        <tr>
            <td><?php echo htmlspecialchars($espece['nom_espece']); ?></td>
            <td><?php echo $espece['total']; ?></td>
        </tr>
    <?php } ?>
</table>

<h2>Résultats par étude</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>Étude</th>
        <th>Nombre de collectes</th>
    </tr>

    <?php foreach ($etudes as $etude) { ?>
        <tr>
            <td><?php echo htmlspecialchars($etude['nom_etude']); ?></td>
            <td><?php echo $etude['total']; ?></td>
        </tr>
    <?php } ?>
</table>

<br>

<a href="dashboard.php">Retour au tableau de bord</a>