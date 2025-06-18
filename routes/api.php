<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/members/search', function (\Illuminate\Http\Request $request) {
    $q = $request->get('q');
    return \App\Models\Member::where('id', $q)
        ->orWhere('name', 'like', "%$q%")
        ->orWhere('surname', 'like', "%$q%")
        ->limit(10)
        ->get(['id', 'name', 'surname']);
});
