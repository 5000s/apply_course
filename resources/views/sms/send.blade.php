{{-- resources/views/sms/send.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">ส่ง SMS (SMS-Kub)</h5>
                        <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ config('services.smskub.url') }}">API Console</a>
                    </div>
                    <div class="card-body">
                        @if(session('sms_result'))
                            @php $r = session('sms_result'); @endphp
                            @if($r['ok'])
                                <div class="alert alert-success">
                                    ✅ ส่งสำเร็จ (HTTP {{ $r['status'] }})
                                    @if(!empty($r['response']))
                                        <pre class="mt-2 mb-0 small">{{ json_encode($r['response'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-danger">
                                    ❌ ส่งไม่สำเร็จ (HTTP {{ $r['status'] ?? '-' }}) — {{ $r['error'] ?? 'Unknown error' }}
                                    @if(!empty($r['response']))
                                        <pre class="mt-2 mb-0 small">{{ json_encode($r['response'], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <form method="post" action="{{ route('sms.send') }}" id="smsForm">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">เบอร์ผู้รับ (E.164)</label>
                                <div class="input-group">
                                    <span class="input-group-text">66</span>
                                    <input type="text" class="form-control @error('to') is-invalid @enderror"
                                           name="to" id="to"
                                           value="{{ old('to', data_get(session('old_input'),'to')) }}"
                                           placeholder="91XXXXXXXX">
                                    @error('to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="form-text">ตัวอย่าง: 6691xxxxxxx หรือใส่เฉพาะส่วนหลัง 66 ตามที่ระบบกำหนด</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">ข้อความ</label>
                                <textarea class="form-control @error('message') is-invalid @enderror"
                                          name="message" id="message" rows="4"
                                          maxlength="1000"
                                          placeholder="พิมพ์ข้อความที่ต้องการส่ง...">{{ old('message', data_get(session('old_input'),'message')) }}</textarea>
                                @error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="d-flex justify-content-end"><small id="charCount" class="text-muted">0/1000</small></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sender (ตัวเลือก)</label>
                                <input type="text" class="form-control @error('sender') is-invalid @enderror"
                                       name="sender" id="sender"
                                       value="{{ old('sender', data_get(session('old_input'),'sender')) }}"
                                       placeholder="เช่น BDMFOUND">
                                @error('sender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">บางแพ็กเกจต้องอนุมัติชื่อผู้ส่งก่อนใช้งาน</div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">ส่งข้อความ</button>
                                <button type="button" class="btn btn-outline-secondary" id="btnFillTest">ใส่ข้อความตัวอย่าง</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer small text-muted">
                        Header ใช้: <code>{{ config('services.smskub.auth_header') }}</code>
                        &nbsp;Prefix: <code>{{ config('services.smskub.auth_prefix') ?: '(none)' }}</code>
                        &nbsp;Endpoint: <code>{{ rtrim(config('services.smskub.url'),'/') . config('services.smskub.send_path') }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const msg = document.getElementById('message');
                const counter = document.getElementById('charCount');
                const btnTest = document.getElementById('btnFillTest');

                const updateCount = () => {
                    const len = msg.value.length;
                    counter.textContent = `${len}/1000`;
                };
                msg.addEventListener('input', updateCount);
                updateCount();

                btnTest.addEventListener('click', () => {
                    document.getElementById('to').value = '91xxxxxxx';
                    msg.value = 'สวัสดีครับ ทดสอบส่ง SMS จากระบบสมัครคอร์ส 🙏';
                    updateCount();
                });
            });
        </script>
    @endpush
@endsection
