<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["identifiant"];
    $pass = $_POST["motdepasse"];

    // Database connection
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "appfaq";

    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

   
    $stmt = $conn->prepare("SELECT user.id_user, user.pseudo, user.mdp, user.id_ligue, usertype.lib_usertype 
                            FROM user 
                            JOIN usertype ON user.id_usertype = usertype.id_usertype 
                            WHERE pseudo = ? AND mdp = ?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    $loginError = "";
    if ($row = $result->fetch_assoc()) {
        // Start the session with user data
        $_SESSION["id_user"] = $row["id_user"];
        $_SESSION["username"] = $row["pseudo"];
        $_SESSION["usertype"] = $row["lib_usertype"]; 
        $_SESSION["id_ligue"] = $row["id_ligue"];

        header("Location: list.php");
        exit();
    } else {
        $loginError = "Nom d'utilisateur ou mot de passe incorrect.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>Se connecter</title>
</head>
<body>
    <div id="login-container">
        <h2>Connexion</h2>
        <?php if (!empty($loginError)) echo "<p style='color:red;'>$loginError</p>"; ?>
        <form id="login-form" method="POST">
            <label for="identifiant">Identifiant :</label>
            <input type="text" id="identifiant" name="identifiant" required>

            <label for="motdepasse">Mot de passe :</label>
            <input type="password" id="motdepasse" name="motdepasse" required>

            <div id="login-buttons">
                <input type="submit" value="Se connecter">
                <input type="button" value="S'inscrire" onclick="window.location.href='register.php'">
            </div>
        </form>
    </div>
</body>
</html>
