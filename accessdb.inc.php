<?php

function connexion() {
$dsn = 'mysql:host=localhost;dbname=appfaq';
$username = 'root';
$password = '';
try {
    $dbh = new PDO($dsn, $username, $password,
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
    die("Erreur lors de la connexion SQL : " . $ex->getMessage());
    }
return $dbh;
}

?>