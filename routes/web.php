<?php
use Illuminate\Support\Facades\Artisan;
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

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/migrate', function(){
    Artisan::call('migrate',[
        '--force' => true
     ]);
    echo "migrated";
});
Route::get('/', [PageController::class, 'home'])->middleware('referrer');
Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');
Route::get('/{path}', [PageController::class, 'index'])->where('path', '.*')->middleware('referrer');

