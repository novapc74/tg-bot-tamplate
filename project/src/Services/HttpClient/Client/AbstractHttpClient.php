<?php

namespace App\Services\HttpClient\Client;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Services\HttpClient\Dto\HttpClientDtoInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

abstract class AbstractHttpClient
{
    protected array $options;

    public function __construct(
        private readonly HttpClientInterface $client,
        protected string                     $url,
        protected readonly string            $token,
        private readonly LoggerInterface     $logger,
    )
    {
    }

    abstract protected function auth(): void;

    private function exec(string $apiMethod, string $httpMethod, array $options = []): ?HttpClientResponseDto
    {
        $this->options = $options;

        $this->auth();

        $uri = $this->url . DIRECTORY_SEPARATOR . $apiMethod;

        try {
            $response = $this->client->request($httpMethod, $uri, $this->options);

            $statusCode = $response->getStatusCode();
            if ($statusCode >= 400) {
                $this->logger->error(
                    sprintf(
                        'The method "%s" returned a %d HTTP response code. response: %s',
                        $apiMethod,
                        $statusCode,
                        $response->getContent(false)
                    ),
                );
                return null;
            }

            $data = $response->toArray();

        } catch (
        TransportExceptionInterface
        |ServerExceptionInterface
        |ClientExceptionInterface
        |DecodingExceptionInterface
        |RedirectionExceptionInterface $e
        ) {

            $this->logger->error(
                sprintf('The "%s" method failed with an error: %s', $apiMethod, $e->getMessage()),
            );

            return null;
        }

        return HttpClientResponseDto::init($data);
    }

    public function request(HttpClientDtoInterface $dto): ?HttpClientResponseInterface
    {
        return $this->exec(
            $dto->getApiMethod(),
            $dto->getMethod(),
            $dto->getParams()
        );
    }
}
