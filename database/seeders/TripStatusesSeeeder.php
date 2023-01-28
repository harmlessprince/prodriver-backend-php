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
        $statuses = [
            'in-transit',
            'accident',
            'awaiting-offloading',
            'diverted',
            'delivered',
            'canceled'
        ];

        foreach ($statuses as $key => $status) {
            TripStatus::query()->updateOrCreate([
                'name' => $status,
            ]);
        }
    }
}
