<?php
session_start();
include 'accesdb.inc.php';
$dbh = connexion();

// Validate user session
if (!isset($_SESSION['username']) || !isset($_SESSION['usertype'])) {
    header("Location: login.php");
    exit();
}

// Check user access: Only admin or superadmin allowed
if ($_SESSION['usertype'] !== 'admin' && $_SESSION['usertype'] !== 'superadmin') {
    die("Access denied. Only admins and superadmins are allowed.");
}

// Handle form submission to update answers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['answers'] as $idFaq => $answer) {
        $answer = trim($answer);
        if (!empty($answer)) {
            $updateQuery = "UPDATE faq SET reponse = ? WHERE id_faq = ?";
            $stmt = $dbh->prepare($updateQuery);
            $stmt->execute([$answer, $idFaq]);
        }
    }

    // Redirect to list.php after saving modifications
    header("Location: list.php");
    exit();
}

// Fetch all FAQ entries from the database
$query = "SELECT id_faq, question, reponse FROM faq";
$stmt = $dbh->query($query);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get league name for the header
$libLigue = "";
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $query = "SELECT ligue.lib_ligue 
              FROM user 
              JOIN ligue ON user.id_ligue = ligue.id_ligue
              WHERE user.pseudo = ?";
    $stmt = $dbh->prepare($query);
    $stmt->execute([$username]);
    $row = $stmt->fetch();

    if ($row) {
        $libLigue = $row['lib_ligue'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/edit.css">
    <title>Modifier Questions FAQ</title>
</head>
<body>
<header>
    <div class="logo">
        <img src="img/LOGO.jpg" alt="logo">
    </div>
    <h1 class="titre">AppFaq</h1>
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
<div class="container">
    <form method="POST">
        <table>
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Réponse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($faqs as $faq): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($faq['question']); ?></td>
                        <td>
                            <textarea name="answers[<?php echo $faq['id_faq']; ?>]" rows="3" style="width: 100%;"><?php echo htmlspecialchars($faq['reponse']); ?></textarea>
                        </td>
                        <td>
                            <button class="delete-button" onclick="window.location.href='delete.php?id_faq=<?php echo htmlspecialchars($faq['id_faq']); ?>'">Supprimer</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <input type="submit" value="Enregistrer les modifications">
    </form>
</div>
</body>
</html>
