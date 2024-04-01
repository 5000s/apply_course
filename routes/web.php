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
use App\Http\Controllers\EmailTestController;


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

#### MEMBER ADMIN
Route::get('admin/courses', [CourseController::class, 'courseList'])->name("admin.courses");
Route::get('admin/courses/applylist', [CourseController::class, 'courseApplyList']);
// Route::post('admin/courses', [CourseController::class, 'courseList']);


Route::get('admin/member', [CourseController::class, 'adminSearchMember']);
Route::get('admin/member/course', [CourseController::class, 'adminMemberCourse']);

##### LOGIN DO BY SRA
Route::get('admin/login', [UserController::class, 'login'] )->name("adminLogin");
Route::post('admin/login', [UserController::class, 'login']);
Route::get('admin/logout', [UserController::class, 'logout'] );




Auth::routes(['verify' => true]);


Route::middleware(['verified'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');

// Route to show form for creating a new member
    Route::get('/member/create', [MemberController::class, 'create'])->name('member.create');

// Route to store a new member
    Route::post('/member', [MemberController::class, 'store'])->name('member.store');

// Route to show form for editing an existing member
    Route::get('/member/{member}/edit', [MemberController::class, 'edit'])->name('member.edit');

// Route to update an existing member
    Route::put('/member/{member}', [MemberController::class, 'update'])->name('member.update');

});


Route::get('/send-test-email', [EmailTestController::class, 'sendTestEmail'] );



