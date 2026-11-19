{{-- resources/views/apply/partials/rules.blade.php --}}
{{-- กฎระเบียบและข้อบังคับ: ต้องติ๊กยอมรับก่อน จึงจะแสดงฟอร์มค้นหาชื่อเพื่อสมัคร (#applyFormWrap) --}}
{{-- ตัวแปรที่ใช้: $course, $lang, $vm['is_vipassana'], $rulesAccepted --}}
@php
    $rulesText = [
        'th' => [
            'header' => 'กฎระเบียบและข้อบังคับ',
            'water' =>
                'ขอความร่วมมือให้ผู้เข้าอบรมทุกท่าน <strong>เตรียมกระบอกน้ำส่วนตัว</strong> เพื่อใส่น้ำดื่มระหว่างการเข้าอบรม',
            'accept' => 'รับทราบและยอมรับในการปฏิบัติตามกฎระเบียบและข้อบังคับทุกประการ',
            // คอร์สทั่วไป (ไม่เกิน 6 วัน)
            'general' => [
                'ปิดวาจา งดใช้เครื่องมือสื่อสารทุกชนิด ระหว่างอบรม',
                'ฝากของ เช่น กระเป๋าขนาดใหญ่ โดยเฉพาะอย่างยิ่ง เครื่องมือสื่อสาร กับเจ้าหน้าที่ผู้ดูแล',
                'ใส่เสื้อคอปกสีขาว มีแขน ตัวเสื้อยาวไม่รัดรูป เนื้อผ้าไม่บางเกินไป กางเกงขายาวทรงหลวมเพื่อให้สะดวกต่อการลุกนั่ง สีสุภาพ เช่น สีขาว ครีม เทา น้ำตาล กรมท่า เป็นต้น (หรือชุดปฏิบัติธรรม) และใส่รองเท้าที่สวมใส่ง่าย',
                'ผู้หญิงกรุณารวบผมให้เรียบร้อย ไม่ปล่อยผม',
                'ไม่สวมเสื้อแขนกุด และกางเกงขาสั้น ทั้งผู้ที่สมัครและผู้ที่มาส่งที่ต้องเข้ามารอ',
            ],
            // คอร์สวิปัสสนา (เกิน 6 วัน)
            'vipassana' => [
                '<strong>ไม่อนุญาตผู้ที่มีรอยสักอยู่บนใบหน้าและศีรษะเข้ารับการอบรม</strong> เพราะจะทำให้กระทบใจผู้ปฏิบัติอื่น ๆ ส่วนผู้ที่มีรอยสักตามตัวเช่นแขนและขา ต้องแต่งกายให้มิดชิด ไม่ให้เห็นรอยสักนั้น',
                'ผู้สมัครต้องอ่านตารางปฏิบัติว่ามีอะไรบ้างให้เข้าใจ และยอมรับในการปฏิบัติตามนั้น',
                '<strong>รักษากฎของการห้ามพูด</strong> และติดต่อสื่อสารระหว่างกัน ทั้งระหว่าง ผู้ปฏิบัติและกับโลกภายนอก ตลอดระยะเวลาเข้ารับการอบรม ยกเว้น การสอบถามใด ๆ กับอาจารย์ผู้สอน หรือธรรมบริกร',
                '<strong>ต้องใส่ชุดสีขาวปฏิบัติธรรมเท่านั้น</strong> เนื้อผ้าไม่บางเกินไป กางเกงต้องมีความยาวเลยเข่าลงมา ต้องเป็นทรงหลวม ๆ สบาย ๆ เพื่อให้ศิษย์ทั้งใหม่และเก่า ปรับตัวปรับใจก่อนที่จะมานั่งปฏิบัติธรรม ณ โพธิธรรมญาณสถานอันศักดิ์สิทธิ์ มาอยู่เบื้องหน้าท่านอาจารย์เพื่อรับการอบรมสอนสั่ง แผ่พลังและสอบอารมณ์ให้ ผู้ที่เข้าอบรมวิปัสสนากรรมฐานและอบรมสมาธิอานาปานสติ ทุกคนต้องมาปฏิบัติภาวนาด้วยชุดขาวปฏิบัติธรรม หรือเสื้อขาวล้วนและผ้าถุงหรือกางเกงขายาวสีขาว <strong>"ห้ามใส่เสื้อยืด"</strong> เพื่อเตือนตัวเองว่า มาอบรมจิตเช่นนักบวช ไม่ใช่แค่มาย้ายที่นอนและมานั่งสะสมแต้มบุญกุศล หากใครไม่พร้อม ให้นอนอยู่บ้าน พักผ่อนท่องเที่ยว ไปตระเวนทำบุญให้สบายใจ ไม่ต้องออกมาสู่ศึกสงครามประหารกิเลสเพื่อการสิ้นภพจบชาติ โพธิธรรมญาณสถานไม่ใช่ที่แวะจ่ายตลาด แต่เป็นที่ ๆ ปลุกจิตสำนึกที่มืดบอด ให้ตื่น และเป็นที่ ๆ จะพาทุกดวงจิตให้พ้นกรรมและพ้นจากความเป็นทาส ได้รับความเป็นไท พ้นจากการกดขี่ข่มเหงของกิเลสไว้ในสังสารวัฏ',
                '<strong>ไม่ให้นำหนังสือ วิทยุ เครื่องมือสื่อสารใด ๆ</strong> เช่น คอมพิวเตอร์, iPad, Smart Phone, เครื่องเล่นเพลงและของมีค่าใด ๆ ติดตัวมาด้วย',
                '<strong>โทรศัพท์มือถือ</strong> ในระหว่างปฏิบัติจะต้องฝากไว้ที่ส่วนกลาง ตลอดทั้ง 7 วันเท่านั้น ห้ามใช้ติดต่อสื่อสารเด็ดขาด หากมีเรื่องกังวลใจใด ๆ ให้ธรรมบริกรช่วยติดต่อแทนท่านได้',
                '<strong>ไม่อนุญาตให้ผู้ที่ทำสีผมฉูดฉาดเกินไป</strong> เช่น สีบรอนซ์ สีแดงสด มาปฏิบัติ เพราะจะรบกวนจิตใจผู้อื่นเกินไป อนุญาตการทำสีผมที่กลาง ๆ มาปฏิบัติได้ ผู้หญิงกรุณารวบผมให้เรียบร้อย ไม่ปล่อยผม',
                'ไม่ใส่เสื้อแขนกุด กางเกงขาสั้น หมายรวมถึงผู้ที่เดินทางมาส่งด้วย',
                '<strong>ผู้ปฏิบัติต้องอยู่ปฏิบัติ จนครบตามวันเวลาที่กำหนดในแต่ละคอร์ส</strong> ห้ามลากลับก่อน ไม่ว่าจะกรณีใด ๆ ยกเว้น ท่านอาจารย์จะอนุญาตให้ในกรณีของการเจ็บป่วยที่จำเป็นต้องออกไปรักษาด่วนเท่านั้น',
            ],
        ],
        'en' => [
            'header' => 'Rules and Regulations',
            'water' =>
                'All participants are kindly asked to <strong>bring a personal water bottle</strong> for drinking water during the course.',
            'accept' => 'I acknowledge and agree to follow all of the rules and regulations.',
            'general' => [
                'Observe silence and refrain from using any communication device during the course.',
                'Leave your belongings, such as large bags and especially communication devices, with the staff in charge.',
                'Wear a white collared shirt with sleeves, long and not tight-fitting, in a fabric that is not too thin, with loose long trousers that make sitting down and getting up easy, in modest colours such as white, cream, grey, brown or navy (or a meditation outfit), and shoes that are easy to slip on and off.',
                'Women are asked to tie their hair up neatly and not wear it loose.',
                'No sleeveless tops or shorts. This applies to applicants and to anyone accompanying them who needs to come in and wait.',
            ],
            'vipassana' => [
                '<strong>People with tattoos on the face or head are not admitted to the course</strong>, as this disturbs other practitioners. Those with tattoos elsewhere on the body, such as the arms or legs, must dress so that the tattoos are fully covered.',
                'Applicants must read and understand the practice schedule and agree to follow it.',
                '<strong>Keep the rule of silence</strong> and of no communication, both among practitioners and with the outside world, for the entire course. The only exception is asking questions of the teacher or the Dhamma servers.',
                '<strong>Only white meditation clothing may be worn.</strong> The fabric must not be too thin, trousers must reach below the knee, and the fit must be loose and comfortable. This helps students, new and returning, to prepare body and mind before sitting to practise at the sacred Bodhidhammayan centre, in the presence of the teacher, to receive instruction, the radiating of energy and the checking of their meditation. Everyone attending a Vipassana or Anapanasati course must practise in white meditation clothing, or in an all-white shirt with a white sarong or white long trousers. <strong>"No T-shirts."</strong> This is a reminder that you have come to train the mind as a monastic does, not simply to sleep somewhere else and sit collecting merit. Anyone who is not ready should stay at home, rest, travel or go about making merit at ease, and need not come out to the battle of destroying the defilements to end the cycle of rebirth. Bodhidhammayan is not a place to stop by on the way to the market. It is a place that awakens the blinded mind, and that leads every mind out of kamma and out of slavery to freedom, away from the oppression of the defilements in samsara.',
                '<strong>Do not bring books, radios or any communication device</strong>, such as a computer, iPad, smartphone or music player, or any valuables.',
                '<strong>Mobile phones</strong> must be left at the central office for the whole 7 days of practice. Using them to communicate is strictly forbidden. If something is worrying you, the Dhamma servers can make contact on your behalf.',
                '<strong>People with overly bright hair colour</strong>, such as bronze or bright red, <strong>are not admitted</strong>, as it is too distracting for others. Moderate hair colours are accepted. Women are asked to tie their hair up neatly and not wear it loose.',
                'No sleeveless tops or shorts. This also applies to anyone accompanying you to the centre.',
                '<strong>Practitioners must stay for the full duration of the course.</strong> Leaving early is not allowed for any reason, unless the teacher gives permission in a case of illness that needs urgent treatment.',
            ],
        ],
    ];

    $rulesLang = $rulesText[$lang === 'en' ? 'en' : 'th'];
    $rulesList = $rulesLang[!empty($vm['is_vipassana']) ? 'vipassana' : 'general'];
@endphp

@once
    @push('head')
        <style>
            .rules-list li {
                margin-bottom: .85rem;
                line-height: 1.75;
            }

            .rules-list li::marker {
                font-weight: 600;
            }

            .rules-accept {
                display: flex;
                align-items: flex-start;
                gap: .9rem;
                padding: 1rem 1.25rem;
                border: 2px solid #c9a750;
                border-radius: .75rem;
                background: #fdf9ee;
                cursor: pointer;
            }

            .rules-accept input[type="checkbox"] {
                flex: 0 0 auto;
                width: 1.6rem;
                height: 1.6rem;
                margin-top: .15rem;
                border: 2px solid #8a6d1f;
                cursor: pointer;
            }

            .rules-accept:focus-within {
                box-shadow: 0 0 0 .25rem rgba(201, 167, 80, .35);
            }
        </style>
    @endpush
@endonce

<div class="card shadow-sm mb-4" id="rulesCard">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">{{ $rulesLang['header'] }}</h5>
        @if ($lang === 'th')
            {{-- ปุ่มสลับเป็นภาษาอังกฤษสำหรับชาวต่างชาติ (ไปหน้าเดิม + lang=en) --}}
            <a href="{{ route('apply.direct', ['course_id' => $course->id, 'lang' => 'en']) }}"
                class="btn btn-primary rounded-pill px-3">For foreigners please click here</a>
        @endif
    </div>
    <div class="card-body">
        <ol class="rules-list fs-5 mb-4">
            @foreach ($rulesList as $rule)
                <li>{!! $rule !!}</li>
            @endforeach
        </ol>

        <p class="fs-5 mb-4">{!! $rulesLang['water'] !!}</p>

        <label class="rules-accept fs-5 fw-semibold mb-0" for="acceptRules">
            <input class="form-check-input" type="checkbox" id="acceptRules" autocomplete="off"
                @if ($rulesAccepted) checked @endif>
            <span>{{ $rulesLang['accept'] }}</span>
        </label>
    </div>
</div>

@push('scripts')
    {{-- ยอมรับกฎแล้วจึงแสดงฟอร์มค้นหาชื่อ/สมัคร (เอาติ๊กออก = ซ่อนฟอร์มอีกครั้ง) --}}
    <script>
        (function() {
            const acceptRules = document.getElementById('acceptRules');
            const applyFormWrap = document.getElementById('applyFormWrap');
            if (!acceptRules || !applyFormWrap) return;

            function syncApplyForm(shouldScroll) {
                applyFormWrap.classList.toggle('d-none', !acceptRules.checked);
                if (shouldScroll && acceptRules.checked) {
                    applyFormWrap.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }

            acceptRules.addEventListener('change', function() {
                syncApplyForm(true);
            });

            // กดย้อนกลับของเบราว์เซอร์อาจคืนค่าติ๊กเดิมมา ให้สถานะฟอร์มตรงกับช่องติ๊กเสมอ
            window.addEventListener('pageshow', function() {
                syncApplyForm(false);
            });
        })();
    </script>
@endpush
