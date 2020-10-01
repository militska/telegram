<?php

$content = file_get_contents("php://input");
$update = json_decode($content, true);

require 'Telegram.php';
use militska\telegram\Telegram;

if (!$update) {
    // receive wrong update, must not happen
    exit;
}

if (isset($update["message"])) {
    processMessage($update["message"]);
}

/***
 * @param $message
 */
function processMessage($message)
{
    $telegram = new Telegram();
    $chat_id = $message['chat']['id'];
    if (isset($message['text'])) {
        // incoming text message
        $text = $message['text'];
        switch ($text) {
            case "/start" :
                $telegram->apiRequest(Telegram::API_URL_SEND_MESSAGE, [
                    'chat_id' => $chat_id,
                    "text" => $message['chat']['first_name']  . ', ' .$telegram->getHello(),
                    'reply_markup' => [
                        'one_time_keyboard' => false,
                        'keyboard' => [['Время', 'Хочу общаться']],
                        'resize_keyboard' => true
                    ]
                ]);
                break;
            case  "Время" :
                $telegram->sendMessage($chat_id, 'Сейчас ' . date('d.m.Y H:i', time()));
                break;
            case  "Хочу общаться" :
                $telegram->apiRequest(Telegram::API_URL_SEND_MESSAGE, array(
                    'chat_id' => $chat_id,
                    "text" => 'О чем мы можем пообщаться?',
                    'reply_markup' => [
                        'one_time_keyboard' => false,
                        'keyboard' => array(array('Нужен комплимент', 'Все плохо')),
                        'resize_keyboard' => true
                    ]
                ));
                break;
            case 'Нужен комплимент' :
                $telegram->sendMessage($chat_id, "Вероятно, ты довольно неплохо выглядешь");
                break;
            case 'Средне плохо' :
                $telegram->sendMessage($chat_id, 'Средне плохо? Точно средне?');
                break;
            case 'Очень плохо' :
                $telegram->sendMessage($chat_id, 'Ты там держись, всего хорошего тебе!');
                break;
            case 'Все плохо' :
                $telegram->apiRequest(Telegram::API_URL_SEND_MESSAGE, array(
                    'chat_id' => $chat_id,
                    "text" => "Привет",
                    'reply_markup' => [
                        'one_time_keyboard' => true,
                        'keyboard' => array(array('Средне плохо', 'Очень плохо')),
                        'resize_keyboard' => true
                    ]
                ));
                break;
            default :
                if (substr($text, 0, 5) == "/call") {
                    $rest = substr($text, 6);    // возвращает "f"
                    $telegram->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => $rest));
                    break;
                }

                $telegram->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => "Не понимаю о чем ты"));
                break;

        }
    } else {
        $telegram->apiRequest("sendMessage", array('chat_id' => $chat_id, "text" => 'I understand only text messages'));
    }
}

