<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CourseApplyExport implements FromCollection, WithHeadings
{
    protected $course_id;

    public function __construct($course_id)
    {
        $this->course_id = $course_id;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return DB::table('members as m')
            ->select(
                DB::raw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s') as apply_date"),
                'a.id as apply_id',
                'c.coursename as coursename',
                'm.name',
                'm.surname',
                'm.phone',
                'm.email',
                DB::raw("TIMESTAMPDIFF(YEAR, m.birthdate, CURDATE()) as age"),
                'm.gender',
                'm.buddhism',
                'a.state',
                'a.updated_by'
            )
            ->join('applies as a', 'a.member_id', '=', 'm.id')
            ->join('courses as c', 'c.id', '=', 'a.course_id')
            ->where('a.course_id', $this->course_id)
            ->where(function ($query) {
                $query->where('a.cancel', 0)
                    ->orWhereNull('a.cancel');
            })
            ->orderByRaw("DATE_FORMAT(a.created_at, '%Y-%m-%d %H:%i:%s')")
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Apply Date',
            'Apply ID',
            'Course Name',
            'Name',
            'Surname',
            'Phone',
            'Email',
            'Age',
            'Gender',
            'Buddhism',
            'State',
            'Updated By'
        ];
    }
}
