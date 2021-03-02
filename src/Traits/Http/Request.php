<?php

namespace Litegram\Traits\Http;

use Chipslays\Collection\Collection;

trait Request
{
    /**
     * @var \CurlHandle 
     */
    private $curl;

    /**
     * Универсальный вызов методов Telegram.
     *
     * @param string $method Название метода.
     * @param array $params Массив параметров, где ключ - это навание параметра, а значение - это значение параметра.
     * @param boolean $isFile True если передается файл, False для обычных запросов.
     *
     * @return \Chipslays\Collection\Collection
     */
    public function api(string $method, array $params = [], bool $isFile = false)
    {
        $this->curl = $this->curl ?: curl_init();

        if ($isFile) {
            $headers = 'Content-Type: multipart/form-data';
        } else {
            $headers = 'Content-Type: application/json';
            $params = json_encode($params);
        }

        curl_setopt($this->curl, CURLOPT_URL, $this->getRequestUrl($method));
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, [$headers]);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);

        $response = curl_exec($this->curl);

        return new Collection(json_decode($response, true));
    }

    /**
     * Собирает параметры в единый массив.
     *
     * @param array $params
     * @param array|string $keyboard
     * @param array $extra
     *
     * @return array
     */
    private function buildRequestParams($params = [], $keyboard = null, $extra = []): array
    {
        if ($keyboard) {
            $params['reply_markup'] = is_array($keyboard) ? $this->keyboard($keyboard) : $keyboard;
        }

        $params['parse_mode'] = $this->config('telegram.parse_mode', 'html');

        if (!empty($params['text'])) {
            $params['text'] = implode("\n", array_map('trim', explode("\n", $params['text'])));
        }

        if (!empty($params['caption'])) {
            $params['caption'] = implode("\n", array_map('trim', explode("\n", $params['caption'])));
        }

        return array_merge($params, (array) $extra);
    }

    /**
     * Собирает ссылку для запроса.
     *
     * @param string $method
     * @return string
     */
    private function getRequestUrl($method = null)
    {
        return "https://api.telegram.org/bot{$this->token}/{$method}";
    }

    /**
     * Декодирует входящий параметр data у callback_query 
     *
     * @return void
     */
    private function decodeCallback()
    {
        if (!$this->config('telegram.safe_callback')) {
            return;
        }

        if (!$this->hasUpdate() || !$this->update()->has('callback_query')) {
            return;
        }

        if (!$data = $this->update('callback_query.data')) {
            return;
        }

        $data = @gzinflate(base64_decode($data));

        if (!$data) {
            return;
        }

        $this->update()->set('callback_query.data', $data);
    }
}
