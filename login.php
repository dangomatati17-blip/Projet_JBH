<?php
session_start();

// Importer la configuration
include_once "config.php";

// Vérifier si le formulaire a été envoyé
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_SPECIAL_CHARS);
    $mot_de_passe = $_POST["mot_de_passe"];

    try {
        // 1 : Connexion à la BDD
        $db = new PDO(
                'mysql:host='.Config::HOST.';dbname='.Config::DBNAME.';charset=utf8mb4',
                Config::USER,
                Config::PASSWORD
        );

        // 2 : Préparer une requête SQL
        $req = $db->prepare('SELECT * FROM utilisateurs WHERE login = ?');

        // 3 : Exécuter la requête
        $req->execute([$login]);

        // 4 : Récupérer le résultat
        $utilisateur = $req->fetch();

        // Vérifier le mot de passe
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {

            session_regenerate_id(true);

            $_SESSION['user'] = [
                    'id' => $utilisateur['id'],
                    'nom' => $utilisateur['nom'],
                    'prenom' => $utilisateur['prenom'],
                    'role' => $utilisateur['role']
            ];

            header("Location: dashboard.php");
            exit();

        } else {
            $erreur = "Identifiant ou mot de passe incorrect.";
        }

    } catch (PDOException $e) {
        $erreur = $e->getMessage();
    }
}
?>

<h1>Connexion IFROCEAN</h1>

<?php if (isset($erreur)) { ?>
    <p><?php echo htmlspecialchars($erreur); ?></p>
<?php } ?>

<form method="POST" action="login.php">

    <label>Identifiant :</label>
    <input type="text" name="login" required>

    <label>Mot de passe :</label>
    <input type="password" name="mot_de_passe" required>

    <button type="submit">Se connecter</button>

</form>
