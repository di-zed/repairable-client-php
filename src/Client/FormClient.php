<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Client;

use DiZed\RepairableClient\Config\FormConfig;
use DiZed\RepairableClient\Response\FormResponse;

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
     * @param array $params
     * @return string
     */
    public function getFormHtml(array $params = []): string
    {
        $result = '';

        try {
            $response = $this->request(self::URL_API_GET_FORM, $params);
            if ($response->isSuccess()) {
                $result = $response->getResult();
            }
        } catch (\Exception $e) {
            return '';
        }

        return $result;
    }

    /**
     * @inheritDoc
     * @return FormConfig
     */
    protected function initConfig(string $publicKey, string $privateKey, array $config = []): FormConfig
    {
        return new FormConfig($publicKey, $privateKey, $config);
    }

    /**
     * @inheritDoc
     * @return FormResponse
     */
    protected function initResponse(int $status, string $body, array $headers = [], array $cookies = []): FormResponse
    {
        return new FormResponse($status, $body, $headers, $cookies);
    }
}
