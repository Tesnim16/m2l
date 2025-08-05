<?php
include 'accesdb.inc.php'; // Connexion à la base de données
$dbh = connexion();


if (isset($_GET['id_faq'])) {
    $id_faq = intval($_GET['id_faq']); 


    $stmt = $dbh->prepare("DELETE FROM faq WHERE id_faq = :id_faq");
    $stmt->bindParam(':id_faq', $id_faq);

    if ($stmt->execute()) {

        header("Location: list.php");
        exit();
    } else {
        echo "Erreur : Impossible de supprimer l'enregistrement.";
    }
} else {
    echo "Erreur : ID non fourni.";
}
?>
