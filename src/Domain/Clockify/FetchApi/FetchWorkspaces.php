<?php

namespace KarelBartunek\Clockify\Domain\Clockify\FetchApi;

use JDecool\Clockify\Model\WorkspaceDto;

final class FetchWorkspaces extends Fetch
{
    /**
     * @return WorkspaceDto[]
     */
    public function __invoke(): array
    {
        $workspaceApi = $this->apiFactory->workspaceApi();
        return $workspaceApi->workspaces();
    }
}