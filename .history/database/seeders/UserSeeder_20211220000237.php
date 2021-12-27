<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Reservation;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(50)->create()->each(function($user) {
            $reservations = $user->reservations()->saveMany(Reservation::factory(mt_rand(1, 20))->make());
            foreach ($reservations as $reservation) {
                $room_ids = [];
                for ($i = 1; $i = mt_rand(1, 3); $i++) {
                    array_push($room_ids, mt_rand(1, 3));
                }
                $reservation->rooms()->attach($room_ids, ['status' => (bool)random_int(0,1)]);
            }
        });
    }
}
