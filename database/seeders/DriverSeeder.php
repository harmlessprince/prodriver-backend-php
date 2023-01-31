<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        Driver::query()->truncate();
        $users = User::query()->where('user_type', User::USER_TYPE_TRANSPORTER)->doesntHave('drivers')->get();
        foreach ($users as $user) {
            $drivers = Driver::factory(rand(3, 5))->create([
                'user_id' => $user->id,
            ]);
            $user->drivers()->saveMany($drivers);
        }
    }
}
