<?php

namespace FreedomtechHosting\PolydockAmazeeAIBackendClient;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class Client
{
    private string $baseUrl;
    private ?string $accessToken;
    private array $headers;

    public function __construct(string $baseUrl, ?string $accessToken = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->accessToken = $accessToken;
        $this->headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        if ($accessToken) {
            $this->headers['Authorization'] = "Bearer {$accessToken}";
        }
    }

    // Auth endpoints
    public function login(string $email, string $password): array
    {
        return $this->post('/auth/login', [
            'username' => $email,
            'password' => $password,
        ]);
    }

    public function logout(): array
    {
        return $this->post('/auth/logout');
    }

    public function register(string $email, string $password): array
    {
        return $this->post('/auth/register', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    public function getMe(): array
    {
        return $this->get('/auth/me');
    }

    public function updateMe(array $data): array
    {
        return $this->put('/auth/me/update', $data);
    }

    // Token management
    public function createToken(string $name): array
    {
        return $this->post('/auth/token', ['name' => $name]);
    }

    public function listTokens(): array
    {
        return $this->get('/auth/token');
    }

    public function deleteToken(string $tokenId): array
    {
        return $this->delete("/auth/token/{$tokenId}");
    }

    // Private AI Keys
    public function createPrivateAIKey(int $regionId, string $name): array
    {
        return $this->post('/private-ai-keys', [
            'region_id' => $regionId,
            'name' => $name,
        ]);
    }

    public function listPrivateAIKeys(): array
    {
        return $this->get('/private-ai-keys');
    }

    public function deletePrivateAIKey(string $keyName): array
    {
        return $this->delete("/private-ai-keys/{$keyName}");
    }

    // Region management
    public function listRegions(): array
    {
        return $this->get('/regions');
    }

    public function getRegion(int $regionId): array
    {
        return $this->get("/regions/{$regionId}");
    }

    public function createRegion(array $data): array
    {
        return $this->post('/regions', $data);
    }

    public function updateRegion(int $regionId, array $data): array
    {
        return $this->put("/regions/{$regionId}", $data);
    }

    public function deleteRegion(int $regionId): array
    {
        return $this->delete("/regions/{$regionId}");
    }

    public function listAdminRegions(): array
    {
        return $this->get('/regions/admin');
    }

    // User management
    public function listUsers(): array
    {
        return $this->get('/users');
    }

    public function getUser(int $userId): array
    {
        return $this->get("/users/{$userId}");
    }

    public function createUser(string $email, string $password): array
    {
        return $this->post('/users', ['email' => $email, 'password' => $password]);
    }

    public function updateUser(int $userId, array $data): array
    {
        return $this->put("/users/{$userId}", $data);
    }

    public function deleteUser(int $userId): array
    {
        return $this->delete("/users/{$userId}");
    }

    public function searchUsers($email): array
    {
        return $this->get('/users/search', ['email' => $email]);
    }

    // Audit logs
    public function getAuditLogs(): array
    {
        return $this->get('/audit/logs');
    }

    public function getAuditLogsMetadata(): array
    {
        return $this->get('/audit/logs/metadata');
    }

    // Health check
    public function health(): array
    {
        return $this->get('/health');
    }

    // HTTP methods
    private function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, [], $query);
    }

    private function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, $data);
    }

    private function put(string $path, array $data = []): array
    {
        return $this->request('PUT', $path, $data);
    }

    private function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    private function request(string $method, string $path, array $data = [], array $query = []): array
    {
        $url = $this->baseUrl . $path;
        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        $headers = [];
        foreach ($this->headers as $key => $value) {
            $headers[] = "{$key}: {$value}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decodedResponse = json_decode($response, true);

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new HttpException(
                $statusCode,
                "API request failed with status code: {$statusCode}",
                $decodedResponse
            );
        }

        return $decodedResponse;
    }
} 