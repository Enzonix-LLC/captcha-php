<?php

declare(strict_types=1);

namespace Enzonix\Captcha;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class CaptchaClient
{
    private ClientInterface $http;
    private string $secret;
    private string $apiUrl;
    private float $timeout;

    public function __construct(
        string $secret,
        ?ClientInterface $http = null,
        string $apiUrl = 'https://verify.enzonix.com',
        float $timeout = 10.0
    ) {
        $this->secret = $secret;
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->timeout = $timeout;
        $this->http = $http ?? new Client(['timeout' => $this->timeout]);
    }

    /**
     * Verify a captcha token.
     *
     * Returns a CaptchaResponse object with parsed fields.
     *
     * @param string $token
     * @param string|null $remoteIp
     * @param float|null $timeout Override the default timeout for this request (in seconds)
     * @return CaptchaResponse
     * @throws \RuntimeException on HTTP or parse errors
     */
    public function verify(string $token, ?string $remoteIp = null, ?float $timeout = null): CaptchaResponse
    {
        $payload = ['secret' => $this->secret, 'response' => $token];
        if ($remoteIp) {
            $payload['remoteip'] = $remoteIp;
        }

        $requestOptions = [
            'form_params' => $payload,
            'headers' => ['Accept' => 'application/json']
        ];

        if ($timeout !== null) {
            $requestOptions['timeout'] = $timeout;
        }

        try {
            $res = $this->http->request('POST', $this->apiUrl . '/siteverify', $requestOptions);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('HTTP request failed: ' . $e->getMessage(), 0, $e);
        }

        $body = (string)$res->getBody();
        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new \RuntimeException('Invalid JSON response from verification endpoint');
        }

        return CaptchaResponse::fromArray($data);
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function withTimeout(float $timeout): self
    {
        $clone = clone $this;
        $clone->timeout = $timeout;
        $clone->http = new Client(['timeout' => $timeout]);
        return $clone;
    }
}
