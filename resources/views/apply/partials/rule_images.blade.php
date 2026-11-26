{{-- รูปกฎระเบียบท้ายอีเมลสมัครคอร์ส: src เป็น cid: (รูปฝังในอีเมล)
     ใช้ inline style เพราะโปรแกรมอีเมลไม่โหลด CSS ภายนอก --}}
@if (!empty($rule_images))
    <div style="margin: 24px 0 8px; text-align: center;">
        @foreach ($rule_images as $ruleImage)
            <img src="{{ $ruleImage['src'] }}" alt="{{ $ruleImage['alt'] }}" width="600"
                style="display: block; width: 100%; max-width: 600px; height: auto; margin: 0 auto 16px; border: 0;">
        @endforeach
    </div>
@endif
