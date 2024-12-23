<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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
use App\Http\Controllers\DebugController;

Route::get('/search', 'RoomSearchController@index')->name('search.index');
Route::get('/search/rooms', 'RoomSearchController@search')->name('search.rooms');

if (App::environment('local')) {
    Route::get('/sync', 'DebugController@sync')->name('sync');
    Route::get('/roomtypes', 'DebugController@roomtypes')->name('roomtypes');
    Route::get('/reservations', 'DebugController@reservations')->name('reservations');
    Route::get('/activehotels', 'DebugController@activehotels')->name('activehotels');
    Route::get('/allhotels', 'DebugController@allhotels')->name('allhotels');
    Route::get('/regions', 'DebugController@regions')->name('regions');
    Route::get('/mainregions', 'DebugController@mainregions')->name('mainregions');
    Route::get('/subregions', 'DebugController@subregions')->name('subregions');
    Route::get('/currency', 'DebugController@currencyList')->name('currency');
    Route::get('/pricesearch', 'DebugController@priceSearch')->name('pricesearch');
    Route::get('/contracts', 'DebugController@getContracts')->name('contracts');
    Route::get('/packets', 'DebugController@getPackets')->name('packets');
}




