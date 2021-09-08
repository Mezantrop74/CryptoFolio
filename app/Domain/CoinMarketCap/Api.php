<?php

namespace App\Domain\CoinMarketCap;

use App\Domain\Settings\ApiSettings;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use InvalidArgumentException;

/**
 * Class Api
 * @package App\Domain\CoinMarketCap
 */
class Api
{
    /**
     * @var string
     */
    protected $api_key = '';

    /**
     * @var GuzzleHttp\Client
     */
    protected $http_client = null;

    /**
     * @var string
     */
    protected $endpoint = '';

    /**
     * @var boolean
     */
    protected $debug = false;

    /**
     * @var boolean
     */
    protected $verify = false;

    /**
     *
     */
    public function __construct()
    {
        $this->_init();
    }

    /**
     *
     */
    private function _init()
    {
        $this->api_key = (new ApiSettings)->cmc_api_token ?? null;
        if ($this->api_key == null) {
            throw new InvalidArgumentException('CMC Api token not set. Go to admin settings.');
        }
        $this->endpoint = config('coinmarketcap.endpoint');
        $this->http_client = new Client([
            'base_uri' => $this->endpoint
        ]);
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $type
     *
     * @return array|boolean
     *
     */
    public function _call($method, $params, $type = 'GET')
    {
        /**
         *
         */
        $headers = [
            'X-CMC_PRO_API_KEY' => $this->api_key
        ];

        $url = $method . '?' . http_build_query($params);

        try {

            $request = $this->http_client->request($type, $url, [
                'headers' => $headers,
                'debug' => $this->debug,
                'verify' => $this->verify

            ]);

            return json_decode($request->getBody(), true);

        } catch (ClientException $e) {
            return json_decode($e->getResponse()->getBody(), true);
        } catch (ConnectException $e) {
        }
    }

    /**
     * Set to true or set to a PHP stream returned by fopen() to enable debug output with the handler used to send a request.
     * For example, when using cURL to transfer requests, cURL's verbose of CURLOPT_VERBOSE will be emitted.
     * When using the PHP stream wrapper, stream wrapper notifications will be emitted.
     * If set to true, the output is written to PHP's STDOUT.
     * If a PHP stream is provided, output is written to the stream
     *
     * @param boolean $debug
     * @return void
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return boolean
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * Describes the SSL certificate verification behavior of a request.
     *
     * @param boolean $verify
     * @return void
     */
    public function setVerify($verify)
    {
        $this->verify = $verify;
    }

    /**
     * @return boolean
     */
    public function getVerify()
    {
        return $this->verify;
    }
}

