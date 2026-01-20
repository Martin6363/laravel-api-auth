<!DOCTYPE html>
<html lang="hy">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ config('app.name') }} - {{ Lang::get('api-auth::auth.verify_email_subject') }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }

        * { box-sizing: border-box; }

        @media only screen and (max-width: 600px) {
            .full-width { width: 100% !important; }
            .container { width: 100% !important; padding: 10px !important; }
            .content { padding: 25px 15px !important; }
            .button {
                display: block !important;
                width: 100% !important;
                padding: 15px 0 !important;
                text-align: center !important;
            }
            h1 { font-size: 22px !important; }
        }

        .external-link {
            word-break: break-all !important;
        }
    </style>
</head>
<body style="background-color: #f3f4f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 0; width: 100% !important;">

<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f3f4f6; table-layout: fixed;">
    <tr>
        <td align="center" style="padding: 20px 0;">

            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="container" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); margin: 0 auto;">

                <tr>
                    <td style="background-color: {{ $primaryColor }}; padding: 30px; text-align: center;">
                        <h1 style="color: {{ $buttonTextColor }}; font-size: 24px; font-weight: 700; margin: 0; letter-spacing: 0.5px;">
                            {{ config('app.name') }}
                        </h1>
                    </td>
                </tr>

                <tr>
                    <td class="content" style="padding: 40px; background-color: #ffffff;">
                        <h2 style="color: #1f2937; font-size: 18px; font-weight: 600; margin-top: 0; margin-bottom: 20px;">
                            {{ Lang::get('api-auth::auth.verify_email_greeting') }}
                        </h2>

                        @foreach ($lines as $line)
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 18px;">
                                {{ $line }}
                            </p>
                        @endforeach

                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px; margin-bottom: 30px;">
                            <tr>
                                <td align="center">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="full-width">
                                        <tr>
                                            <td align="center" style="border-radius: 6px; background-color: {{ $primaryColor }};">
                                                <a href="{{ $actionUrl }}" class="button" style="background-color: {{ $primaryColor }}; border: 1px solid {{ $primaryColor }}; border-radius: 6px; color: {{ $buttonTextColor }}; display: inline-block; font-size: 16px; font-weight: 600; padding: 14px 30px; text-decoration: none; min-width: 200px;">
                                                    {{ $actionText }}
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <p style="color: #6b7280; font-size: 14px; line-height: 1.4; margin-top: 30px; border-top: 1px solid #f3f4f6; padding-top: 20px;">
                            {{ Lang::get('If you did not request this, no further action is required.') }}
                        </p>

                        <p class="external-link" style="color: #9ca3af; font-size: 12px; margin-top: 20px;">
                            {{ Lang::get('If you’re having trouble clicking the button, copy and paste the URL below into your web browser:') }}<br>
                            <a href="{{ $actionUrl }}" style="color: {{ $primaryColor }}; text-decoration: underline;">{{ $actionUrl }}</a>
                        </p>
                    </td>
                </tr>
            </table>

            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" class="container" style="max-width: 600px; margin: 0 auto;">
                <tr>
                    <td style="padding: 20px; text-align: center; color: #9ca3af; font-size: 12px;">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>