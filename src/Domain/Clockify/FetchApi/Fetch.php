<?php

namespace KarelBartunek\Clockify\Domain\Clockify\FetchApi;

use JDecool\Clockify\ApiFactory;
use KarelBartunek\Clockify\Domain\Clockify\ClockifyApiFactory;

abstract class Fetch
{
    protected ApiFactory $apiFactory;

    public function __construct(ClockifyApiFactory $clockifyApiFactory)
    {
        $this->apiFactory = $clockifyApiFactory();
    }
}