<?php

namespace Database\Seeders;

use App\Models\TripStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripStatusesSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = TripStatus::STATUSES;
        

        foreach ($statuses as $key => $status) {
            TripStatus::query()->updateOrCreate([
                'name' => $status,
            ]);
        }
    }
}
