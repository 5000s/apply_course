<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPhoneToLocationsTable extends Migration
{
    /**
     * เบอร์ติดต่อเจ้าหน้าที่ประจำแต่ละสถานที่ (ใช้แจ้งกรณีฉุกเฉิน)
     * Contact phone per location, keyed by location id.
     */
    private array $phones = [
        4 => '083-226 2799',                 // มูลนิธิ อ่อนนุช
        1 => '091-416-6008',                 // แก่งคอย / สระบุรี
        3 => '061-491-4459, 084-095-5426',   // หาดใหญ่
        5 => '094-923-6441, 081-956-0389',   // ภูเก็ต
    ];

    public function up(): void
    {
        if (!Schema::hasColumn('locations', 'phone')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('subdistrict');
            });
        }

        foreach ($this->phones as $id => $phone) {
            DB::table('locations')->where('id', $id)->update(['phone' => $phone]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('locations', 'phone')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }
    }
}
