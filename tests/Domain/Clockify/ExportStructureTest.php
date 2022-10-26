<?php

namespace Tests\Domain\Clockify;

use DateTimeImmutable;
use KarelBartunek\Clockify\Domain\Clockify\ExportStructure;
use PHPUnit\Framework\TestCase;

class ExportStructureTest extends TestCase
{
    public function testStructureExport()
    {
        $from = new DateTimeImmutable('2022-10-25 midnight');

        $export = new ExportStructure();
        $fileContent = $export(records: [], from: $from, hasAlreadyExportedRecordsInDateRange: false);

        $this->assertEquals("Time entries for Tuesday (2022-10-25): 0 \r\n", $fileContent);
    }
}
