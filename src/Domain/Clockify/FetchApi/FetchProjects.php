<?php

namespace KarelBartunek\Clockify\Domain\Clockify\FetchApi;

use JDecool\Clockify\Model\ProjectDtoImpl;

final class FetchProjects extends Fetch
{
    /**
     * @return ProjectDtoImpl[]
     */
    public function __invoke(string $workspaceId): array
    {
        $projectApi = $this->apiFactory->projectApi();
        return $projectApi->projects($workspaceId);
    }
}