<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;

if (!defined('CONTROLLERS')) define('CONTROLLERS', 'App\\Http\\Controllers\\');
if (!defined('PANEL_CONTROLLER')) define('PANEL_CONTROLLER', CONTROLLERS .'Panel\\');

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('/tenants/index', function () {
	return 'OK';
});

Route::post('register-demo-user', [DemoController::class, 'registerDemoUser']);

Route::group(['prefix' => 'tenants', 'middleware' => 'auth:sanctum'], function () {
	// ...
});
