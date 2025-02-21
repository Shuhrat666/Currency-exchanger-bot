<?php
require 'vendor/autoload.php';
require 'includes/db.php';
require 'credentials/token.php';
require 'currency_check.php';

use GuzzleHttp\Client;

$client = new Client(['base_uri'=> "https://api.telegram.org/bot$token/sendMessage", ]);

$input = file_get_contents('php://input');
$update = json_decode($input, true);

if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $text = $update['message']['text'];
    $firstName = $update['message']['chat']['first_name'];

    $stmt = $pdo->prepare("SELECT user_id FROM users;");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array($chatId, $users)) {
        $stmt = $pdo->prepare("INSERT INTO users (user_id) VALUES (:user_id);");
        $stmt->execute(['user_id' => $chatId]);
    }

    $convert_from = ([$text=='🇺🇸 USD > 🇺🇿 UZS' || $text=='🇪🇺 EUR > 🇺🇿 UZS' || $text=='🇷🇺 RUB > 🇺🇿 UZS']) ? currency_check($text) : $convert_from;

    if ($text == '/start') {
        $welcomeMessage = "Salom, $firstName! Bu yerda siz quyidagi komandalarni ishlatishingiz mumkin:\n";
        $welcomeMessage .= "/usd2uzs - USD dan UZS ga o'giradi\n";
        $welcomeMessage .= "/eur2uzs - EUR dan UZS ga o'giradi\n";
        $welcomeMessage .= "/rub2uzs - RUB dan UZS ga o'giradi\n";

        $keyboard = [
            'keyboard' => [
                [['text' => '🇺🇸 USD > 🇺🇿 UZS']],
                [['text' => '🇪🇺 EUR > 🇺🇿 UZS']],
                [['text' => '🇷🇺 RUB > 🇺🇿 UZS']],
                [['text'=> 'BOT USERS']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        $data = [
            'chat_id' => $chatId,
            'text' => $welcomeMessage,
            'reply_markup' => json_encode($keyboard)
        ];

        $url = "https://api.telegram.org/bot$token/sendMessage";
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
    else if (in_array($text, ['🇺🇸 USD > 🇺🇿 UZS', '🇪🇺 EUR > 🇺🇿 UZS', '🇷🇺 RUB > 🇺🇿 UZS'])) {
        $data = [
            'chat_id' => $chatId,
            'text' => 'Qiymat kiriting:',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ];

        $url = "https://api.telegram.org/bot$token/sendMessage";
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
    else if (is_numeric($text)) {
            $api_url = "https://api.exchangerate-api.com/v4/latest/$convert_from";

            $api_response = file_get_contents($api_url);
            $api_data = json_decode($api_response, true);

            $convert_to = 'UZS';
            $converted_rate = $api_data['rates'][$convert_to];

            $quantity = $text * $converted_rate;
            $output = "$text $convert_from = $quantity $convert_to";

            $data = [
                'chat_id' => $chatId,
                'text' => $output,
                'reply_markup' => json_encode([
                    'keyboard' => [
                        [['text' => '⬅️ ortga']]
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ])
            ];

            $url = "https://api.telegram.org/bot$token/sendMessage";
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
    else if ($text == '⬅️ ortga') {
        $data = [
            'chat_id' => $chatId,
            'text' => 'Asosiy menyuga qaytish...',
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => '🇺🇸 USD > 🇺🇿 UZS']],
                    [['text' => '🇪🇺 EUR > 🇺🇿 UZS']],
                    [['text' => '🇷🇺 RUB > 🇺🇿 UZS']],
                    [['text'=> 'BOT USERS']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ];
        $url = "https://api.telegram.org/bot$token/sendMessage";
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
    else if ($text == 'BOT USERS') {

        $stmt = $pdo->prepare("SELECT COUNT(user_id) AS user_count FROM users;");
        $stmt->execute();
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($users["user_count"]);
        if ($users) {
            if((int)$users['user_count']<=1){
                $output = $users['user_count'] . ' user';
            }
            else{
                $output = $users['user_count'] . ' users';
            }
        }

        $data = [
            'chat_id' => $chatId,
            'text' => $output,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ];

        $url = "https://api.telegram.org/bot$token/sendMessage";
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
    else{
        $output=" Noto'g'ri format ! ";
        $data = [
            'chat_id' => $chatId,
            'text' => $output,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ];

        $url = "https://api.telegram.org/bot$token/sendMessage";
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
}


?>
