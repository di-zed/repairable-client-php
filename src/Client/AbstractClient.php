<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Client;

use DiZed\RepairableClient\Config\AbstractConfig;
use DiZed\RepairableClient\Client\Http\Curl;

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

    public function sendPost(string $uri, array $params = [])
    {
        $this->curl->setCredentials($this->config->getPublicKey(), $this->config->getPrivateKey());
        $this->curl->sendPost($uri, array_merge($this->config->getConfig(), $params));

        return $this->curl->getBody();
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
}
