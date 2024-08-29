@extends('layouts.app')

@section('content')
    <div class="container">


        <div class="d-flex justify-content-between align-items-center my-4">
            <h1 class="text-center my-4">{{ __('messages.course_list') }}</h1>
            <a href="{{ route('profile') }}" class="btn btn-secondary">{{ __('messages.back') }}</a>
        </div>

        <div class="row">
            <div class="col" style="text-align: center; justify-content: center;">
                <form action="{{route('courses.index', $member_id)}}" method="get">
                    <table>
                        <tr>
                            <td>{{ __('messages.location') }}</td>
                            <td>{{ __('messages.course') }}</td>
                            <td>{{ __('messages.year') }}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>
                                <select class="select select-bordered max-w-xs" name="location" id="location">
                                    @php $location = request()->query('location') @endphp
                                    <option @if($location==1) selected @endif value="1">แก่งคอย</option>
                                    <option @if($location==2) selected @endif value="2">ลานหิน</option>
                                    <option @if($location==3) selected @endif value="3">หาดใหญ่</option>
                                    <option @if($location==4) selected @endif value="4">มูลนิธิ อ่อนนุช</option>
                                </select>
                            </td>
                            <td>
                                <select class="form select select-bordered max-w-xs" name="category" id="category">
                                    @php $category = request()->query('category') @endphp
                                    <option @if($category==0) selected @endif value="0">ทั้งหมด</option>
{{--                                    <option @if($category==7) selected @endif value="7">ธรรมะแคมป์</option>--}}
                                    <option @if($category==8) selected @endif value="8">คอร์สอานาปานสติ 1 วัน</option>
                                    <option @if($category==5) selected @endif value="5">คอร์สอานาปานสติ 4 วัน</option>
                                    <option @if($category==1) selected @endif value="1">คอร์สวิปัสสนา 7 วัน</option>
                                    <option @if($category==3) selected @endif value="3">คอร์สวิปัสสนา 7 วัน (ศิษย์เก่า)</option>
                                    <option @if($category==6) selected @endif value="6">คอร์สวิปัสสนา 3 วัน (ศิษย์เก่า)</option>
{{--                                    <option @if($category==4) selected @endif value="4">คอร์สวิถีอาสวะ</option>--}}
{{--                                    <option @if($category==2) selected @endif value="2">คอร์สฤาษี (๑๔ วัน)</option>--}}
{{--                                    <option @if($category==9) selected @endif value="9">คอร์สเตโชฯ (อาวุโส)</option>--}}
                                </select>
                            </td>
                            <td>
                                <select class="select select-bordered max-w-xs" name="year" id="year">
                                    @for($year = \Carbon\Carbon::now()->year ; $year <= \Carbon\Carbon::now()->year +1 ; $year++)
                                        @php $year_s = request()->query('year') @endphp
                                        <option @if($year_s==$year) selected @endif value="{{$year}}">{{$year}}</option>
                                    @endfor
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-active">{{ __('messages.search') }}</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <table class="table">
            <thead>
            <tr>
                <td class="text-center w-[20%]">{{ __('messages.course_name') }}</td>
                <td class="text-center w-[20%]">{{ __('messages.start_date') }}</td>
                <td class="text-center w-[20%]">{{ __('messages.end_date') }}</td>
                <td class="text-center w-[20%]">{{ __('messages.status') }}</td>
                <td class="text-center w-[20%]">{{ __('messages.application') }}</td>
            </tr>
            </thead>
            <tbody>
            @foreach($courses as $course)
                <tr>
                    <td class="text-center">{{ $course->category }}</td>
                    <td class="text-center">{{ $course->date_start }}</td>
                    <td class="text-center">{{ $course->date_end }}</td>
                    <td class="text-center">{{ $course->state }}</td>
                    <td>
                        @if($course->state === 'เปิดรับสมัคร')
                            @if(is_null($course->apply_id))
                                <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-primary">{{ __('messages.register') }}</a>
                            @else
                                <a href="{{ route('courses.show', [$member_id, $course->id]) }}" class="btn btn-secondary">{{ __('messages.edit') }}</a>
                            @endif
                        @else
                            {{ __('messages.closed') }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
