<?php

namespace PagerdutySlackUnfurl;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PagerdutyClient
{
    /** @var HttpClientInterface */
    private $client;

    public function __construct(
        HttpClientInterface $client,
        string $token
    ) {
        $this->client = $client->withOptions([
            'base_uri' => 'https://api.pagerduty.com',
            'headers' => [
                'Accept' => 'application/vnd.pagerduty+json;version=2',
                'Authorization' => sprintf('Token token=%s', $token),
            ],
        ]);
    }

    /**
     * @see https://developer.pagerduty.com/api-reference/b3A6Mjc0ODE0MQ-get-an-incident
     */
    public function getIncident(string $id): array
    {
        $url = sprintf('incidents/%s', $id);
        $response = $this->client->request('GET', $url);

        return $response->toArray();
    }
}
