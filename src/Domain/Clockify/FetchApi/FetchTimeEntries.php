<?php

namespace KarelBartunek\Clockify\Domain\Clockify\FetchApi;

use DateTimeImmutable;
use JDecool\Clockify\Model\TimeEntryDtoImpl;

final class FetchTimeEntries extends Fetch
{
    /**
     * @param string $workspaceId
     * @param string $userId
     * @param DateTimeImmutable $from
     * @param DateTimeImmutable $to
     * @return TimeEntryDtoImpl[]
     */
    public function __invoke(string $workspaceId, string $userId, DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $params = [
            'start' => $from->format('Y-m-d\TH:i:s.u\Z'),
            'end' => $to->format('Y-m-d\TH:i:s.u\Z')
        ];

        $timeEntryApi = $this->apiFactory->timeEntryApi();
        return $timeEntryApi->find($workspaceId, $userId, $params);
    }
}