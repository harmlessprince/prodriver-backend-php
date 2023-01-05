<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $countries = json_decode(file_get_contents('public/countriesandstates.json'));
        foreach ($countries as $country) {
            $states = $country->states;
            $createdCountry = Country::query()->updateOrCreate(
                [
                    'name' => $country->name,
                    'phone_code' => $country->phone_code
                ],
                [
                    'region' => $country->region,
                    'capital' => $country->capital,
                    'flag' => $country->emoji,
                ]
            );

            foreach ($states as $state) {
                State::query()->updateOrCreate(
                    [
                        'country_id' => $createdCountry->id,
                        'name' => $state->name],
                    [
                        'state_code' => $state->state_code,
                    ]
                );
            }

        }
    }
}
