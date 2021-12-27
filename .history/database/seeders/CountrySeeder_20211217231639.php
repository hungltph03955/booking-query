<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::factory()->count(50)->create()->each(function($country) {
            $country->cities()->saveMany(City::factory()->make());
        })
    }
}
