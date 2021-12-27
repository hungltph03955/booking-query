<?php

use Illuminate\Support\Facades\Route;
use App\Models\Reservation;
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
    $result1 = Reservation::where(function($q) use($check_in, $check_out) {
        $q->where('check_in', '>', $check_in);
        $q->where('check_in', '>=', $check_out);
    })->orWhere(function($q) use($check_in, $check_out) {
        $q->where('check_out', '<=', $check_out);
        $q->where('check_out', '<', $check_out);
    })->get();

    // dump($result1);

    $result2 = DB::table('rooms')->whereNotExists(function($query) use($check_in, $check_out) {
        $query->select('reservations.id')
        ->from('reservations')
        ->where(function ($q) use ($check_in, $check_out) {
            $q->where('check_out', '>', $check_in);
        });
    });

    dump($result2);


    return view('welcome');
});
