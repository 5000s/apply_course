<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to {{ config('app.name') }}</title>
    <style type="text/css">
        /* Basic Reset */
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
        img {
            max-width: 100%;
            height: auto;
            border: 0;
        }
        a {
            color: #007bff; /* Primary link color */
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }

        /* Container */
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #f8f9fa; /* Light gray header */
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

        /* Responsive Styles */
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
            .header h1 {
                font-size: 20px !important;
            }
            .button {
                padding: 10px 20px !important;
                font-size: 14px !important;
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
                        <h1>Welcome to {{ config('app.name') }}!</h1>
                    </td>
                </tr>
                <tr>
                    <td class="content">
                        <p>Hi {{ $name ?? 'there' }},</p>
                        <p>Thank you for joining {{ config('app.name') }}. We're thrilled to have you as part of our community!</p>
                        <p>We've created this account for you so you can start exploring all the amazing features we offer. Here are some quick links to get you started:</p>

                        <div class="button-container">
                            <a href="{{ url('/') }}" class="button">Visit Our Website</a>
                        </div>

                        @if (isset($login_url))
                            <div class="button-container">
                                <a href="{{ $login_url }}" class="button">Log In Now</a>
                            </div>
                            <p>You can use your registered email and password to log in.</p>
                        @endif

                        <p>If you have any questions, feel free to reply to this email. We're always here to help!</p>
                        <p>Best regards,</p>
                        <p>The Team at {{ config('app.name') }}</p>
                    </td>
                </tr>
                <tr>
                    <td class="footer">
                        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
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
