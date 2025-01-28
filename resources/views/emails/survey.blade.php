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
        <p>Dear {{ ucfirst($survey->name) }},</p>
        <p>{{ $survey->message }}</p>

        @foreach (json_decode($survey->survey_id) as $surveyId)
            <a href="{{ route(
                'surveys.response_survey',
                [
                    'surveyId' => $surveyId,
                    'email' => $survey->guest_email,
                    'siteId' => $survey->siteId,
                    'token' => $survey->token,
                ],
                false,
            ) }}"
                class="cta-button">
                Complete Survey {{ $surveyId }}-{{ $survey->siteId }}
            </a>
        @endforeach


        <p>Thank you,</p>
        <p>Your Team</p>
    </div>

    <footer>
        &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
    </footer>
</body>

</html>
