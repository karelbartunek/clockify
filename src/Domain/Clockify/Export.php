<?php

namespace KarelBartunek\Clockify\Domain\Clockify;

use DateTimeImmutable;
use KarelBartunek\Clockify\Infrastructure\Repository\RecordRepository;

final class Export
{
    protected const FILENAME = 'results.txt';

    private RecordRepository $recordRepository;

    public function __construct(RecordRepository $recordRepository)
    {
        $this->recordRepository = $recordRepository;
    }

    public function __invoke(DateTimeImmutable $from, DateTimeImmutable $to): void
    {
        $content = $this->build($from, $to);

        file_put_contents(__DIR__ . '/../../../var/' . self::FILENAME, $content, FILE_APPEND);

        $this->markExportedRecords($from, $to);
    }

    private function build(DateTimeImmutable $from, DateTimeImmutable $to): string
    {
        $hasAlreadyExportedRecordsInDateRange = $this->hasAlreadyExportedRecordsInDateRange($from, $to);

        $records = $this->getRecords($from, $to);

        $content = '';

        if (!$hasAlreadyExportedRecordsInDateRange) {
            $content .= $this->buildHeader($from, count($records));
        }

        $content .= $this->buildBody($records);

        return $content;
    }

    private function buildHeader(DateTimeImmutable $dateTime, int $recordsCount): string
    {
        $dayOfWeek = $dateTime->format('l');
        $date =  $dateTime->format('Y-m-d');

        return "Time entries for {$dayOfWeek} ({$date}): {$recordsCount} \r\n";
    }

    /**
     * @todo Rozdelit nejak ${Surname} ${Name} uz na importu z Clockify
     */
    private function buildBody(array $records): string
    {
        $body = '';

        foreach ($records as $record) {
            $surname = $record['lastName'];
            $name = $record['firstName'];
            $timeEntriesSum = self::timeFormat($record['duration']);
            $meetingsStatus = filter_var($record['isStandup'], FILTER_VALIDATE_BOOL) ? 'OK' : 'WRONG';
            $body .= "{$surname} ${name}: {$timeEntriesSum}, meetings {$meetingsStatus}  \r\n";
        }

        return $body;
    }

    /**
     * @todo pozor na dlouhy smeny +24h
     */
    private static function timeFormat($seconds): string
    {
        return gmdate("H \h\o\d i \m\i\\n", $seconds);
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

    /**
     * @todo hloupe oznaceni
     */
    private function markExportedRecords(DateTimeImmutable $from, DateTimeImmutable $to): int
    {
        return $this->recordRepository->markExportedRecords($from, $to);
    }
}