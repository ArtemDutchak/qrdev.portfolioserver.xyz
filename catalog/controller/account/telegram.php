<?php

namespace Opencart\Catalog\Controller\Account;

class Telegram
{
    public function index()
    {
        # Принимаем запрос
        $data = json_decode(file_get_contents('php://input'), TRUE);
        //file_put_contents('file.txt', '$data: '.print_r($data, 1)."\n", FILE_APPEND);


//https://api.telegram.org/bot5427022142:AAFHk3MUh0O9YPuGmR5A6sd2Nl72mHgXw8g/setwebhook?url=https://qrdev.portfolioserver.xyz/index.php?route=account/telegram|index
//https://api.telegram.org/bot5566461120:AAFU65AoQYkkZBHsXJgdgan6neN5QbyRUtI/setwebhook?url=http://2329082.yc470698.web.hosting-test.net/index.py


# Обрабатываем ручной ввод или нажатие на кнопку
        $data = $data['callback_query'] ? $data['callback_query'] : $data['message'];

# Записываем сообщение пользователя
        $message = mb_strtolower(($data['text'] ? $data['text'] : $data['data']), 'utf-8');


# Обрабатываем сообщение
        switch ($message) {
            case '/info':
                $method = 'sendMessage';
                $send_data = [
                    'text' => "Ласкаво просимо до QR telegram bot.\nДля підключення повідомлень вкажіть telegram chat id у налаштуваннях компанії. \nChat id Ви можете дізнатися додавивши цього бота у групу.\nhttps://t.me/getmyid_bot"
                ];
                break;
        }

# Добавляем данные пользователя
        $send_data['chat_id'] = $data['chat']['id'];

        $res = $this->sendTelegram($method, $send_data);

    }


    function sendTelegram($method, $data, $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => 'https://api.telegram.org/bot' . TELEGRAM_TOKEN . '/' . $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array_merge(array("Content-Type: application/json"), $headers)
        ]);

        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, 1) ? json_decode($result, 1) : $result);
    }
}