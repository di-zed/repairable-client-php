<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Response;

use DiZed\RepairableClient\Exception\ApiException;

/**
 * The main Response class.
 */
class AbstractResponse
{
    /**
     * The required fields in the response with default values.
     */
    const RESPONSE_REQUIRED_FIELDS = [
        'is_success' => false,
        'response_data' => [],
    ];

    /**
     * @var int
     */
    private int $status;

    /**
     * @var string
     */
    private string $body;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var array
     */
    private array $cookies;

    /**
     * Response constructor.
     *
     * @param int $status
     * @param string $body
     * @param array $headers
     * @param array $cookies
     */
    public function __construct(int $status, string $body, array $headers = [], array $cookies = [])
    {
        $this->status = $status;
        $this->body = $body;
        $this->headers = $headers;
        $this->cookies = $cookies;
    }

    /**
     * Get status.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Get body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Get decoded body.
     *
     * @return array
     * @throws ApiException
     */
    public function getDecodedBody(): array
    {
        try {
            $body = $this->getBody();
            $data = (is_array($body) ? $body : json_decode($body, true));
            return array_merge_recursive(self::RESPONSE_REQUIRED_FIELDS, $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage());
        }

        return [];
    }

    /**
     * Get headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get cookies.
     *
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    /**
     * Get result.
     *
     * @return array
     * @throws ApiException
     */
    public function getResult(): array
    {
        $data = $this->getDecodedBody();
        if (!empty($data['response_data']) && is_array($data['response_data'])) {
            return $data['response_data'];
        }

        return [];
    }

    /**
     * Is success?
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        if ($this->getStatus() == 200) {
            try {
                $data = $this->getDecodedBody();
                if (!empty($data['is_success']) && is_bool($data['is_success'])) {
                    return true;
                }
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }
}
