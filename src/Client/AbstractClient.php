<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Client;

use DiZed\RepairableClient\Config\AbstractConfig;
use DiZed\RepairableClient\Client\Http\Curl;
use DiZed\RepairableClient\Exception\ApiException;
use DiZed\RepairableClient\Response\AbstractResponse;

/**
 * The main Client class.
 */
abstract class AbstractClient
{
    /**
     * @var AbstractConfig
     */
    protected AbstractConfig $config;

    /**
     * @var Curl
     */
    protected Curl $curl;

    /**
     * Client constructor.
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $config
     */
    public function __construct(string $publicKey, string $privateKey, array $config = [])
    {
        $this->config = $this->initConfig($publicKey, $privateKey, $config);
        $this->curl = new Curl();
    }

    /**
     * Get Config class.
     *
     * @return AbstractConfig
     */
    public function getConfig(): AbstractConfig
    {
        return $this->config;
    }

    /**
     * Get Curl wrapper.
     *
     * @return Curl
     */
    public function getCurl(): Curl
    {
        return $this->curl;
    }

    /**
     * Send POST request.
     *
     * @param string $uri
     * @param array $params
     * @return AbstractResponse
     * @throws ApiException
     */
    public function request(string $uri, array $params = []): AbstractResponse
    {
        try {
            $this->curl->setCredentials($this->config->getPublicKey(), $this->config->getPrivateKey());
            $this->curl->setHeader('Content-Type', 'application/json');
            $this->curl->sendPost($uri, array_merge($this->config->getConfig(), $params));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }

        return $this->initResponse(
            $this->curl->getStatus(),
            $this->curl->getBody(),
            $this->curl->getHeaders(),
            $this->curl->getCookies(true)
        );
    }

    /**
     * Initialize the correct Config class.
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $config
     * @return AbstractConfig
     */
    abstract protected function initConfig(string $publicKey, string $privateKey, array $config = []): AbstractConfig;

    /**
     * Response initialization.
     *
     * @param int $status
     * @param string $body
     * @param array $headers
     * @param array $cookies
     * @return AbstractResponse
     */
    abstract protected function initResponse(
        int $status,
        string $body,
        array $headers = [],
        array $cookies = []
    ): AbstractResponse;
}
