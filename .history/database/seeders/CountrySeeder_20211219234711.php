<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Room;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $Countries = Country::factory()->count(20)->create()->each(function($country) {
            $country->cities()->saveMany(City::factory(mt_rand(1, 20))->make());
        });

        foreach ($Countries as $country) {
            foreach($country->cities as $city) {
                $hotels = $city->hotels()->SaveMany(Hotel::factory(mt_rand(1, 10))->make());
                foreach ($hotels as $hotel) {
                    $hotel->rooms()->saveMany(Room::factory(mt_rand(3,20)))->make();
                }
            }
        }
    }
}
