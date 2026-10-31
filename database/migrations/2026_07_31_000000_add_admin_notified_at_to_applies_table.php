<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAdminNotifiedAtToAppliesTable extends Migration
{
    /**
     * เพิ่มคอลัมน์ติดตามว่าได้แจ้งเตือนแอดมินเรื่องผู้สมัครรายนี้แล้วหรือยัง (SMS รวมกลุ่ม)
     */
    public function up(): void
    {
        if (!Schema::hasColumn('applies', 'admin_notified_at')) {
            Schema::table('applies', function (Blueprint $table) {
                $table->timestamp('admin_notified_at')->nullable()->after('updated_at');
            });

            // Backfill: ใบสมัครเดิมทั้งหมดถือว่าแจ้งแล้ว เพื่อไม่ให้รอบแรกยิง SMS ย้อนหลังจำนวนมาก
            DB::table('applies')->whereNull('admin_notified_at')->update(['admin_notified_at' => now()]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('applies', 'admin_notified_at')) {
            Schema::table('applies', function (Blueprint $table) {
                $table->dropColumn('admin_notified_at');
            });
        }
    }
}
