<!-- Verwenden von BootstrapCDN -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

<style>
[class*="col-"] {
  padding-top: 1rem;
  padding-bottom: 1rem;
  background-color: rgba(86, 61, 124, .15);
  border: 1px solid rgba(86, 61, 124, .2);
}
</style>

<?php

// Verbindung zur Datenbank herstellen
require 'config.php';

$pdo = new PDO("mysql:host=$sql_db_host;dbname=$sql_db_name", $sql_db_user, $sql_db_pass);

// Benutzer laden
$sql = "SELECT user_id, username FROM Wo_Users";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$userArray = $stmt->fetchAll();

echo '<div class="container">';
echo '<h1>Profile als RSS-Feed</h1>';
echo '<div class="row">';

// Schleife
foreach ($userArray as $user) {
    // Erstelle Link zur XML-Datei für jeden Benutzer
    $xml_link = 'profil_feed.php?user=' . $user['username'];
    echo '<div class="col-2"><a href="' . $xml_link . '">' . $user['username'] . '</a></div>';
}

echo '</div>';
echo '</div>';

?>
