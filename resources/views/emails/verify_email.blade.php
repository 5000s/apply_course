<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('mail.verify.subject') }}</title>
    <style type="text/css">
        /* Basic Reset & Styles (similar to welcome.blade.php for consistency) */
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
                        <h1>{{ __('mail.verify.title') }}</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <p>{{ __('mail.verify.greeting_formal', ['name' => $name ?? __('User')]) }}</p>

                        <p>{{ __('mail.verify.intro_formal') }}</p>

                        <div class="button-container">
                            <a href="{{ $verification_url }}" class="button">{{ __('mail.verify.button') }}</a>
                        </div>

                        <p>{{ __('mail.verify.expire_formal', ['count' => (int) config('auth.verification.expire', 60)]) }}</p>

                        <p>{{ __('mail.verify.ignore_formal') }}</p>

                        <p>{{ __('mail.verify.closing_formal') }}</p>
                        <p>{{ __('mail.verify.team_formal') }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer">
                        <p>&copy; {{ date('Y') }} {{ $app_name }}. {{ __('mail.verify.footer') }}</p>
                        <p>
                            <a href="{{ url('/') }}">{{ __('mail.verify.visit') }}</a> |
                            <a href="mailto:{{ config('mail.from.address') }}">{{ __('mail.verify.contact') }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
