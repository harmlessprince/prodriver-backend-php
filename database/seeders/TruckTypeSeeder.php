<?php

namespace Database\Seeders;

use App\Models\TruckType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TruckTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types  = [
            'Flat body',
            'Tipper',
            'Boxed Body',
            'Regular Truck',
            'Low Bed',
            'Vans'
        ];
        foreach ($types as $type){
            TruckType::query()->updateOrCreate(['name' => $type]);
        }
    }
}
