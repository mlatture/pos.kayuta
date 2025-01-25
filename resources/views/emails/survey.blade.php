<!DOCTYPE html>
<html>
<head>
    <title>Survey Email</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        h1 {
            color: #4CAF50;
            font-size: 24px;
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            color: #555555;
            font-size: 16px;
            line-height: 1.6;
        }

        .cta-button {
            display: block;
            width: fit-content;
            margin: 20px auto;
            text-align: center;
            background-color: #4CAF50;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
        }

        .cta-button:hover {
            background-color: #45a049;
        }

       
        footer {
            text-align: center;
            color: #aaaaaa;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h1>{{ ucfirst($survey->subject) }}</h1>
        <p>Dear {{ $survey->guest_email }},</p>
        <p>{{ $survey->message }}</p>

        <a href="https://docs.google.com/forms/d/e/1FAIpQLScFk0U06-6VJx4fWUC1ngIpn6dbEy6At_4Z_UlcOoJJ631ARQ/viewform?usp=sf_link" class="cta-button">
            Click here to complete the survey
        </a>

    
        <p>Thank you,</p>
        <p>Your Team</p>
    </div>

    <footer>
        &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
    </footer>
</body>
</html>
