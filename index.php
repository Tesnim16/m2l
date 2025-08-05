<?php
session_start();

include 'accesdb.inc.php';
$dbh = connexion();

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_destroy();
    header("Location: login.php");
    exit();
}

$libLigue = "";

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Récupération de la ligue de l'utilisateur
    $query = "SELECT ligue.lib_ligue 
              FROM user 
              JOIN ligue ON user.id_ligue = ligue.id_ligue
              WHERE user.pseudo = '$username'";
    $result = $dbh->query($query);
    $row = $result->fetch();

    if ($row) {
        $libLigue = $row['lib_ligue']; // Nom de la ligue
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Accueil FAQ</title>
</head>
<body>
<header>
    <div class="logo">
        <img src="img/LOGO.jpg" alt="logo">
    </div>
    <h1 class="titre">AppFaq</h1>
    <nav class="navbar">
        <ul class="menu">
            <li><a href="#">Accueil</a></li>
            <li>
                <a href="#">Services ▼</a>
                <ul class="submenu">
                    <li><a href="list.php">Aller à la FAQ</a></li>
                </ul>
            </li>

            <?php
            if (isset($_SESSION['username'])) {
                echo '<li><a href="#">Bienvenue, ' . htmlspecialchars($_SESSION['username']) . ' (Ligue de ' . htmlspecialchars($libLigue) . ')</a></li>';
                echo '<li><a href="index.php?logout=true"><u>Se déconnecter</u></a></li>';
            } else {
                echo '<li><a href="register.php">Inscription</a></li>';
                echo '<li><a href="login.php">Connexion</a></li>';
            }
            ?>
        </ul>
    </nav> 
</header>
<hr>
<div class="image-container">
    <img src="img/photo bat.png" alt="Image de fond">
    <div class="text-overlay">
        Bienvenue à la Maison des Sports
    </div>
</div>
<hr>

<div class="where-container">
    <h2>Où nous trouver</h2>
        <div class="carte">
            <img src="img/carte.jpg" alt="Carte">
        </div>
</div>



<footer>Réalisé par <strong>Nouira Selim</strong></footer>
</body>
</html>
