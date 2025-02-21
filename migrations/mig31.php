<?php include 'includes/db.php'; 
require '../includes/password.php'?>

<?php
$pdo = new PDO('mysql:host=localhost;dbname=' . $db_name, $db_username, $db_password);
$stmt=$pdo->prepare(query:"CREATE table users(id INT PRIMARY KEY  auto_increment, user_id BIGINT UNIQUE);");
$stmt->execute();
printf("Created successsfully (Table 'userrs')!\n");

?>