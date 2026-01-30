<?php

declare(strict_types=1);

namespace Enzonix\Captcha;

class CaptchaResponse
{
    private bool $success = false;
    private ?float $score = null;
    private ?string $action = null;
    private ?string $challengeTs = null;
    private ?string $hostname = null;
    private array $errors = [];

    public static function fromArray(array $data): self
    {
        $r = new self();
        $r->success = !empty($data['success']);
        $r->score = isset($data['score']) ? (float)$data['score'] : null;
        $r->action = $data['action'] ?? null;
        $r->challengeTs = $data['challenge_ts'] ?? null;
        $r->hostname = $data['hostname'] ?? null;
        $r->errors = $data['error-codes'] ?? [];
        return $r;
    }

    public function isSuccess(): bool { return $this->success; }
    public function getScore(): ?float { return $this->score; }
    public function getAction(): ?string { return $this->action; }
    public function getChallengeTs(): ?string { return $this->challengeTs; }
    public function getHostname(): ?string { return $this->hostname; }
    public function getErrors(): array { return $this->errors; }
}
