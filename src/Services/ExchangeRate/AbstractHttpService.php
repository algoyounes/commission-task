<?php

namespace AlgoYounes\CommissionTask\Services\ExchangeRate;

use AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions\HttpRequestException;
use AlgoYounes\CommissionTask\Services\ExchangeRate\Exceptions\InvalidResponseException;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Throwable;

abstract class AbstractHttpService
{
    protected Client $client;

    public function __construct(
        private readonly string $baseUri,
        private readonly int    $retryCount = 3,
        private readonly int    $timeout    = 30,
    ) {
        $this->client = $this->initClient();
    }

    protected function sendRequest(string $method, string $uri, array $options = []): array
    {
        $response = $this->client->request($method, $uri, $options);

        if ($response->getStatusCode() !== 200) {
            throw new HttpRequestException($response->getStatusCode());
        }

        $responseBody = json_decode((string) $response->getBody(), true);
        if (! is_array($responseBody)) {
            throw new InvalidResponseException(
                $response->getStatusCode(),
                (string) $response->getBody()
            );
        }

        return $responseBody;
    }

    protected function get(string $uri, array $options = []): array
    {
        return $this->sendRequest('GET', $uri, $options);
    }

    abstract protected function buildRequest(): array;

    private function initClient(): Client
    {
        $stack = HandlerStack::create();
        $stack->push(Middleware::retry($this->retryDecider()));

        return new Client(
            [
                'base_uri'                      => $this->baseUri,
                RequestOptions::TIMEOUT         => $this->timeout,
                RequestOptions::CONNECT_TIMEOUT => $this->timeout,
                RequestOptions::HTTP_ERRORS     => false,
                'handler'                       => $stack,
                RequestOptions::HEADERS         => [
                    'Content-Type' => 'application/json',
                    'Accept'       => 'application/json',
                ],
                ...$this->buildRequest(),
            ]
        );
    }

    private function retryDecider(): callable
    {
        return function (int $retries, $req, ?ResponseInterface $response, ?Throwable $error) {
            if ($retries >= $this->retryCount) {
                return false;
            }

            if (! $response instanceof ResponseInterface) {
                return true;
            }

            if ($response->getStatusCode() >= 500) {
                return true;
            }

            if ($error) {
                return true;
            }

            return false;
        };
    }
}
