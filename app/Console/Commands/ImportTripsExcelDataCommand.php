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
        $path = $this->argument('file');
        $files = scandir($path);
        sort($files, SORT_NATURAL);

        foreach ($files as $file) {
            // Check if the file is an Excel file
            if (pathinfo($file, PATHINFO_EXTENSION) == 'xlsx' || pathinfo($file, PATHINFO_EXTENSION) == 'xls') {

                $this->output->title('Starting import ' . basename($file));
                $import = new TripsImport();
                $import->onlySheets('DATABASE');
                Excel::queueImport($import, $path . '/' . $file);
                $this->output->success('Import successful');
            }
        }
    }
}
