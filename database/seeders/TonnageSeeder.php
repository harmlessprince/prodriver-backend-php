<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        $tonnages = [];
        while ($nextValue <= 100) {
            $tonnages[] = $initialValue . '-' . $nextValue . ' ton';
            $initialValue = $nextValue;
            $nextValue += 5;
        }
//        dd($tonnages);
    }
}
