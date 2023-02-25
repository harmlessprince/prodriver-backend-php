<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Tonnage;
use App\Models\Truck;
use App\Models\TruckType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (Truck::query()->count() < 20){
            $drivers = Driver::query()->doesntHave('truck')->get();
            $transPorters = User::query()->where('user_type', User::USER_TYPE_TRANSPORTER)->doesntHave('trucks')->get()->pluck('id');
            foreach ($drivers as $driver){
                $truck = Truck::factory()->create([
                    'transporter_id' => $driver->user_id,
                    'truck_type_id' => TruckType::query()->inRandomOrder()->first()->id,
                    'tonnage_id' => Tonnage::query()->get()->random(1)[0],
                ]);
                $driver->truck()->save($truck);
            }
        }

    }
}
