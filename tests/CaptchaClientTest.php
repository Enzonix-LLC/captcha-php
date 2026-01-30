<?php

declare(strict_types=1);

namespace Enzonix\Captcha\Tests;

use Enzonix\Captcha\CaptchaClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CaptchaClientTest extends TestCase
{
    public function testVerifyParsesSuccess(): void
    {
        $body = json_encode([
            'success' => true,
            'score' => 0.9,
            'action' => 'login',
            'challenge_ts' => '2026-01-30T12:00:00Z',
            'hostname' => 'example.com'
        ]);

        $mock = new MockHandler([
            new GuzzleResponse(200, ['Content-Type' => 'application/json'], $body)
        ]);

        $handler = HandlerStack::create($mock);
        $guzzle = new Client(['handler' => $handler]);

        $client = new CaptchaClient('secret', $guzzle, 'https://verify.enzonix.com');
        $resp = $client->verify('token123');

        $this->assertTrue($resp->isSuccess());
        $this->assertSame(0.9, $resp->getScore());
        $this->assertSame('login', $resp->getAction());
    }
}
