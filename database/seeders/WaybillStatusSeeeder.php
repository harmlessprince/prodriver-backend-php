<?php

namespace Database\Seeders;

use App\Models\WaybillStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WaybillStatusSeeeder extends Seeder
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
            'received',
            'pending',
            'invoiced',
        ];

        foreach ($statuses as $key => $status) {
            WaybillStatus::query()->updateOrCreate([
                'name' => $status,
            ]);
        }
    }
}
