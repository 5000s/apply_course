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

use App\Http\Controllers\AdminMemberController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseApplyController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmailTestController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\DashboardController;


Auth::routes();

Route::get('/', [PagesController::class, 'index'])->name('pages.index');

Route::get('/login', [AuthController::class, 'index'])->name('pages.login');

Route::get('/preregister', [AuthController::class, 'preregister'])->name('preregister');

Route::get('/request-access', [AuthController::class, 'formPreregister'])->name('request-access');

Route::post('/check-email', [AuthController::class, 'checkEmail'])->name('check-email');
Route::post('/request-password-reset', [AuthController::class, 'requestPasswordReset'])->name('request-password-reset');
Route::get('/request-password-reset', [AuthController::class, 'requestPasswordReset'])->name('request-password-reset');

Route::post('/post-login', [AuthController::class, 'login'])->name('pages.postlogin');

Route::get('/updatePhone', [AuthController::class, 'updatePhone']);

Route::get('/about', [PagesController::class, 'about'])->name('pages.about');


//student
Route::get('/course/list', [ApplyController::class, 'showCourseForStudent'])->name('showCourseForStudent');


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
    Route::get('courses/create', [CourseController::class, 'courseCreate'])->name("admin.courses.create");
    Route::get('courses/edit/{course_id}', [CourseController::class, 'courseEdit'])->name("admin.courses.edit");
    Route::post('courses/save', [CourseController::class, 'courseSave'])->name("admin.courses.save");

    Route::put('courses/update/{course_id}', [CourseController::class, 'courseUpdate'])->name("admin.courses.update");

    Route::get('courses/applylist/{course_id}', [CourseController::class, 'courseApplyList'])->name("admin.courseList");
    Route::get('courses/applylist/{course_id}/download', [CourseController::class, 'courseApplyListDownload'])->name('admin.applylist.download');
    Route::get('courses/applylist/{course_id}/pdf', [CourseController::class, 'courseApplyListPdfDownload'])->name('admin.applylist.totalform.pdf');
    Route::get('courses/applylist/{course_id}/zip', [CourseController::class, 'courseApplyListZipDownload'])->name('admin.applylist.totalform.zip');


    Route::get('courses/applylist/{course_id}/{apply_id}/form', [CourseController::class, 'viewForm'])->name("admin.courseApplyForm");

    Route::get('courses/applylist/{course_id}/{apply_id}/update/{status}', [CourseController::class, 'updateApplyStatus'])->name("admin.courseApplyStatus");


    Route::get('member', [AdminMemberController::class, 'profile'])->name("admin.members");
    Route::get('member/type', [AdminMemberController::class, 'getMemberType'])->name("admin.membersType");
    Route::get('member/course', [AdminMemberController::class, 'adminMemberCourse']);


    Route::get('gap/course/{course_id}', [CourseController::class, 'calculateGap']);

    Route::post('/apply/{apply_id}/remark', [ApplyController::class, 'updateRemark'])
        ->name('admin.apply.remark');



    // IMPORT GOOGLE SHEET

    Route::get('/sheet', [GoogleSheetController::class, 'showSheetButtons'])->name('admin.import.sheet.buttons');
    Route::get('/sheet/{index}', [GoogleSheetController::class, 'importSheetByIndex'])->name('admin.import.sheet.direct');
    Route::get('/sheet/member/{locationId}', [GoogleSheetController::class, 'showUnsyncedApplications'])->name('admin.applications.sync');
    Route::post('/sheet/member/{locationId}/sync', [GoogleSheetController::class, 'syncToMembers'])->name('admin.applications.sync.store');


    ##### LOGIN DO BY SRA
    Route::get('login', [UserController::class, 'login'])->name("adminLogin");
    Route::post('login', [UserController::class, 'login']);
    Route::get('logout', [UserController::class, 'logout']);



    Route::resource('teams', TeamController::class);

    // Team-Member CRUD (all in TeamController)
    Route::prefix('teams/{team}')->name('teammembers.')->group(function () {
        // List members
        Route::get('members', [TeamController::class, 'indexMember'])
            ->name('index');
        // Show “add member” form
        Route::get('members/create', [TeamController::class, 'createMember'])
            ->name('create');
        // Handle add
        Route::post('members', [TeamController::class, 'addMember'])
            ->name('store');
        // Show “edit member” form
        Route::get('members/{member}/edit', [TeamController::class, 'editMemberForm'])
            ->name('edit');
        // Handle edit
        Route::put('members/{member}', [TeamController::class, 'editMember'])
            ->name('update');
        // Handle delete
        Route::delete('members/{member}/delete', [TeamController::class, 'deleteMember'])
            ->name('destroy');
    });



    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/dashboard/course-types', [DashboardController::class, 'typesByLocation'])
        ->name('dashboard.course-types');
});





Auth::routes(['verify' => true]);


Route::middleware(['verified'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');

    // Route to show form for creating a new member
    Route::get('/member/create', [MemberController::class, 'create'])->name('member.create');

    // Route to store a new member
    Route::post('/member', [MemberController::class, 'store'])->name('member.store');
    Route::post('/memberadmin', [MemberController::class, 'storeAdmin'])->name('member.store.admin');

    // Route to show form for editing an existing member
    Route::get('/member/{member}/edit', [MemberController::class, 'edit'])->name('member.edit');

    // Route to update an existing member
    Route::put('/member/update', [MemberController::class, 'update'])->name('member.update');
    Route::put('/member/updateadmin', [MemberController::class, 'updateAdmin'])->name('member.update.admin');

    Route::get('apply/{member_id}/courses', [ApplyController::class, 'index'])->name('courses.index');

    Route::get('apply/{member_id}/courses/history', [ApplyController::class, 'memberApplyHistory'])->name('courses.history');


    Route::get('apply/{member_id}/courses/{course_id}', [ApplyController::class, 'show'])->name('courses.show');
    Route::post('apply/{member_id}/courses', [ApplyController::class, 'save'])->name('courses.save');
    Route::post('apply/{member_id}/courses/{apply}', [ApplyController::class, 'update'])->name('courses.update');

    Route::post('apply/{member_id}/courses/{apply}/cancel', [ApplyController::class, 'cancel'])->name('courses.cancel');

    Route::get('apply/{member_id}/courses/edit/{course_id}', [ApplyController::class, 'edit'])->name('courses.edit');
});



Route::get('/admin/members/similar', [GoogleSheetController::class, 'similar'])
    ->name('admin.members.similar');

Route::post('/admin/applications/{application}/link-member', [GoogleSheetController::class, 'linkMember'])
    ->name('admin.applications.linkMember');

Route::get('/send-test-email', [EmailTestController::class, 'sendTestEmail']);



Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'th'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');





Route::get('/setCourseData', [CourseController::class, 'setCourseData'])->name('admin.course.sync');



Route::get('/sms/send', [SmsController::class, 'form'])->name('sms.form');
Route::post('/sms/send', [SmsController::class, 'send'])->name('sms.send');






Route::middleware(['allow.iframe'])->group(function () {

    Route::get('/apply/table/{type}/{lang}', [CourseApplyController::class, 'courseTableWordpress'])->name('course.table');

    Route::get('/apply/direct', [CourseApplyController::class, 'directApply'])->name('apply.direct');
    // Route::post('/apply/direct/request-otp', [CourseApplyController::class, 'directRequestOtp'])->name('apply.direct.requestOtp');

    Route::post('/apply/direct/apply', [CourseApplyController::class, 'applyCourse'])->name('apply.direct.apply');
    Route::PUT('/apply/direct/confirm/{course_id}/{member_id}', [CourseApplyController::class, 'directConfirm'])->name('apply.form.confirm');
});

Route::get('/search/member', [CourseApplyController::class, 'searchMember'])->name('search.member');
