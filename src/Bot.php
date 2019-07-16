<?php

namespace Jt\DingBot;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Events\Dispatcher;

class Bot
{
    const BASE_URL       = '';
    const CONFIG_BOT_KEY = 'dingbot.bots';

    protected $bot;

    protected $events;

    protected $history = [];

    protected $client;

    protected $headers = [];

    protected $timeout = 30;

    protected $atAll = false;

    protected $atUsers = [];

    public function __construct($token)
    {
        $this->bot = $this->getBotNameFromToken($token);

        $this->client = new Client([
            'base_uri' => $this->getBaseUrl($token),
            'timeout'  => $this->timeout,
        ]);
    }

    public function __call($method, $data)
    {
        return $this->handleResponse(
            $this->cient->post($method, [
                'json'    => $this->parseData($method, $data),
                'headers' => $this->getHeaders(),
            ])
        );
    }

    public function bot()
    {
        return $this->bot;
    }

    public function getheaders()
    {
        return $this->headers;
    }

    public function addHeaders(array $headers, $replace = false)
    {
        if ($replace) {
            $this->headers = $headers;
        } else {
            $this->headers = array_merge($this->headers, $headers);
        }
        return $this;
    }

    public function at($users)
    {
        if (!is_array($users)) {
            $users = func_get_args();
        }
        $this->atUsers = $users;
    }

    public function atAll()
    {
        $this->atAll = true;
    }

    public function getHistory()
    {
        return $this->history;
    }

    protected function parseData($method, $data)
    {
        $at        = [];
        $parseData = [
            "msgtype" => $method,
            $method   => $data,
        ];

        if ($this->atUsers) {
            $this->atAll = false;
        }

        if ($this->atUsers or $this->atAll) {

            $at = ['at' => [

            ]];
            $parseData += $at;
        }
    }

    protected function getBotNameFromToken(string $token)
    {
        return (collect(config(self::CONFIG_BOT_KEY)))
            ->map(function ($bot, $name) {
                return ['name' => $name, 'bot' => $bot];
            })
            ->first(function ($bot) use ($token) {
                return $bot['bot']['token'] === $token;
            });
    }

    public function select(string $bot)
    {
        $bots = config(self::CONFIG_BOT_KEY);
        if (!array_key_exists($bot, $bots)) {
            throw new Exception('aaaa');
        }
        $this->bot    = $bot;
        $token        = $bot['token'];
        $this->client = new Client([
            'baseUrl' => $this->getBaseUrl($token),
            'timeout' => $this->timeout,
        ]);
        return $this;
    }

    protected function getBaseUrl(string $token)
    {
        return self::BASE_URL . "/?access_token=" . $token;
    }

    protected function handleResponse(Response $response)
    {
        return \GuzzleHttp\json_decode($response->getBody(), true);
    }

    public function setEventDispatcher(Dispatcher $events): self
    {
        $this->events = $events;
        return $this;
    }

    public function dispatch($event)
    {
        return $this->events->dispatch($event);
    }
}
