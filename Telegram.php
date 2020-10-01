<?php

namespace militska\telegram;

use GuzzleHttp\Client;

require 'vendor/autoload.php';
require 'config.php';
require 'Common.php';

class Telegram
{
    use \Common;


    /**
     * @return string
     */
    public function getHello()
    {
        return $this->how[array_rand($this->how, 1)];

    }

    /**
     * todo Перенести в бд
     * @return string
     */
    public function getComplement()
    {
        return $this->complements[array_rand($this->complements, 1)];

    }

    const API_URL_SEND_MESSAGE = 'https://api.telegram.org/bot%s/sendMessage';

    /**
     * @param integer $chatId
     * @param string $message
     */
    public function sendMessage($chatId, $message)
    {
        $telegram = new self;
        $telegram->apiRequest(self::API_URL_SEND_MESSAGE,
            array('chat_id' => $chatId, "text" => $message));
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return bool
     */
    public function apiRequestWebHook($method, $parameters)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else {
            if (!is_array($parameters)) {
                error_log("Parameters must be an array\n");
                return false;
            }
        }

        $parameters["method"] = $method;

        header("Content-Type: application/json");
        echo json_encode($parameters);
        return true;
    }


    /**
     * @param string $method
     * @param array $parameters
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     */
    public function apiRequest($method, $parameters = null)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        foreach ($parameters as $key => &$val) {
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }
        $url = sprintf($method, Configure::TOKEN);
        $client = new Client();

        return $client->request('GET', $url, ['query' => $parameters, 'timeout' => 60, 'connect_timeout' => 5]);
    }

}
