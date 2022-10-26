<?php

namespace KarelBartunek\Clockify\Domain\Clockify;

use DateTimeImmutable;
use KarelBartunek\Clockify\Infrastructure\Repository\RecordRepository;

final class Export
{
    private RecordRepository $recordRepository;

    public function __construct(RecordRepository $recordRepository)
    {
        $this->recordRepository = $recordRepository;
    }

    public function __invoke(DateTimeImmutable $from, DateTimeImmutable $to): string
    {
        $hasAlreadyExportedRecordsInDateRange = $this->hasAlreadyExportedRecordsInDateRange($from, $to);
        $records = $this->getRecords($from, $to);

        $exportStructure = new ExportStructure();
        $content = $exportStructure($records, $from, $hasAlreadyExportedRecordsInDateRange);

        $this->markExportedRecords($from, $to);

        return $content;
    }

    private function hasAlreadyExportedRecordsInDateRange(DateTimeImmutable $from, DateTimeImmutable $to): bool
    {
        $count = $this->recordRepository->getCountExportedInDateRange($from, $to);
        return filter_var($count, FILTER_VALIDATE_BOOLEAN);
    }

    private function getRecords(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        return $this->recordRepository->findNotExportedRecords($from, $to);
    }

    private function markExportedRecords(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return $this->recordRepository->markExportedRecords($from, $to);
    }
}