<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Client;

use DiZed\RepairableClient\Config\FormConfig;

/**
 * The Form Client class.
 */
class FormClient extends AbstractClient
{
    /**
     * Get form by this URL.
     */
    const URL_API_GET_FORM = 'http://repairable.loc/get-iframe.php';

    /**
     * Get form HTML code.
     *
     * @return string
     */
    public function getFormHtml(): string
    {
        $this->sendPost(self::URL_API_GET_FORM);

        return '';
    }

    /**
     * @inheritDoc
     */
    protected function initConfig(string $publicKey, string $privateKey, array $config = []): FormConfig
    {
        return new FormConfig($publicKey, $privateKey, $config);
    }
}
