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

use App\Http\Controllers\ApplyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailTestController;
use Illuminate\Support\Facades\Auth;


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

Route::prefix('admin')->middleware('auth', 'admin')->group(function () {
    Route::get('courses', [CourseController::class, 'courseList'])->name("admin.courses");
    Route::get('courses/applylist/{course_id}', [CourseController::class, 'courseApplyList'])->name("admin.courseList");
    Route::get('courses/applylist/{course_id}/download', [CourseController::class, 'courseApplyListDownload'])->name('admin.applylist.download');
    Route::get('courses/applylist/{course_id}/pdf', [CourseController::class, 'courseApplyListPdfDownload'])->name('admin.applylist.totalform.pdf');
    Route::get('courses/applylist/{course_id}/zip', [CourseController::class, 'courseApplyListZipDownload'])->name('admin.applylist.totalform.zip');


    Route::get('courses/applylist/{course_id}/{apply_id}/form', [CourseController::class, 'viewForm'])->name("admin.courseApplyForm");

    Route::get('courses/applylist/{course_id}/{apply_id}/update/{status}', [CourseController::class, 'updateApplyStatus'])->name("admin.courseApplyStatus");


    Route::get('member', [CourseController::class, 'adminSearchMember']);
    Route::get('member/course', [CourseController::class, 'adminMemberCourse']);

##### LOGIN DO BY SRA
    Route::get('login', [UserController::class, 'login'] )->name("adminLogin");
    Route::post('login', [UserController::class, 'login']);
    Route::get('logout', [UserController::class, 'logout'] );
});





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
    Route::put('/member/update', [MemberController::class, 'update'])->name('member.update');

    Route::get('apply/{member_id}/courses', [ApplyController::class, 'index'])->name('courses.index');
    Route::get('apply/{member_id}/courses/{course_id}', [ApplyController::class, 'show'])->name('courses.show');
    Route::post('apply/{member_id}/courses', [ApplyController::class, 'save'] )->name('courses.save');
    Route::get('apply/{member_id}/courses/edit/{course_id}', [ApplyController::class, 'edit'] )->name('courses.edit');
    Route::post('apply/{member_id}/courses/cancel/{course_id}', [ApplyController::class, 'cancel'] )->name('courses.cancel');

});


Route::get('/send-test-email', [EmailTestController::class, 'sendTestEmail'] );



