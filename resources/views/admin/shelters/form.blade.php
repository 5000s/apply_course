@extends('layouts.master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h1>{{ $title ?? 'จัดการ Shelter' }}</h1>
                    <a href="{{ route('shelters.index') }}" class="btn btn-secondary">กลับไปหน้ารวม</a>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form
                            action="{{ isset($shelter) ? route('shelters.update', $shelter->id) : route('shelters.store') }}"
                            method="POST">
                            @csrf
                            @if (isset($shelter))
                                @method('PUT')
                            @endif

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="number">Number</label>
                                    <input type="text" name="number" id="number" class="form-control"
                                        value="{{ old('number', $shelter->number ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="index">Index</label>
                                    <input type="number" name="index" id="index" class="form-control"
                                        value="{{ old('index', $shelter->index ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        value="{{ old('name', $shelter->name ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="surname">Surname</label>
                                    <input type="text" name="surname" id="surname" class="form-control"
                                        value="{{ old('surname', $shelter->surname ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="member_id">Member ID</label>
                                    <input type="number" name="member_id" id="member_id" class="form-control"
                                        value="{{ old('member_id', $shelter->member_id ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="extra_user">Extra User</label>
                                    <input type="text" name="extra_user" id="extra_user" class="form-control"
                                        value="{{ old('extra_user', $shelter->extra_user ?? '') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="note">Note</label>
                                    <textarea name="note" id="note" class="form-control">{{ old('note', $shelter->note ?? '') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="note_master">Note Master</label>
                                    <textarea name="note_master" id="note_master" class="form-control">{{ old('note_master', $shelter->note_master ?? '') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
