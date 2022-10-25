<?php

namespace KarelBartunek\Clockify\Domain\Clockify\FetchApi;

use JDecool\Clockify\Model\UserDto;

final class FetchUsers extends Fetch
{
    /**
     * @param string $workspaceId
     * @return UserDto[]
     */
    public function __invoke(string $workspaceId): array
    {
        $userApi = $this->apiFactory->userApi();
        return $userApi->workspaceUsers($workspaceId);
    }
}