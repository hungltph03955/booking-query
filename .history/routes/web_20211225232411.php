<?php

use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    $check_in = '2021-12-23';
    $check_out = '2021-12-29';
    $city_id = 2;
    $room_size = 2;

    $result1 = Reservation::where(function($q) use($check_in, $check_out) {
        $q->where('check_in', '>', $check_in);
        $q->where('check_in', '>=', $check_out);
    })->orWhere(function($q) use($check_in, $check_out) {
        $q->where('check_out', '<=', $check_out);
        $q->where('check_out', '<', $check_out);
    })->get();

    // dump($result1);

    $check_in = '2021-12-23';
    $check_out = '2021-12-29';

    $result2 = DB::table('rooms')
    ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
    ->join('hotels', 'rooms.hotel_id', '=', 'hotels.id')
    ->whereNotExists(function($query) use($check_in, $check_out) {
        $query->select('reservations.id')
        ->from('reservations')
        ->join('reservation_room', 'reservations.id', '=', 'reservation_room.reservation_id')
        // ->whereRaw('rooms.id = reservation_room.room_id')
        ->whereColumn('rooms.id', 'reservation_room.room_id')
        ->where(function ($q) use ($check_in, $check_out) {
            $q->where('check_out', '>', $check_in);
            $q->where('check_in', '<', $check_out);
        })->limit(1);
    })
    ->whereExists(function($q) use($city_id) {
        $q->select('hotels.id')
        ->from('hotels')
        ->whereColumn('rooms.hotel_id', 'hotels.id')
        ->whereExists(function($q) use($city_id) {
            $q->select('cities.id')
            ->from('cities')
            ->whereColumn('cities.id', 'hotels.city_id')
            ->where('id', $city_id)
            ->limit(1);
        })->limit(1);
    })
    ->where('room_types.amount', '>', 0)
    ->where('room_types.size', '=', $room_size)
    ->orderBy('room_types.price', 'asc')
    ->paginate(10);

    dump($result2);

    // $result3 = Room::with('type')->whereDoesntHave('reservations', function($q) use ($check_in, $check_out) {
    //     $q->where('check_out', '>', $check_in);
    //     $q->where('check_in', '<', $check_out);
    // })->get();

    // dump($result3);

    $result4 = Room::with(['type', 'hotel'])
    ->whereDoesntHave('reservations', function($q) use($check_in, $check_out) {
        $q->where('check_out', '>', $check_in);
        $q->where('check_in', '<', $check_out);
    })->whereHas('hotel.city', function($q) use($city_id) {
        $q->where('id', $city_id);
    })
    ->whereHas('type', function($q) use($room_size){
        $q->where('amount', '>', 0);
        $q->where('size', '=', $room_size);
    })
    ->paginate(10)
    ->sortBy('type.price');
    // dump($result4);

    // $a = DB::statement('ALTER TABLE reservation_room ADD INDEX reservation_id_index (reservation_id)');
    // $b = DB::statement('ALTER TABLE reservation_room ADD INDEX room_id_index (room_id)');
    // $c = DB::statement('ALTER TABLE rooms ADD INDEX room_type_id_index (room_type_id)');
    // $d = DB::statement('ALTER TABLE room_types ADD INDEX size_index (size)');
    // $d = DB::statement('ALTER TABLE hotels ADD INDEX city_id_index (city_id)');
    // $d = DB::statement('ALTER TABLE cities ADD INDEX country_id_index (country_id)');




    return view('welcome');
});
