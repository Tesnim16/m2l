<?php
include 'accesdb.inc.php';
$dbh = connexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = htmlspecialchars($_POST['question']);


    $id_user = 1; 


    $stmt = $dbh->prepare("INSERT INTO faq (question, id_user, dat_question, approuvé) /*modif ici*/
                           VALUES (:question, :id_user, NOW(), 0)");
    $stmt->bindParam(':question', $question);
    $stmt->bindParam(':id_user', $id_user);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit();
    } else {
        $error_message = "Erreur : Impossible d'ajouter la question.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/add.css"> 
    <title>Ajouter une Question</title>
</head>
<body>
    <div id="main-container">
        <form action="add.php" method="POST">
            <label for="question">Ajouter une Question :</label>
            <textarea id="question" name="question" required></textarea>
            <input type="submit" value="Ajouter">
        </form>

        <?php
        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>
