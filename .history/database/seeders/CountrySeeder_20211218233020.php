<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Countries = Country::factory()->count(50)->create()->each(function($country) {
            $country->cities()->saveMany(City::factory(mt_rand(1, 50))->make());
        });

        foreach ($Countries as $country) {
            foreach($country->cities as $city) {
                $hotels = $city->hotels()->SaveMany(Hotel::factory(mt_rand(1, 20))->make());

                foreach ($hotels as $hotel) {

                }
            }
        }
    }
}
