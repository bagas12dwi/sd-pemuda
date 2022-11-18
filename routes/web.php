<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use hisorange\BrowserDetect\Parser as Browser;


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

Route::get('/', [HomeController::class, 'index']);

//Route auth
Route::get('/login', [AuthController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/ip', function () {
    $client = new Client(); //GuzzleHttp\Client
    $url = "https://geolocation-db.com/json/geoip.php";


    $response = $client->request('GET', $url, [
        'verify'  => false,
    ]);

    $responseBody = json_decode($response->getBody());
    dd($responseBody->country_name);
});


//Route admin
Route::group(['middleware' => ['auth', 'ceklevel:admin']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});
