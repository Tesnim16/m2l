<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pseudo = trim($_POST["identifiant"]);
    $mail = trim($_POST["mail"]);
    $password = password_hash(trim($_POST["motdepasse"]), PASSWORD_DEFAULT); // Hash password for security
    $id_usertype = intval($_POST["id_usertype"]); // Utilisateur doit sélectionner un type utilisateur
    $id_ligue = intval($_POST["id_ligue"]); // Ajouter la sélection de la ligue

    // Connexion à la base de données
    $host = "localhost";
    $username = "root";
    $dbpassword = "";
    $dbname = "appfaq";

    $conn = new mysqli($host, $username, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Échec de la connexion : " . $conn->connect_error);
    }

    // Vérifier si le pseudo ou l'adresse e-mail existe déjà
    $sql = "SELECT * FROM user WHERE pseudo = ? OR mail = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $pseudo, $mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red; text-align: center;'>Le pseudo ou l'adresse e-mail est déjà utilisé.</p>";
    } else {
        // Vérifie si le type utilisateur et la ligue existent
        $sql = "SELECT * FROM usertype WHERE id_usertype = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usertype);
        $stmt->execute();
        $usertype_result = $stmt->get_result();

        $sql = "SELECT * FROM ligue WHERE id_ligue = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_ligue);
        $stmt->execute();
        $ligue_result = $stmt->get_result();

        if ($usertype_result->num_rows > 0 && $ligue_result->num_rows > 0) {
            // Insérer le nouvel utilisateur
            $sql = "INSERT INTO user (pseudo, mail, mdp, id_usertype, id_ligue) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssii", $pseudo, $mail, $password, $id_usertype, $id_ligue);

            if ($stmt->execute()) {
                echo "<p style='color: green; text-align: center;'>Inscription réussie ! Redirection vers la page de connexion...</p>";
                header("refresh:2; url=login.php");
                exit();
            } else {
                echo "<p style='color: red; text-align: center;'>Erreur lors de l'inscription : " . $conn->error . "</p>";
            }
        } else {
            echo "<p style='color: red; text-align: center;'>Le type d'utilisateur ou la ligue sélectionnée est invalide.</p>";
        }
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
    <link rel="stylesheet" href="css/style.css">
    <title>Inscription</title>
</head>
<body>
    <div id="register-container">
        <h2>Inscription</h2>
        <form id="register-form" method="POST" action="">
            <label for="identifiant">Pseudo :</label>
            <input type="text" id="identifiant" name="identifiant" required>

            <label for="mail">Adresse e-mail :</label>
            <input type="email" id="mail" name="mail" required>

            <label for="motdepasse">Mot de passe :</label>
            <input type="password" id="motdepasse" name="motdepasse" required>

            <label for="id_usertype">Sélectionner un type utilisateur :</label>
            <select id="id_usertype" name="id_usertype" required>
                <option value="1">Utilisateur</option>
                <option value="2">Admin</option>
                <option value="3">Superadmin</option>
            </select>

            <label for="id_ligue">Sélectionner une ligue :</label>
            <select id="id_ligue" name="id_ligue" required>
                <option value="1">Football</option>
                <option value="2">Basketball</option>
                <option value="3">Volleyball</option>
                <option value="4">Handball</option>
                <option value="5">Toutes les ligues</option>
            </select>

            <div id="register-buttons">
                <input type="submit" value="S'inscrire">
                <input type="button" value="Se connecter" onclick="window.location.href='login.php'">
            </div>
        </form>
    </div>
</body>
</html>
