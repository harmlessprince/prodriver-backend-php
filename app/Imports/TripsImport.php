<?php

namespace App\Imports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;
use Maatwebsite\Excel\Concerns\WithCustomChunkSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TripsImport implements WithMultipleSheets, SkipsUnknownSheets, ShouldQueue, WithChunkReading
{
    use WithConditionalSheets;
    /**
     * @param Collection $collection
     */

    public function conditionalSheets(): array
    {
        return [
            'DATABASE' => new TripsDatabaseSheet(),
        ];
    }

    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
    public function chunkSize(): int
    {
        return 500;
    }
}
