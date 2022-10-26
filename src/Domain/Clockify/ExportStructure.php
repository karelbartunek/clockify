<?php

namespace KarelBartunek\Clockify\Domain\Clockify;

use DateTimeImmutable;

class ExportStructure
{
    public function __invoke(
        array $records,
        DateTimeImmutable $from,
        bool $hasAlreadyExportedRecordsInDateRange = false
    ): string {
        $content = '';

        if (!$hasAlreadyExportedRecordsInDateRange) {
            $content .= $this->buildHeader($from, count($records));
        }

        if (count($records) > 0) {
            $content .= $this->buildRecords($records);
        }

        return $content;
    }

    private function buildHeader(DateTimeImmutable $dateTime, int $recordsCount): string
    {
        $dayOfWeek = $dateTime->format('l');
        $date = $dateTime->format('Y-m-d');

        return "Time entries for {$dayOfWeek} ({$date}): {$recordsCount} \r\n";
    }

    private function buildRecords(array $records): string
    {
        $content = '';

        foreach ($records as $record) {
            $content .= $this->buildRecord($record);
        }

        return $content;
    }

    /**
     * @todo Rozdelit nejak ${Surname} ${Name} uz na importu z Clockify
     */
    private function buildRecord(array $record): string
    {
        $surname = $record['lastName'];
        $name = $record['firstName'];
        $timeEntriesSum = self::timeFormat($record['duration']);
        $meetingsStatus = filter_var($record['isStandup'], FILTER_VALIDATE_BOOL) ? 'OK' : 'WRONG';

        return "{$surname} ${name}: {$timeEntriesSum}, meetings {$meetingsStatus}" . "\r\n";
    }

    /**
     * @todo pozor na dlouhy smeny +24h
     */
    private static function timeFormat($seconds): string
    {
        return gmdate("H \h\o\d i \m\i\\n", $seconds);
    }
}