<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>รหัสผ่านใหม่ของคุณ / Your New Password</title>
    <style type="text/css">
        body,
        html {
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
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

        .password-container {
            text-align: center;
            margin-top: 25px;
            margin-bottom: 15px;
        }

        .password {
            display: inline-block;
            padding: 12px 30px;
            background-color: #f8f9fa;
            color: #007bff;
            border: 2px solid #007bff;
            border-radius: 5px;
            font-size: 28px;
            font-weight: bold;
            letter-spacing: 5px;
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
                            <h1>{{ __('mail.new_password.subject') }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td class="content">
                            <p>{{ __('mail.new_password.greeting', ['name' => $name ?? __('User')]) }}</p>

                            <p>{{ __('mail.new_password.intro') }}</p>

                            <div class="password-container">
                                <div class="password">{{ $password }}</div>
                            </div>

                            <div class="button-container"
                                style="text-align: center; margin-top: 25px; margin-bottom: 25px;">
                                <a href="{{ route('login', ['email' => $email ?? '']) }}"
                                    style="display: inline-block; padding: 12px 25px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">
                                    {{ __('mail.new_password.button') }}
                                </a>
                            </div>
                            {{-- 
                            <p style="color: #dc3545; font-size: 14px;">
                                * {{ __('mail.new_password.warning') }}
                            </p> --}}

                            <p style="margin-top: 30px;">
                                {{ __('mail.new_password.closing') }}<br>
                                {{ __('mail.new_password.team') }}
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td class="footer">
                            <p>&copy; {{ date('Y') }} {{ __('mail.new_password.team') }}.
                                {{ __('mail.new_password.footer') }}</p>
                            <p>
                                <a href="{{ url('/') }}">{{ __('mail.new_password.visit') }}</a> |
                                <a href="mailto:info@bodhidhammayan.org">{{ __('mail.new_password.contact') }}</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
