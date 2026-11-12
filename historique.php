<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include_once "config.php";

$db = new PDO(
    'mysql:host='.Config::HOST.';dbname='.Config::DBNAME,
    Config::USER,
    Config::PASSWORD
);

// Récupérer l'historique des collectes
$req = $db->prepare(
    'SELECT
        c.id_collecte,
        c.date_collecte,
        c.latitude,
        c.longitude,
        p.nom_plage,
        e.nom_etude,
        es.nom_espece,
        ce.nombre_individus
    FROM Collecte_des_donnes c
    LEFT JOIN Plage p ON c.id_plage = p.id_plage
    LEFT JOIN Etudes e ON c.id_etude = e.id_etude
    LEFT JOIN Comptage_espece ce ON c.id_collecte = ce.id_collecte
    LEFT JOIN Espece es ON ce.id_espece = es.id_espece
    ORDER BY c.date_collecte DESC'
);

$req->execute();
$collectes = $req->fetchAll();
?>

<h1>Historique des collectes</h1>

<table border="1" cellpadding="10">

    <tr>
        <th>Collecte</th>
        <th>Date</th>
        <th>Plage</th>
        <th>Étude</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Espèce</th>
        <th>Nombre d'individus</th>
    </tr>

    <?php foreach ($collectes as $collecte) { ?>

        <tr>
            <td><?= $collecte['id_collecte'] ?></td>
            <td><?= $collecte['date_collecte'] ?></td>
            <td><?= htmlspecialchars($collecte['nom_plage'] ?? '') ?></td>
            <td><?= htmlspecialchars($collecte['nom_etude'] ?? '') ?></td>
            <td><?= $collecte['latitude'] ?></td>
            <td><?= $collecte['longitude'] ?></td>
            <td><?= htmlspecialchars($collecte['nom_espece'] ?? 'Aucun comptage') ?></td>
            <td><?= $collecte['nombre_individus'] ?? '-' ?></td>
        </tr>

    <?php } ?>

</table>

<br>

<a href="dashboard.php">Retour au tableau de bord</a>