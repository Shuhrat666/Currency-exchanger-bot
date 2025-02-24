<?php
require 'vendor/autoload.php';
require 'use_database.php';
require 'credentials/token.php';
require 'includes/password.php';

use GuzzleHttp\Client;

class Bot {
    private $client;
    private $db;

    public function __construct($token, $db_name, $db_username, $db_password) {
        $this->client = new Client(['base_uri' => "https://api.telegram.org/bot$token/"]);
        $this->db = new Database($db_name, $db_username, $db_password);
    }

    public function update($update) {
        if (isset($update['message'])) {
            $chatId = $update['message']['chat']['id'];
            $text = $update['message']['text'];
            $firstName = $update['message']['chat']['first_name'];

            if (!$this->db->userExists($chatId)) {
                $this->db->addUser($chatId);
            }

            switch ($text) {
                case '/start':
                    $this->sendWelcomeMessage($chatId, $firstName);
                    break;
                case '/usd2uzs':
                case '/eur2uzs':
                case '/rub2uzs':
                    $this->setCurrency($chatId, strtoupper(substr($text, 1, 3)));
                    break;
                case '🇺🇸 USD > 🇺🇿 UZS':
                case '🇪🇺 EUR > 🇺🇿 UZS':
                case '🇷🇺 RUB > 🇺🇿 UZS':
                    $this->setCurrency($chatId, explode(' ', $text)[1]);
                    break;
                case '⬅️ ortga':
                    $this->sendMainMenu($chatId);
                    break;
                case 'BOT USERS':
                    $this->sendUserCount($chatId);
                    break;
                default:
                    if (is_numeric($text)) {
                        $this->convertCurrency($chatId, $text);
                    } else {
                        $this->sendErrorMessage($chatId);
                    }
            }
        }
    }

    public function sendWelcomeMessage($chatId, $firstName) {
        $welcomeMessage = "Salom, $firstName! Bu yerda siz quyidagi komandalarni ishlatishingiz mumkin:\n";
        $welcomeMessage .= "/usd2uzs - USD dan UZS ga o'giradi\n";
        $welcomeMessage .= "/eur2uzs - EUR dan UZS ga o'giradi\n";
        $welcomeMessage .= "/rub2uzs - RUB dan UZS ga o'giradi\n";
        $welcomeMessage .= "Boshqa valyutalar ham tez orada qo'shiladi!\n";

        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> $welcomeMessage,
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '🇺🇸 USD > 🇺🇿 UZS']],
                    [['text' => '🇪🇺 EUR > 🇺🇿 UZS']],
                    [['text' => '🇷🇺 RUB > 🇺🇿 UZS']],
                    [['text'=> 'BOT USERS']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }

    public function setCurrency($chatId, $currency) {
        $this->db->setCurrency($currency);
        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> 'Qiymatni kiriting :',
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }

    public function sendMainMenu($chatId) {
        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> 'Asosiy menyuga qaytish ...',
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '🇺🇸 USD > 🇺🇿 UZS']],
                    [['text' => '🇪🇺 EUR > 🇺🇿 UZS']],
                    [['text' => '🇷🇺 RUB > 🇺🇿 UZS']],
                    [['text'=> 'BOT USERS']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }

    public function sendUserCount($chatId) {
        $userCount = $this->db->getUsers();
        $output = $userCount . (($userCount <= 1) ? ' user' : ' users');
        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> $output,
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }

    public function convertCurrency($chatId, $text) {
        $currency = $this->db->getCurrency();
        $apiUrl = "https://api.exchangerate-api.com/v4/latest/$currency";
        $apiResponse = file_get_contents($apiUrl);
        $apiData = json_decode($apiResponse, true);

        $convertedRate = $apiData['rates']['UZS'];
        $quantity = $text * $convertedRate;
        $output = "$text $currency = $quantity UZS";

        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> $output,
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }

    public function sendErrorMessage($chatId) {
        $this->client->post('sendMessage', [ 'form_params'=>[
            'chat_id'=> $chatId,
            'text'=> "Noto'g'ri format!",
            'reply_markup'=>json_encode([
                'keyboard' => [
                    [['text' => '⬅️ ortga']]
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ])
        ]]);
    }
}
?>
