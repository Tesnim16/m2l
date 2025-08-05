<?php
session_start();
include 'accesdb.inc.php';
$dbh = connexion();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$userType = $_SESSION['usertype'];
$idLigue = $_SESSION['id_ligue']; 

// Récupération de la ligue de l'utilisateur
$query = "SELECT ligue.lib_ligue FROM user 
          JOIN ligue ON user.id_ligue = ligue.id_ligue
          WHERE user.pseudo = ?";
$stmt = $dbh->prepare($query);
$stmt->execute([$username]);
$row = $stmt->fetch();
$libLigue = $row ? $row['lib_ligue'] : "";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/list.css">
    <title>Liste des Questions</title>
</head>
<body>
<header>
    <div class="logo">
        <img src="img/LOGO.jpg" alt="logo">
    </div>
    <h1 class="titre">AppFaq</h1>
    <a href="add.php" class="btn">Ajouter une question</a>
    <nav class="navbar">
        <ul class="menu">
            <li><a href="index.php">Accueil</a></li>
            <li>
                <a href="#">Services ▼</a>
                <ul class="submenu">
                    <li><a href="list.php">Aller à la FAQ</a></li>
                </ul>
            </li>
            <?php
            echo '<li><a href="#">Bienvenue, ' . htmlspecialchars($username) . ' (Ligue de ' . htmlspecialchars($libLigue) . ')</a></li>';
            echo '<li><a href="index.php?logout=true"><u>Se déconnecter</u></a></li>';
            ?>
        </ul>
    </nav>
</header>

<div class="container">
    <div class="button-list">
        <h2>Liste des questions FAQ</h2>
        <table>
            <tr>
                <th>Numéro Question</th>
                <th>Question</th>
                <th>Réponse</th>
                <th>Date Question</th>
                <th>État</th>
                <?php
                if ($userType === 'admin' || $userType === 'superadmin') {
                    echo '<th>Actions</th>';
                }
                ?>
            </tr>

            <?php
            if ($userType === 'user') {
                $stmt = $dbh->prepare("SELECT faq.id_faq, faq.question, faq.reponse, faq.dat_question, faq.approuvé /*ici*/
                                       FROM faq 
                                       JOIN user ON faq.id_user = user.id_user 
                                       WHERE user.id_ligue = ? AND faq.approuvé = 1");  /*ici*/
                $stmt->execute([$idLigue]);
            } else {
                $stmt = $dbh->query("SELECT id_faq, question, reponse, dat_question, approuvé FROM faq");   /*ici*/
            }

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['id_faq']) . "</td>
                        <td>" . htmlspecialchars($row['question']) . "</td>
                        <td>" . htmlspecialchars($row['reponse']) . "</td>
                        <td>" . htmlspecialchars(date("d-m-Y H:i:s", strtotime($row['dat_question']))) . "</td>
                        <td>" . ($row['approuvé'] == 1 ? 'approuvé' : 'en attente') . "</td>";  /*ici*/

                if ($userType === 'admin' || $userType === 'superadmin') {
                    echo "<td>";
                    if ($row['approuvé'] == 0) {
                        echo "<button onclick=\"window.location.href='approve.php?id_faq=" . htmlspecialchars($row['id_faq']) . "'\">Confirmer</button>";     /*ici*/
                    }
                    echo "<button onclick=\"window.location.href='edit.php?id_faq=" . htmlspecialchars($row['id_faq']) . "'\">Modifier</button>
                          <button onclick=\"window.location.href='delete.php?id_faq=" . htmlspecialchars($row['id_faq']) . "'\">Supprimer</button>
                          </td>";
                }

                echo "</tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
