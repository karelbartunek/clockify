<?php

namespace Tests\Domain\Clockify;

use DateTimeImmutable;
use KarelBartunek\Clockify\Domain\Clockify\Export;
use KarelBartunek\Clockify\Infrastructure\Repository\RecordRepository;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ExportTest extends TestCase
{
    /** @dataProvider provideExportData */
    public function testExport($countExportedInDateRange, $notExportedRecords, $markExportedRecords, $expectedValue)
    {
        $from = new DateTimeImmutable('2022-10-25 midnight');
        $to = new DateTimeImmutable('2022-10-26 midnight');

        $export = new Export(
            m::mock(RecordRepository::class, [
                'getCountExportedInDateRange' => $countExportedInDateRange,
                'findNotExportedRecords' => $notExportedRecords,
                'markExportedRecords' => $markExportedRecords
            ])
        );

        $fileContent = $export($from, $to);

        $this->assertEquals($expectedValue, $fileContent);
    }

    private function provideExportData(): array
    {
        return [
            'empty records' => [
                'getCountExportedInDateRange' => 0,
                'findNotExportedRecords' => [],
                'markExportedRecords' => 0,
                'expectedValue' => "Time entries for Tuesday (2022-10-25): 0 \r\n"
            ]
        ];
    }
}
