<?php

namespace Database\Seeders;

use App\Models\User;
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
        $transporter = User::query()->updateOrCreate(
            ['email' => 'truck@test.com'],
            [
                'first_name' => 'Truck',
                'last_name' => 'User',
                'phone_number' => '09086442311',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_TRANSPORTER,
                'email_verified_at' => now(),
            ]
        );
        $driver =  $transporter->drivers()->create([
            'first_name' => 'Anonymous',
            'last_name' => 'Driver',
            'phone_number' => 12345678910
        ]);

        $transporter->trucks()->create([
            'driver_id' => $driver->id,
            'on_trip' => true,
        ]);

        User::query()->updateOrCreate(
            ['email' => 'cargo@test.com'],
            [
                'first_name' => 'Cargo',
                'last_name' => 'User',
                'phone_number' => '09086442312',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_CARGO_OWNER,
                'email_verified_at' => now(),
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'phone_number' => '09086442313',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ADMIN,
                'email_verified_at' => now(),
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'account@test.com'],
            [
                'first_name' => 'Account',
                'middle_name' => 'Manager',
                'last_name' => 'User',
                'phone_number' => '09086442314',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'victor.e@accountmanager.com'],
            [
                'first_name' => 'Victor',
                'last_name' => 'Evboifo',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );
        User::query()->updateOrCreate(
            ['email' => 'tunde.a@accountmanager.com'],
            [
                'first_name' => 'Tunde',
                'last_name' => 'Adebayo',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'maximus.m@accountmanager.com'],
            [
                'first_name' => 'Maximus',
                'last_name' => 'Maxi',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'chidebere.u@accountmanager.com'],
            [
                'first_name' => 'Chidebere',
                'last_name' => 'Ukoha',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'johnson.o@accountmanager.com'],
            [
                'first_name' => 'Johnson',
                'last_name' => 'Ojabulu',
                'password' => Hash::make('password'),
                'user_type' => User::USER_TYPE_ACCOUNT_MANAGER,
                'email_verified_at' => now(),
            ]
        );
        // if (User::query()->count() <  10) {
        //     \App\Models\User::factory(10)->create();
        // }
    }
}
