<?php

namespace FreedomtechHosting\PolydockAmazeeAIBackendClient;

use FreedomtechHosting\PolydockAmazeeAIBackendClient\Exception\HttpException;

class Client
{
    private string $baseUrl;
    private ?string $accessToken;
    private array $headers;

    /**
     * @param string $baseUrl
     * @param string|null $accessToken
     */
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

    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function login(string $email, string $password): array
    {
        return $this->post('/auth/login', [
            'username' => $email,
            'password' => $password,
        ]);
    }

    /**
     * @return array
     */
    public function logout(): array
    {
        return $this->post('/auth/logout');
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     */
    public function register(string $email, string $password): array
    {
        return $this->post('/auth/register', [
            'email' => $email,
            'password' => $password,
        ]);
    }

    /**
     * @return array
     */
    public function getMe(): array
    {
        return $this->get('/auth/me');
    }

    /**
     * @param array $data
     * @return array
     */
    public function updateMe(array $data): array
    {
        return $this->put('/auth/me/update', $data);
    }

    /**
     * @param string $name
     * @return array
     */
    public function createToken(string $name): array
    {
        return $this->post('/auth/token', ['name' => $name]);
    }

    /**
     * @return array
     */
    public function listTokens(): array
    {
        return $this->get('/auth/token');
    }

    /**
     * @param string $tokenId
     * @return array
     */
    public function deleteToken(string $tokenId): array
    {
        return $this->delete("/auth/token/{$tokenId}");
    }

    /**
     * @param int $regionId
     * @param string $name
     * @return array
     */
    public function createPrivateAIKeys(int $regionId, string $name, int $userId = 0): array
    {
        $data = [ 
            'region_id' => $regionId,
            'name' => $name,
        ];

        if ($userId > 0) {
            $data['user_id'] = $userId;
        }

        return $this->post('/private-ai-keys', $data);
    }

    /**
     * @return array
     */
    public function listPrivateAIKeys(): array
    {
        return $this->get('/private-ai-keys');
    }

    /**
     * @param string $keyName
     * @return array
     */
    public function deletePrivateAIKeys(string $keyName): array
    {
        return $this->delete("/private-ai-keys/{$keyName}");
    }

    /**
     * @return array
     */
    public function listRegions(): array
    {
        return $this->get('/regions');
    }

    /**
     * @param int $regionId
     * @return array
     */
    public function getRegion(int $regionId): array
    {
        return $this->get("/regions/{$regionId}");
    }

    /**
     * @param array $data
     * @return array
     */
    public function createRegion(array $data): array
    {
        return $this->post('/regions', $data);
    }

    /**
     * @param int $regionId
     * @param array $data
     * @return array
     */ 
    public function updateRegion(int $regionId, array $data): array
    {
        return $this->put("/regions/{$regionId}", $data);
    }

    /**
     * @param int $regionId
     * @return array
     */
    public function deleteRegion(int $regionId): array
    {
        return $this->delete("/regions/{$regionId}");
    }

    /**
     * @return array
     */
    public function listAdminRegions(): array
    {
        return $this->get('/regions/admin');
    }

    /**
     * @return array
     */
    public function listUsers(): array
    {
        return $this->get('/users');
    }

    /**
     * @param int $userId
     * @return array
     */
    public function getUser(int $userId): array
    {
        return $this->get("/users/{$userId}");
    }

    /**
     * @param string $email
     * @param string $password
     * @return array
     */ 
    public function createUser(string $email, string $password): array
    {
        return $this->post('/users', ['email' => $email, 'password' => $password]);
    }

    /**
     * @param int $userId
     * @param array $data
     * @return array
     */ 
    public function updateUser(int $userId, array $data): array
    {
        return $this->put("/users/{$userId}", $data);
    }

    /**
     * @param int $userId
     * @return array
     */
    public function deleteUser(int $userId): array
    {
        return $this->delete("/users/{$userId}");
    }

    /**
     * @param string $email
     * @return array
     */
    public function searchUsers(string $email): array
    {
        return $this->get('/users/search', ['email' => $email]);
    }

    /**
     * @return array
     */
    public function getAuditLogs(): array
    {
        return $this->get('/audit/logs');
    }

    /**
     * @return array
     */
    public function getAuditLogsMetadata(): array
    {
        return $this->get('/audit/logs/metadata');
    }

    /**
     * @return array
     */
    public function health(): array
    {
        return $this->get('/health');
    }

    /**
     * @param string $path
     * @param array $query
     * @return array
     */
    private function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, [], $query);
    }

    /**
     * @param string $path
     * @param array $data
     * @return array
     */ 
    private function post(string $path, array $data = []): array
    {
        return $this->request('POST', $path, $data);
    }

    /**
     * @param string $path
     * @param array $data
     * @return array
     */ 
    private function put(string $path, array $data = []): array
    {
        return $this->request('PUT', $path, $data);
    }

    /**
     * @param string $path
     * @return array
     */ 
    private function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param array $query
     * @return array
     */ 
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