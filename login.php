<?php

session_start();

include_once "config.php";

// 1 : Connexion à la BDD
$db = new PDO(
        'mysql:host='.Config::HOST.';dbname='.Config::DBNAME,
        Config::USER,
        Config::PASSWORD
);

// 2 : Vérifier si le formulaire a été envoyé
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 3 : Récupérer les informations du formulaire
    $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
    $mot_de_passe = $_POST['mot_de_passe'];

    // 4 : Préparer la requête SQL
    $req = $db->prepare('SELECT * FROM utilisateurs WHERE login = ?');

    // 5 : Exécuter la requête
    $req->execute([$login]);

    // 6 : Récupérer l'utilisateur
    $utilisateur = $req->fetch();

    // 7 : Vérifier le mot de passe
    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {

        session_regenerate_id(true);

        $_SESSION['user'] = $utilisateur;

        header('Location: dashboard.php');
        exit();

    } else {
        $erreur = "Identifiant ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion IFROCEAN</title>
</head>
<body>

<h1>Connexion IFROCEAN</h1>

<?php if (isset($erreur)) { ?>
    <p><?= htmlspecialchars($erreur) ?></p>
<?php } ?>

<form method="POST" action="">

    <label>Identifiant :</label>
    <input type="text" name="login" required>

    <br><br>

    <label>Mot de passe :</label>
    <input type="password" name="mot_de_passe" required>

    <br><br>

    <button type="submit">Se connecter</button>

</form>

</body>
</html>