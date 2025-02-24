<?php include 'includes/db.php'; 
require '../includes/password.php'?>

<?php
$pdo = new PDO('mysql:host=localhost;dbname=' . $db_name, $db_username, $db_password);
$stmt=$pdo->prepare(query:"CREATE table users(id INT PRIMARY KEY  auto_increment, user_id BIGINT UNIQUE);");
$stmt->execute();
printf("Created successsfully (Table 'userrs')!\n");

$pdo = new PDO('mysql:host=localhost;dbname=' . $db_name, $db_username, $db_password);
$stmt=$pdo->prepare(query:"CREATE table currency(id INT PRIMARY KEY  auto_increment, currency varchar(8));");
$stmt->execute();
printf("Created successsfully (Table 'currency')!\n");

$pdo = new PDO('mysql:host=localhost;dbname=' . $db_name, $db_username, $db_password);
$stmt=$pdo->prepare(query:"INSERT INTO currency(currency) values('USD');");
$stmt->execute();
printf("Inserted successsfully (Like default 'USD')!\n");

?>