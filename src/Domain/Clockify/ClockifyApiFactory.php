<?php

namespace KarelBartunek\Clockify\Domain\Clockify;

use JDecool\Clockify\ApiFactory;
use JDecool\Clockify\Client;
use JDecool\Clockify\ClientBuilder;

final class ClockifyApiFactory
{
    private Client $client;

    public function __construct(string $clockifyApiKey)
    {
        $this->client = (new ClientBuilder())->createClientV1($clockifyApiKey);
    }

    public function __invoke()
    {
        return new ApiFactory($this->client);
    }
}