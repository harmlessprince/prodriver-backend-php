<?php

namespace App\Console\Commands;

use App\Imports\TripsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportTripsExcelDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:trips {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import trips from passed excel file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');

        $import = new TripsImport();
        $import->onlySheets('DATABASE');
        Excel::queueImport($import, $file);
        $this->info('Data imported successfully.');
    }
}
