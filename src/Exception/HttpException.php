<?php

namespace FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception;

class HttpException extends \RuntimeException
{
    private int $statusCode;
    private ?array $response;

    public function __construct(int $statusCode, string $message = "", ?array $response = null)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->response = $response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
} 