# Enzonix Captcha PHP Client

A small, lightweight PHP client for verifying captcha tokens against Enzonix's verification endpoint (`https://verify.enzonix.com`).

## Installation

Require the package with Composer (after publishing to Packagist) or include locally:

```bash
composer require enzonix/captcha
```

## Usage

```php
use Enzonix\Captcha\CaptchaClient;

$client = new CaptchaClient(getenv('ENZONIX_SECRET'));
$response = $client->verify($_POST['captcha_token'] ?? '');

if ($response->isSuccess()) {
    // accepted
    $score = $response->getScore();
} else {
    $errors = $response->getErrors();
}
```
