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
        $tonnages = [
            '3 TONS',
            '5 TONS',
            '10 TONS',
            '14 TONS',
            '15 TONS',
            '20 TONS',
            '25 TONS',
            '28 TONS',
            '30 TONS',
            '35 TONS',
            '40 TONS',
            '45 TONS',
            '50 TONS',
            '55 TONS',
            '60 TONS',
        ];
        foreach ($tonnages as $key => $value) {
            Tonnage::query()->updateOrCreate(['name' => $value]);
        }
        // while ($nextValue <= 100) {
        //     $tonnage = $initialValue . '-' . $nextValue . ' ton';
        //     Tonnage::query()->updateOrCreate(['name' => $tonnage]);
        //     $initialValue = $nextValue;
        //     $nextValue += 5;
        // }
    }
}
