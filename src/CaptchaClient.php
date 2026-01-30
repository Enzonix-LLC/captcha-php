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

    public function __construct(string $secret, ?ClientInterface $http = null, string $apiUrl = 'https://verify.enzonix.com')
    {
        $this->secret = $secret;
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->http = $http ?? new Client(['timeout' => 5.0]);
    }

    /**
     * Verify a captcha token.
     *
     * Returns a CaptchaResponse object with parsed fields.
     *
     * @param string $token
     * @param string|null $remoteIp
     * @return CaptchaResponse
     * @throws \RuntimeException on HTTP or parse errors
     */
    public function verify(string $token, ?string $remoteIp = null): CaptchaResponse
    {
        $payload = ['secret' => $this->secret, 'response' => $token];
        if ($remoteIp) {
            $payload['remoteip'] = $remoteIp;
        }

        try {
            $res = $this->http->request('POST', $this->apiUrl . '/siteverify', [
                'form_params' => $payload,
                'headers' => ['Accept' => 'application/json']
            ]);
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
}
