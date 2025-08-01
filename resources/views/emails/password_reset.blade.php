<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Password Reset</title>
    <style type="text/css">
        /* Basic Reset & Styles (consistent with your other email templates) */
        body, html {
            margin: 0;
            padding: 0;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            width: 100%;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            line-height: 1.6;
            color: #333333;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        td {
            padding: 0;
            vertical-align: top;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
        }
        .header h1 {
            margin: 0;
            color: #343a40;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
            line-height: 1.6;
        }
        .button-container {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 15px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .footer a {
            color: #6c757d;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
                margin: 0 auto !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .content {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body>
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            <table class="email-container" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td class="header">
                        <h1>Password Reset Request</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <p>Hello {{ $name ?? 'there' }},</p>
                        <p>You are receiving this email because we received a password reset request for your account.</p>

                        <div class="button-container">
                            <a href="{{ $reset_url }}" class="button">Reset Password</a>
                        </div>

                        <p>This password reset link will expire in {{ config('auth.passwords.users.expire', 60) }} minutes.</p>

                        <p>If you did not request a password reset, no further action is required.</p>

                        <p>Regards,</p>
                        <p>The {{ $app_name }} Team</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer">
                        <p>&copy; {{ date('Y') }} {{ $app_name }}. All rights reserved.</p>
                        <p>
                            <a href="{{ url('/') }}">Our Website</a> |
                            <a href="mailto:{{ config('mail.from.address') }}">Contact Us</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
