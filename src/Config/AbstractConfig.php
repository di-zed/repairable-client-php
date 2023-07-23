<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Config;

use DiZed\RepairableClient\Exception\ConfigException;

/**
 * The main Config class.
 */
class AbstractConfig
{
    /**
     * @var string
     */
    private string $publicKey;

    /**
     * @var string
     */
    private string $privateKey;

    /**
     * @var array Configuration parameters.
     */
    private array $config = [];

    /**
     * Config constructor.
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $config
     */
    public function __construct(string $publicKey, string $privateKey, array $config = [])
    {
        $this->setDefaultConfig($publicKey, $privateKey, $config);
    }

    /**
     * Get parameter value.
     *
     * @param string $paramName
     * @param mixed $value
     * @return $this
     */
    public function setParam(string $paramName, $value)
    {
        $this->config[$paramName] = $value;
        return $this;
    }

    /**
     * Get parameter value.
     *
     * @param string $paramName
     * @return mixed
     * @throws ConfigException
     */
    public function getParam(string $paramName)
    {
        if (!array_key_exists($paramName, $this->config)) {
            throw new ConfigException(sprintf('The requested parameter "%s" was not found.', $paramName));
        }

        return $this->config[$paramName];
    }

    /**
     * Get Public Key.
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Get Private Key.
     *
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Get Config.
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Set Default Config values.
     *
     * @param string $publicKey
     * @param string $privateKey
     * @param array $config
     * @return $this
     * @throws ConfigException
     */
    private function setDefaultConfig(string $publicKey, string $privateKey, array $config = [])
    {
        if (!trim($publicKey)) {
            throw new ConfigException('The public key cannot be empty.');
        }
        if (!trim($privateKey)) {
            throw new ConfigException('The private key cannot be empty.');
        }

        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;

        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                throw new ConfigException(sprintf('Configuration parameter "%s" is not defined.', $key));
            }
            $this->setParam($key, $value);
        }

        return $this;
    }
}
