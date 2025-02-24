<?php

require 'bot.php';
require 'credentials/token.php';
require 'includes/password.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);

$bot = new Bot($token, $db_name, $db_username, $db_password);
$bot->update($update);

?>

