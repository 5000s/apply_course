<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{

    protected $fillable = [
        'gender',
        'name',
        'surname',
        'nickname',
        'age',
        'birthdate',
        'buddhism',
        'status',
        'phone',
        'phone_desc',
        'phone_2',
        'phone_2_desc',
        'phone_slug',
        'blacklist',
        'email',
        'province',
        'country',
        'facebook',
        'organization',
        'expertise',
        'degree',
        'career',
        'techo_year',
        'techo_courses',
        'blacklist_release',
        'blacklist_remark',
        'pseudo',
        'url_apply',
        'url_history',
        'url_image',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'line',
        'nationality',
        'name_emergency',
        'surname_emergency',
        'phone_emergency',
        'relation_emergency',
        'dharma_ex',
        'dharma_ex_desc',
        'know_source',
        'shelter_number',
        'medical_condition',
        'applycode',
        'current_level',
        'level_1_date',
        'level_2_date',
        'level_3_date',
        'level_4_date',
        'death_date',
        'leave_date',
        'leave_description'
    ];

    protected $dates = ['birthdate'];

    protected $casts = [
        'birthdate' => 'date', // or 'datetime'
    ];

    public static function getEnumValues($column)
    {
        $type = DB::select("SHOW COLUMNS FROM members WHERE Field = ?", [$column])[0]->Type;
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $values = explode(',', $matches[1]);
        return array_map(fn($value) => trim($value, "'"), $values);
    }



    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $value;
        $this->attributes['phone_clean'] = self::normalizeThaiPhone($value);
    }


    // --- Accessor: เบอร์แบบสวย ๆ ---
    public function getPhonePrettyAttribute()
    {
        $clean = $this->phone_clean ?: self::normalizeThaiPhone($this->phone);

        if (!$clean) return null;

        // mobile 10 หลัก ขึ้นต้น 06/08/09 → 081 234 5678
        if (preg_match('/^(06|08|09)\d{8}$/', $clean)) {
            return substr($clean, 0, 3) . ' ' . substr($clean, 3, 3) . ' ' . substr($clean, 6, 4);
        }

        // เบอร์บ้าน กทม. 02-xxx-xxxx → 02 123 4567
        if (preg_match('/^02\d{7}$/', $clean)) {
            return substr($clean, 0, 2) . ' ' . substr($clean, 2, 3) . ' ' . substr($clean, 5, 4);
        }

        // เบอร์บ้านต่างจังหวัด 0x-xxxx-xxxx (รวม 9–10 หลัก)
        if (preg_match('/^0[3-9]\d{7,8}$/', $clean)) {
            // 0AA BBB BBBB / 0A BBB BBBB ตามความยาว
            if (strlen($clean) === 9) {
                return substr($clean, 0, 2) . ' ' . substr($clean, 2, 3) . ' ' . substr($clean, 5, 4);
            }
            if (strlen($clean) === 10) {
                return substr($clean, 0, 3) . ' ' . substr($clean, 3, 3) . ' ' . substr($clean, 6, 4);
            }
        }

        return $clean; // fallback
    }

    // --- Helper: แปลงเป็นเลขไทยมาตรฐาน 0xxxxxxxxx ---
    public static function normalizeThaiPhone(?string $raw): ?string
    {
        if (!$raw) return null;
        $digits = preg_replace('/\D+/', '', $raw);  // เอาเฉพาะตัวเลข

        if ($digits === '') return null;

        // แปลง +66 / 66xxxxx → 0xxxxx
        if (str_starts_with($digits, '66')) {
            $digits = '0' . substr($digits, 2);
        }

        // บางกรณีเป็น 660… จาก copy เบอร์สากล
        if (str_starts_with($digits, '660')) {
            $digits = '0' . substr($digits, 3);
        }

        // ถ้าเริ่มไม่ใช่ 0 แต่ยาวพอ ให้คงไว้ (กรณีเบอร์ตปท) — แต่ระบบนี้โฟกัสไทย
        // ตัดความยาวเกิน 10–11 เผื่อมีต่อเบอร์
        if (strlen($digits) > 11) {
            // เก็บเฉพาะ 11 ตัวแรก (กรณี 02xxxxxxxx) หรือ 10 ตัวแรก (mobile)
            // ใช้ heuristic ง่าย ๆ
            $digits = preg_match('/^02/', $digits) ? substr($digits, 0, 9) : substr($digits, 0, 10);
        }

        return $digits;
    }

    public static function findCandidate($gender, $firstname, $lastname, $birthDate)
    {
        $candidate = self::whereDate('birthdate', $birthDate)
            ->where('gender', $gender)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($firstname)])
            ->whereRaw('LOWER(surname) = ?', [mb_strtolower($lastname)])
            ->first();

        if ($candidate) {
            return $candidate;
        }

        // 1) คัดกรองจากวันเกิดก่อน (ลดจำนวนแถว)
        $candidates = self::query()
            ->whereDate('birthdate', $birthDate)
            ->where('gender', $gender)
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        // helper: normalize ชื่อให้เทียบได้ (รองรับไทย)
        $norm = function (string $s) {
            $s = mb_strtolower($s, 'UTF-8');
            $s = preg_replace('/[^[:alnum:]\p{Thai}]+/u', '', $s);
            return $s;
        };

        // ตัด 3 ตัวแรกจากอินพุต
        $inFirst3 = mb_substr($norm((string) $firstname), 0, 3, 'UTF-8');
        $inLast3  = mb_substr($norm((string) $lastname),  0, 3, 'UTF-8');

        if ($inFirst3 === '' || $inLast3 === '') {
            return null;
        }

        // 2) กรองชื่อ: ต้องตรงกันทั้งชื่อและนามสกุล (3 ตัวแรก) แล้วคืนตัวแรก
        $match = $candidates->first(function ($m) use ($norm, $inFirst3, $inLast3) {
            $first = $m->first_name ?? $m->name ?? '';
            $last  = $m->last_name  ?? $m->surname ?? '';

            $candFirst3 = mb_substr($norm($first), 0, 3, 'UTF-8');
            $candLast3  = mb_substr($norm($last),  0, 3, 'UTF-8');

            return (mb_strtolower($candFirst3) === mb_strtolower($inFirst3)) && (mb_strtolower($candLast3) === mb_strtolower($inLast3));
        });

        return $match ?: null;
    }

    public static function findPossibleMatches($gender, $firstname, $lastname, $birthDate)
    {
        // 1) คัดกรองจากวันเกิดและเพศ (ลดจำนวนแถว)
        $candidates = self::query()
            ->whereDate('birthdate', $birthDate)
            ->where('gender', $gender)
            ->get();

        if ($candidates->isEmpty()) {
            return collect();
        }

        // helper: normalize ชื่อให้เทียบได้ (รองรับไทย)
        $norm = function (string $s) {
            $s = mb_strtolower($s, 'UTF-8');
            $s = preg_replace('/[^[:alnum:]\p{Thai}]+/u', '', $s);
            return $s;
        };

        // ตัด 3 ตัวแรกจากอินพุต
        $inFirst3 = mb_substr($norm((string) $firstname), 0, 3, 'UTF-8');
        $inLast3  = mb_substr($norm((string) $lastname),  0, 3, 'UTF-8');

        if ($inFirst3 === '' || $inLast3 === '') {
            return collect();
        }

        // 2) กรองชื่อ: ต้องตรงกันทั้งชื่อและนามสกุล (3 ตัวแรก)
        $matches = $candidates->filter(function ($m) use ($norm, $inFirst3, $inLast3) {
            $first = $m->first_name ?? $m->name ?? '';
            $last  = $m->last_name  ?? $m->surname ?? '';

            $candFirst3 = mb_substr($norm($first), 0, 3, 'UTF-8');
            $candLast3  = mb_substr($norm($last),  0, 3, 'UTF-8');

            return ($candFirst3 === $inFirst3) && ($candLast3 === $inLast3);
        });

        return $matches->values();
    }

    public static function findMatchingMember($gender, $firstname, $lastname, $birthDate)
    {
        if (trim($firstname) === '' || trim($lastname) === '') {
            return collect();
        }

        // Strip Thai honorifics
        $first = preg_replace('/^(แม่ชี|พระ|สามเณร|นาง|นาย)/u', '', trim($firstname));
        $last = trim($lastname);

        // Extract day and month if birthDate is provided
        $day = $month = $year = null;
        if ($birthDate) {
            try {
                $dt = \Carbon\Carbon::parse($birthDate);
                $day = $dt->day;
                $month = $dt->month;
                $year = $dt->year;
            } catch (\Exception $e) {
                // ignore
            }
        }

        // Broad query to get candidates
        // We filter strictly in PHP to handle the "80% similarity" requirement
        $candidates = self::where('gender', $gender)
            ->where(function ($q) use ($first, $day, $month, $year) {
                // Optimization: Match records with same birth day/month
                if ($day && $month) {
                    $q->where(function ($q2) use ($day, $month) {
                        $q2->whereMonth('birthdate', $month)
                            ->whereDay('birthdate', $day);
                    });
                }

                if ($year) {
                    $q->orWhereYear('birthdate', $year);
                }

                // OR Name starts with the same first character (Heuristic for performance)
                // If name is short, valid matches usually share the first char.
                if (mb_strlen($first) > 0) {
                    $c1 = mb_substr($first, 0, 1);
                    $q->orWhere('name', 'LIKE', "$c1%");
                }
            })
            ->get();

        return $candidates->filter(function ($m) use ($first, $last, $day, $month, $year) {
            // Helper to calc percent
            $simName = 0;
            similar_text(mb_strtolower($m->name), mb_strtolower($first), $simName);

            // 1) Name + Surname (Partial / Loose Match ~ 80%)
            $simSur = 0;
            $surname = $m->surname ?? ''; // Handle null surname
            similar_text(mb_strtolower($surname), mb_strtolower($last), $simSur);

            if ($simName >= 90 && $simSur >= 90) {
                return true;
            }

            // 2) OR Name + Birth Day+Month OR Year
            if ($m->birthdate) {
                try {
                    $mDt = \Carbon\Carbon::parse($m->birthdate);

                    $isDayMonthMatch = ($day && $month && $mDt->month == $month && $mDt->day == $day);
                    $isYearMatch = ($year && $mDt->year == $year);

                    if ($isDayMonthMatch || $isYearMatch) {
                        // Check name similarity (e.g. > 75%)
                        if ($simName >= 75 && $simSur >= 75) {
                            return true;
                        }

                        if ($simName >= 90 && $simSur >= 70) {
                            return true;
                        }

                        if ($simName >= 70 && $simSur >= 90) {
                            return true;
                        }
                    }
                } catch (\Exception $e) {
                }
            }

            return false;
        })
            ->sortByDesc(function ($m) use ($first, $last) {
                $simName = 0;
                similar_text(mb_strtolower($m->name), mb_strtolower($first), $simName);
                $simSur = 0;
                similar_text(mb_strtolower($m->surname ?? ''), mb_strtolower($last), $simSur);
                return $simName + $simSur;
            })
            ->values();
    }
}
