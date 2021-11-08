<?php

namespace Litegram\Traits\Http;

use Exception;
use Litegram\Support\Collection;

trait Request
{
    /**
     * @var \CurlHandle
     */
    private $curl;

    /**
     * A universal executor of Telegram methods.
     *
     * @param string $method
     * @param array|null $parameters
     * @return Collection
     * @throws Exception
     */
    public function api(string $method, ?array $parameters = [])
    {
        $this->curl = $this->curl ?: $this->initTelegramCurlClient();

        curl_setopt($this->curl, CURLOPT_URL, "https://api.telegram.org/bot{$this->token}/{$method}");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, (array) $parameters);

        $response = curl_exec($this->curl);

        if (curl_error($this->curl)) {
            throw new Exception('Litegram: ' . curl_errno($this->curl) . ' - ' . curl_error($this->curl));
        }

        $response = new Collection(json_decode($response, true));

        // request errors logging
        if (
            $response->get('ok', false) === false
            && $this->config('errors.telegram')
            && $logPath = $this->config('errors.path')
        ) {
            $logPath = rtrim($logPath, '/\\');

            $backtrace = debug_backtrace();
            // $stack = end($backtrace);
            $stack = $backtrace[0];

            $stacks = [];
            foreach ($backtrace as $key => $value) {
                if (isset($value['file'])) {
                    $stacks[] = $value['file'] . ':' . $value['line'];
                }
            }

            $erorrMessage = var_export([
                'stack' => $stacks,
                'function' => $stack['function'],
                'api' => $method,
                'parameters' => (array) $parameters,
                'response' => $response->toArray(),
            ], true);

            file_put_contents("{$logPath}/telegram_errors.log",  "[".date('d.m.Y H:i:s')."] {$erorrMessage}" . PHP_EOL, FILE_APPEND);

            if ($this->config('errors.telegram_output')) {
                $this->cli->log($erorrMessage, 'error');
            }
        }

        return $response;
    }

    /**
     * Init curl client.
     *
     * @return \CurlHandle
     */
    private function initTelegramCurlClient()
    {
        $host = [implode(':', [
            'api.telegram.org',
            443,
            gethostbyname('api.telegram.org'),
        ])];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RESOLVE, $host);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        curl_setopt($ch, CURLOPT_POST, true);

        return $ch;
    }

    /**
     * Собирает параметры в единый массив.
     *
     * @param array $parameters
     * @param array|string $keyboard
     * @param array $extra
     *
     * @return array
     */
    private function buildRequestParams($parameters = [], $keyboard = null, $extra = []): array
    {
        if ($keyboard) {
            $parameters['reply_markup'] = is_array($keyboard) ? $this->keyboard($keyboard) : $keyboard;
        }

        $parameters['parse_mode'] = $this->config('telegram.parse_mode', 'html');

        if (!empty($parameters['text'])) {
            $parameters['text'] = implode("\n", array_map('trim', explode("\n", $parameters['text'])));
            $parameters['text'] = str_replace('<<<', '«', $parameters['text']);
            $parameters['text'] = str_replace('>>>', '»', $parameters['text']);
        }

        if (!empty($parameters['caption'])) {
            $parameters['caption'] = implode("\n", array_map('trim', explode("\n", $parameters['caption'])));
            $parameters['caption'] = str_replace('<<<', '«', $parameters['caption']);
            $parameters['caption'] = str_replace('>>>', '»', $parameters['caption']);
        }

        return array_merge($parameters, (array) $extra);
    }
}