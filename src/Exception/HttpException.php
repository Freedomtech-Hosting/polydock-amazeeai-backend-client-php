<?php

namespace FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception;

class HttpException extends \RuntimeException
{
    private int $statusCode;
    private ?array $response;

    /**
     * @param int $statusCode
     * @param string $message
     * @param array|null $response
     */
    public function __construct(int $statusCode, string $message = "", ?array $response = null)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }
} 