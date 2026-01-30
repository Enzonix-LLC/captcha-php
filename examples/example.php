<?php

require __DIR__ . '/../vendor/autoload.php';

use Enzonix\Captcha\CaptchaClient;

$client = new CaptchaClient('your-secret-here');
$token = 'token-from-client';

try {
    $resp = $client->verify($token);
    if ($resp->isSuccess()) {
        echo "Verified. score: " . ($resp->getScore() ?? 'n/a') . PHP_EOL;
    } else {
        echo "Verification failed: " . implode(', ', $resp->getErrors()) . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
