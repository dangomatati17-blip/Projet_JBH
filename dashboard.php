<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$utilisateur = $_SESSION['user'];
?>

<h1>Bienvenue sur IFROCEAN</h1>

<p>
    Bonjour <?php echo htmlspecialchars($utilisateur['prenom']); ?>
    <?php echo htmlspecialchars($utilisateur['nom']); ?>
</p>

<?php if ($utilisateur['role'] == 'admin') { ?>
    <h2>Espace administrateur</h2>
    <p><a href="gestion_etudes.php">Gestion des études</a></p>
    <p><a href="gestion_plages.php">Gestion des plages</a></p>
    <p><a href="historique.php">Consultation des résumés</a></p>

<?php } elseif ($utilisateur['role'] == 'preleveur') {?>

    <h2>Espace préleveur</h2>
    <p><a href="releves_gps.php">Saisie des relevés GPS</a></p>
    <p>Saisie des comptages </p>

<?php } ?>

<a href="logout.php">Se déconnecter</a>