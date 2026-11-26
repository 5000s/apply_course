<?php

namespace App\Console;

use App\Http\Controllers\CourseController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\SmsController;
use App\Services\CourseAutoConfirmService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        $schedule->call(function () {
            // ถ้าไม่มี dependency พิเศษ ใช้แบบนี้ได้เลย
            (new CourseController)->setCourseData();
        })->hourly();

        $schedule->call(function () {
            (new CronController)->checkAndCancelPastApplications();
        })->daily();

        $schedule->call(function () {
            (new CronController)->closeCourseAuto();
        })->daily();

        $schedule->call(function () {
            (new CronController)->updateMemberStatus();
        })->daily();

        // ตัดรอบ 7 วันก่อนเริ่มคอร์ส: ปิดรับสมัคร, ยืนยันอัตโนมัติจนเต็ม, ยกเลิกส่วนที่เกิน + แจ้งอีเมล/SMS
        // (หน้าเว็บปิดรับสมัครตั้งแต่ 00:00 ของวันตัดรอบอยู่แล้ว เวลานี้จึงกำหนดแค่ช่วงที่ส่งแจ้งเตือน)
        $schedule->call(function () {
            app(CourseAutoConfirmService::class)->runCutoff();
        })->dailyAt('08:30');

        // SMS + อีเมล เตือนการเข้าคอร์ส ก่อนวันเริ่มคอร์ส 3 วัน
        $schedule->call(function () {
            (new SmsController)->runCourseReminder();
        })->dailyAt('09:00');

        // SMS แจ้งแอดมินรายการผู้สมัครใหม่ (ของเมื่อวาน; วันจันทร์รวม ศ-ส-อา)
        // ส่งเฉพาะ จ-ศ เวลา 08:00 (อยู่ในกรอบ 08:00-17:00)
        $schedule->call(function () {
            (new SmsController)->runAdminNewApplyNotify();
        })->weekdays()->dailyAt('08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
