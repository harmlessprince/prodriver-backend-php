<?php

namespace Database\Seeders;

use App\Models\Tonnage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TonnageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $step = 4;
        $initialValue = 1;
        $nextValue = 5;
        $total = 100;
        $tonnage = '';
        while ($nextValue <= 100) {
            $tonnage = $initialValue . '-' . $nextValue . ' ton';
            Tonnage::query()->updateOrCreate(['name' => $tonnage]);
            $initialValue = $nextValue;
            $nextValue += 5;
        }
    }
}
