<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;

class HcmisService
{
    protected $client;

    public function __construct()
    {
        $base = config('hcmis.base_uri');
        $this->client = new Client([
            'base_uri' => $base,
            'timeout' => 15,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    protected function defaultHeaders($useToken = true)
    {
        $headers = ['Accept' => 'application/json'];
        if ($useToken) {
            $token = $this->getToken();
            if ($token) {
                $headers['Authorization'] = 'Bearer '.$token;
            }
        }
        return $headers;
    }

    public function getToken()
    {
        $token = Cache::get('hcmis.token');
        if ($token) {
            return $token;
        }
        return config('hcmis.token');
    }

    public function setToken(string $token, int $seconds = 7200)
    {
        Cache::put('hcmis.token', $token, $seconds);
    }

    public function request(string $method, string $path, array $options = [], bool $useToken = true)
    {
        $opts = [];
        // merge headers
        $headers = $this->defaultHeaders($useToken);
        if (isset($options['headers'])) {
            $headers = array_merge($headers, $options['headers']);
        }
        $opts['headers'] = $headers;

        if (!empty($options['query'])) {
            $opts['query'] = $options['query'];
        }
        if (isset($options['json'])) {
            $opts['json'] = $options['json'];
        } elseif (isset($options['body'])) {
            $opts['body'] = $options['body'];
        }

        try {
            $res = $this->client->request($method, ltrim($path, '/'), $opts);
            $body = $res->getBody()->getContents();
            return json_decode($body, true) ?? $body;
        } catch (RequestException $e) {
            $resp = $e->getResponse();
            $message = $e->getMessage();
            $status = $resp ? $resp->getStatusCode() : null;
            $body = $resp ? $resp->getBody()->getContents() : null;
            return ['error' => true, 'message' => $message, 'status' => $status, 'body' => $body];
        }
    }

    // convenience methods
    public function get(string $path, array $query = [], bool $useToken = true)
    {
        return $this->request('GET', $path, ['query' => $query], $useToken);
    }

    public function post(string $path, array $data = [], bool $useToken = true)
    {
        return $this->request('POST', $path, ['json' => $data], $useToken);
    }

    public function put(string $path, array $data = [], bool $useToken = true)
    {
        return $this->request('PUT', $path, ['json' => $data], $useToken);
    }

    public function delete(string $path, array $data = [], bool $useToken = true)
    {
        return $this->request('DELETE', $path, ['json' => $data], $useToken);
    }

    public function login(string $email, string $password)
    {
        return $this->post('/api/login', ['email' => $email, 'password' => $password], false);
    }
}
