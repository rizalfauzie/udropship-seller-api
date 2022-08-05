<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\RequestOptions;

class Controller extends BaseController
{
    /**
     * @var Client
     */
    protected $http;

    /**
     * @return Http
     */
    protected function _http()
    {
        if ($this->http === null) {
            $token = config('app.magento.api_token');
            $apiUrl = config('app.magento.api_url');
            $this->http = Http::withToken($token)
                ->baseUrl($apiUrl)
                ->timeout(120)
                ->acceptJson()
            ;
        }
        return $this->http;
    }

    /**
     * @param string $path
     * @param array $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function httpGet($path, $query = [])
    {
        return $this->_http()->get($path, $query);
    }

    /**
     * @param string $path
     * @param array $query
     * @return \Illuminate\Http\Client\Response
     */
    protected function httpPut($path, $query = [])
    {
        return $this->_http()
            ->put($path, $query);
    }

    /**
     * @param string $path
     * @param array $body
     * @return \Illuminate\Http\Client\Response
     */
    protected function httpPost($path, $body = [])
    {
        return $this->_http()
            ->post($path, $body);
    }
}
