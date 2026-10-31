<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
</head>

<body style="font-family: Arial, 'Sarabun', sans-serif; color: #333;">
    <p>มีผู้แจ้งว่าไม่พบข้อมูลสมัครในระบบ กรุณาตรวจสอบและติดต่อกลับ</p>

    <h3 style="margin-bottom: 8px;">ข้อมูลผู้สมัคร</h3>
    <table cellpadding="6" style="border-collapse: collapse;">
        <tr>
            <td><strong>ชื่อ-นามสกุล:</strong></td>
            <td>{{ trim(($reportCase->name ?? '') . ' ' . ($reportCase->surname ?? '')) ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>เพศ:</strong></td>
            <td>{{ $reportCase->gender ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>วันเกิด:</strong></td>
            <td>{{ $reportCase->birthdate ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>เบอร์โทรศัพท์:</strong></td>
            <td>{{ $reportCase->phone ?: '-' }}</td>
        </tr>
        <tr>
            <td><strong>อีเมล:</strong></td>
            <td>{{ $reportCase->email ?: '-' }}</td>
        </tr>
    </table>

    <p style="color: #888; font-size: 12px; margin-top: 16px;">
        รหัสเคส: {{ $reportCase->id }}
    </p>
</body>

</html>
