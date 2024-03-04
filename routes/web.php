<?php

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

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserController;


Auth::routes();

Route::get('/', [PagesController::class, 'index'])->name('pages.index');

Route::get('/login', [AuthController::class, 'index'])->name('pages.login');
Route::post('/post-login', [AuthController::class, 'login'])->name('pages.postlogin');
Route::get('/updatePhone', [AuthController::class, 'updatePhone']);

Route::get('/about', [PagesController::class, 'about'])->name('pages.about');

// Course

Route::get('/course/{location}/detail', [CourseController::class, 'locationCourse']);
Route::get('/course/{course}/register', [CourseController::class, 'courseRegister']);
Route::post('/member/search', [CourseController::class, 'memberSearch']);
Route::post('/course/apply', [CourseController::class, 'courseApply']);
Route::post('/course/confirm', [CourseController::class, 'courseConfirm']);
Route::post('/course/apply/finish', [CourseController::class, 'courseApplyFinish']);

Route::get('userform/{time}/{member_id}/{filename}', [CourseController::class, 'getApplication']);
Route::get('profile/course', [CourseController::class, 'userCourse']);


Route::get('admin/member', [CourseController::class, 'adminSearchMember']);
Route::get('admin/member/course', [CourseController::class, 'adminMemberCourse']);


Route::get('admin/login', [UserController::class, 'login'] )->name("adminLogin");
Route::post('admin/login', [UserController::class, 'login']);
Route::get('admin/logout', [UserController::class, 'logout'] );




Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
