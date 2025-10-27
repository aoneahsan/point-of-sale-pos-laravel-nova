<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS System')</title>
    <style>
        /* Email-safe CSS */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f8fafc;
            padding: 40px 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
            padding: 32px 40px;
            text-align: center;
        }

        .email-header img {
            max-height: 40px;
            width: auto;
        }

        .email-header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 16px 0 0 0;
        }

        .email-body {
            padding: 40px;
            color: #1e293b;
            line-height: 1.6;
        }

        .email-body h2 {
            color: #1e40af;
            font-size: 20px;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 16px;
        }

        .email-body p {
            margin: 0 0 16px 0;
            color: #64748b;
        }

        .email-button {
            display: inline-block;
            padding: 12px 32px;
            background-color: #1e40af;
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            margin: 24px 0;
        }

        .email-button:hover {
            background-color: #1e3a8a;
        }

        .email-footer {
            background-color: #f8fafc;
            padding: 32px 40px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }

        .email-footer p {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #94a3b8;
        }

        .email-footer a {
            color: #1e40af;
            text-decoration: none;
        }

        .info-box {
            background-color: #eff6ff;
            border-left: 4px solid #1e40af;
            padding: 16px;
            border-radius: 4px;
            margin: 24px 0;
        }

        .info-box p {
            margin: 0;
            color: #1e293b;
        }

        .success-box {
            background-color: #d1fae5;
            border-left: 4px solid #059669;
            padding: 16px;
            border-radius: 4px;
            margin: 24px 0;
        }

        .success-box p {
            margin: 0;
            color: #1e293b;
        }

        .warning-box {
            background-color: #fed7aa;
            border-left: 4px solid #f59e0b;
            padding: 16px;
            border-radius: 4px;
            margin: 24px 0;
        }

        .warning-box p {
            margin: 0;
            color: #1e293b;
        }

        .divider {
            height: 1px;
            background-color: #e2e8f0;
            margin: 32px 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
        }

        .data-table th {
            background-color: #f8fafc;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }

        @media only screen and (max-width: 600px) {
            .email-body {
                padding: 24px;
            }

            .email-footer {
                padding: 24px;
            }

            .email-header {
                padding: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <!-- Logo would go here if we had a public URL -->
                <h1>POS System</h1>
            </div>

            <!-- Body Content -->
            <div class="email-body">
                @yield('content')
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p><strong>POS System</strong></p>
                <p>&copy; {{ date('Y') }} POS System. All rights reserved.</p>
                <p>
                    This email was sent from an automated system. Please do not reply.
                </p>
                @yield('footer')
            </div>
        </div>
    </div>
</body>
</html>
