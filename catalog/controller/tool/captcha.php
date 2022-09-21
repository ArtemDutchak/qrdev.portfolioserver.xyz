<?php

namespace Opencart\Catalog\Controller\Tool;

class Captcha
{
    public function checkCaptcha()
    {
        $json = [
            'errors' => array(),
        ];

        if (!$_POST["captcha"]) {
            // Если данных нет, то программа останавливается и выводит ошибку
            $json['errors'][] = 'Captcha is empty';
        } else { // Иначе создаём запрос для проверки капчи
            // URL куда отправлять запрос для проверки
            $url = "https://www.google.com/recaptcha/api/siteverify";
            // Ключ для сервера
            $key = "6LcI6BIiAAAAAKy-Co9Ar1hIPh5bfgNtmR_9iOQp";
            // Данные для запроса
            $query = array(
                "secret" => $key, // Ключ для сервера
                "response" => $_POST["g-recaptcha-response"], // Данные от капчи
                "remoteip" => $_SERVER['REMOTE_ADDR'] // Адрес сервера
            );

            // Создаём запрос для отправки
            $ch = curl_init();
            // Настраиваем запрос
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            // отправляет и возвращает данные
            $data = json_decode(curl_exec($ch), $assoc=true);
            // Закрытие соединения
            curl_close($ch);

            // Если нет success то
            if (!$data['success']) {
                // Останавливает программу и выводит "ВЫ РОБОТ"
                $json['errors'][] = 'Captcha is invalid';
            }
        }

        return $json;
    }
}