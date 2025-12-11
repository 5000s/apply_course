<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Member;

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

    $keywords = explode(' ', $q);

    $name = $keywords[0];
    $surname = "";

    if (count($keywords) > 1) {
        $surname = $keywords[1];
    }

    $query = Member::query();

    $query->where("surname", "!=", "");
    $query->where("name", "!=", "");

    if (count($keywords) == 1) {
        $query->where('name', 'like', "%$name%")->where('surname', 'like', "%$surname%");
    } else if (count($keywords) > 1) {
        $query->where('name', 'like', "%$name%")->where('surname', 'like', "%$surname%");
    }

    $members = $query->limit(10)->get();

    // Map to include age from the accessor if it's not automatically appended
    $members->each(function ($member) {
        $member->age = $member->birthdate?->age ?? '-';
    });

    return $members;
});
