<?php
include 'accesdb.inc.php';
$dbh = connexion();


$idFaq = isset($_GET['id_faq']) ? intval($_GET['id_faq']) : 0;  /*trouve la question*/


$stmt = $dbh->prepare("UPDATE faq SET approuvé = 1 WHERE id_faq = ?"); /*remlpace le 0 par 1 pour l'approuvé*/
$stmt->execute([$idFaq]);


header("Location: list.php");
exit();
?>
