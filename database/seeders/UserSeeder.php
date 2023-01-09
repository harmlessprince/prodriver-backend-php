<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->updateOrCreate(
            ['email' => 'truck@test.com'],
            [
                'first_name' => 'Truck',
                'last_name' => 'User',
                'phone_number' => '09086442311',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_TRANSPORTER,
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'cargo@test.com'],
            [
                'first_name' => 'Cargo',
                'last_name' => 'User',
                'phone_number' => '09086442315',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_CARGO_OWNER,
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '09086442314',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ADMIN,
                'email_verified_at' => now(),
            ]
        );
    }
}
