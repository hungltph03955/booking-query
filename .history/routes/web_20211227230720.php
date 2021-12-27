<?php

use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Hotel;
use App\Models\City;
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
            $q->where('room_types.amount', '=', 0);
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

    // dump($result2);

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

    $room_id = 29;
    $user_id = 1;

    // DB::transaction(function() use($room_id, $user_id, $check_in, $check_out){
    //     $room = Room::findOrFail($room_id);
    //     $reservation = new Reservation;
    //     $reservation->user_id = $user_id;
    //     $reservation->check_in = $check_in;
    //     $reservation->check_out = $check_out;
    //     $reservation->price = $room->type->price;
    //     $reservation->save();
    //     $room->reservations()->attach($reservation->id);
    //     RoomType::where('id', $room->room_type_id)->where('amount' , '>', 0)->decrement('amount');
    // });

    $user_id = 1;
    $result5 = Reservation::with(['rooms.type', 'rooms.hotel'])
    ->where('user_id', $user_id)
    ->first();
    // ->get();
    // dump($result5);

    $hotel_id = [1];
    $result6 = Reservation::with(['rooms.type', 'user'])
        ->select('reservations.*', DB::raw('DATEDIFF(check_out, check_in) as nights'))
        ->whereHas('rooms.hotel', function($q) use($hotel_id) {
            $q->whereIn('hotel_id', $hotel_id);
        })->orderBy('nights', 'DESC')
        ->get();

    // dump($result6);

    $result7 = Room::whereHas('hotel', function($q) use($hotel_id){
        $q->whereIn('hotel_id', $hotel_id);
    })
    ->withCount('reservations')
    ->orderBy('reservations_count', 'DESC')
    ->get()
    ;

    // dump($result7);

    $hotel_id = range(1, 10);
    $result8 = Hotel::whereIn('id', $hotel_id)
    ->withCount('rooms')
    ->orderBy('rooms_count', 'desc')
    ->get();
    // dump($result8);

    $result9 = DB::table('rooms')
        ->join('room_types', 'rooms.room_type_id', '=', 'room_types.id')
        ->selectRaw('sum(room_types.amount) as number_of_single_rooms, rooms.name')
        ->groupBy('rooms.name', 'room_types.size')
        ->having('room_types.size', '=', 1)
        ->whereIn('rooms.hotel_id', $hotel_id)
        ->orderBy('number_of_single_rooms', 'desc')
        ->get();
    ;

    // dump($result9);

    $result10 = DB::table('users')->orderByDesc(
        DB::table('reservations')
        ->select('price')
        ->whereColumn('users.id', 'reservations.user_id')
        ->orderByDesc('price')
        ->limit(1)
    )->get();

    // dump($result10);

    // $city = City::find(1);
    // $hotel = new Hotel;
    // $hotel->name = 'hotel name';
    // $hotel->description = 'hotel description';
    // $hotel->city()->associate($city);
    // $result11 = $hotel->save();
    // dump($result11);

    // $hotel = Hotel::find(1);
    // $room_type = new RoomType();
    // $room_type->size = 2;
    // $room_type->price = 200;
    // $room_type->amount = 2;
    // $room_type->save();

    // $room = new Room;
    // $room->name = 'hotel';
    // $room->description = 'hotel description';
    // $room->type()->associate($room_type);
    // $result12 = $hotel->rooms()->save($room);
    // dump($result12);

    $room = Room::find(1);
    $room->name = 'new name';
    $result = $room->save();
    dump($result);

    return view('welcome');
});
