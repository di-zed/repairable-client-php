<?php
/**
 * @author DiZed Team
 * @copyright Copyright (c) DiZed Team (https://github.com/di-zed/)
 */
namespace DiZed\RepairableClient\Client\Http;

/**
 * The CURL wrapper.
 */
class Curl
{
    /**
     * @var array
     */
    protected array $cookies = [];

    /**
     * @var \CurlHandle
     */
    protected ?\CurlHandle $curl = null;

    /**
     * @var int
     */
    protected int $headerCount = 0;

    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * @var int
     */
    protected int $port = 80;

    /**
     * @var string
     */
    protected string $responseBody;

    /**
     * @var array
     */
    protected array $responseHeaders = [];

    /**
     * @var int
     */
    protected int $responseStatus = 0;

    /**
     * @var int
     */
    protected int $timeout = 300;

    /**
     * @var array
     */
    protected array $userOptions = [];

    /**
     * @var int|null
     */
    private $sslVersion;

    /**
     * Curl constructor.
     *
     * @param int|null $sslVersion
     */
    public function __construct(int $sslVersion = null)
    {
        $this->sslVersion = $sslVersion;
    }

    /**
     * Send CURL request.
     *
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function sendRequest(string $method, string $uri, array $params = []): static
    {
        $this->curl = curl_init();

        curl_setopt($this->curl, CURLOPT_URL, $uri);
        curl_setopt($this->curl, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS | CURLPROTO_FTP | CURLPROTO_FTPS);

        if ($method == 'GET') {
            curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
        } elseif ($method == 'POST') {
            curl_setopt($this->curl, CURLOPT_POST, 1);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        if ($this->port != 80) {
            curl_setopt($this->curl, CURLOPT_PORT, $this->port);
        }

        if ($this->timeout) {
            curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        }

        if (count($this->headers)) {
            $headers = [];
            foreach ($this->headers as $k => $v) {
                $headers[] = $k . ': ' . $v;
            }
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        }

        if (count($this->cookies)) {
            $cookies = [];
            foreach ($this->cookies as $k => $v) {
                $cookies[] = "{$k}={$v}";
            }
            curl_setopt($this->curl, CURLOPT_COOKIE, implode(";", $cookies));
        }

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'parseHeaders']);

        if ($this->sslVersion !== null) {
            curl_setopt($this->curl, CURLOPT_SSLVERSION, $this->sslVersion);
        }

        if (count($this->userOptions)) {
            foreach ($this->userOptions as $k => $v) {
                curl_setopt($this->curl, $k, $v);
            }
        }

        $this->headerCount = 0;
        $this->responseHeaders = [];
        $this->responseBody = curl_exec($this->curl);

        if ($error = curl_errno($this->curl)) {
            throw new \Exception($error);
        }
        curl_close($this->curl);

        return $this;
    }

    /**
     * Send GET request.
     *
     * @param string $uri
     * @return $this
     * @throws \Exception
     */
    public function sendGet(string $uri): static
    {
        return $this->sendRequest('GET', $uri);
    }

    /**
     * Send POST request.
     *
     * @param string $uri
     * @param array $params
     * @return $this
     * @throws \Exception
     */
    public function sendPost(string $uri, array $params = []): static
    {
        return $this->sendRequest('POST', $uri, $params);
    }

    /**
     * Set port.
     *
     * @param int $value
     * @return $this
     */
    public function setPort(int $value): static
    {
        $this->port = $value;
        return $this;
    }

    /**
     * Set timeout.
     *
     * @param int $value
     * @return $this
     */
    public function setTimeout(int $value): static
    {
        $this->timeout = $value;
        return $this;
    }

    /**
     * Set headers.
     *
     * @param array $headers
     * @return $this
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Set header.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setHeader(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    /**
     * Remove header.
     *
     * @param string $name
     * @return bool
     */
    public function removeHeader(string $name): bool
    {
        if (!array_key_exists($name, $this->headers)) {
            return false;
        }

        unset($this->headers[$name]);
        return true;
    }

    /**
     * Set cookies.
     *
     * @param array $cookies
     * @return $this
     */
    public function setCookies(array $cookies): static
    {
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * Set cookie.
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function setCookie(string $name, string $value): static
    {
        $this->cookies[$name] = $value;
        return $this;
    }

    /**
     * Remove cookies.
     *
     * @return bool
     */
    public function removeCookies(): bool
    {
        $this->setCookies([]);
        return true;
    }

    /**
     * Remove cookie.
     *
     * @param string $name
     * @return bool
     */
    public function removeCookie(string $name): bool
    {
        if (!array_key_exists($name, $this->cookies)) {
            return false;
        }

        unset($this->cookies[$name]);
        return true;
    }

    /**
     * Set credentials.
     *
     * @param string $login
     * @param string $password
     * @return $this
     */
    public function setCredentials(string $login, string $password): static
    {
        $val = base64_encode("{$login}:{$password}");
        $this->setHeader("Authorization", "Basic {$val}");

        return $this;
    }

    /**
     * Set options.
     *
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): static
    {
        $this->userOptions = $options;
        return $this;
    }

    /**
     * Set option.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setOption($name, $value): static
    {
        $this->userOptions[$name] = $value;
        return $this;
    }

    /**
     * Get response status code.
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->responseStatus;
    }

    /**
     * Get response body.
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->responseBody;
    }

    /**
     * Get response headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->responseHeaders;
    }

    /**
     * Get cookies.
     *
     * @param bool $isFull
     * @return array
     */
    public function getCookies(bool $isFull = false): array
    {
        if (empty($this->responseHeaders['Set-Cookie'])) {
            return [];
        }

        $out = [];

        foreach ($this->responseHeaders['Set-Cookie'] as $row) {
            $values = explode("; ", $row ?? '');
            if (!$count = count($values)) {
                continue;
            }
            list($key, $val) = explode("=", $values[0]);
            if ($val === null) {
                continue;
            }
            if (!$isFull) {
                $out[trim($key)] = trim($val);
            } else {
                $out[trim($key)] = ['value' => trim($val)];
                array_shift($values);
                $count--;
                if (!$count) {
                    continue;
                }
                for ($i = 0; $i < $count; $i++) {
                    list($subkey, $val) = explode("=", $values[$i]);
                    $out[trim($key)][trim($subkey)] = ($val !== null ? trim($val) : '');
                }
            }
        }

        return $out;
    }

    /**
     * Parse headers.
     *
     * @param $curl
     * @param string|null $data
     * @return int
     * @throws \Exception
     */
    protected function parseHeaders($curl, string $data = null)
    {
        $data = ($data !== null ? $data : '');

        if ($this->headerCount == 0) {
            $line = explode(" ", trim($data), 3);
            if (count($line) < 2) {
                throw new \Exception(sprintf('Invalid response line returned from server: %s.', $data));
            }
            $this->responseStatus = (int)$line[1];
        } else {
            $name = '';
            $value = '';
            $out = explode(": ", trim($data), 2);
            if (count($out) == 2) {
                $name = $out[0];
                $value = $out[1];
            }
            if (strlen($name)) {
                if (strtolower($name) === 'set-cookie') {
                    $this->responseHeaders['Set-Cookie'][] = $value;
                } else {
                    $this->responseHeaders[$name] = $value;
                }
            }
        }

        $this->headerCount++;

        return strlen($data);
    }
}
