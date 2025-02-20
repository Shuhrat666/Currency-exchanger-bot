<?php
require 'token.php';

$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $date=date('Y-m-d');
    $component=explode(  ' ', $text);
    $value=(int)$component[0];
    $conver_from= $component[1];
    $convert_to=$component[3];
    $converted_rate;
    $quantity=$value*$converted_rate;
    $output=$value.' '.$convert_from.' '.$quantity.' '.$convert_to;

    $url = "https://api.telegram.org/bot$token/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $output
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
}