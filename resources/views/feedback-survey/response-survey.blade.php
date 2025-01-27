<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4fff4;
            font-family: Arial, sans-serif;
        }

        .survey-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .survey-card {
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 600px;
            margin-bottom: 20px;
        }

        .survey-card-header {
            background-color: #2e7d32;
            color: white;
            padding: 15px;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        .survey-card-body {
            padding: 20px;
        }

        .btn-submit {
            background-color: #2e7d32;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            margin-top: 20px;
        }

        .btn-submit:hover {
            background-color: #256c28;
        }

        .star {
            font-size: 24px;
            color: gold;
            margin-right: 2px;
        }
    </style>
</head>

<body>
    <div class="survey-container">
        <div class="survey-card">
            <div class="survey-card-header">
                Survey Form
            </div>
            <div class="survey-card-body">

                @foreach ($result as $index => $item)
                    <div class="survey-card mb-3">
                        <div class="survey-card-body">
                            <p><strong>Question {{ $index + 1 }}:</strong> {{ $item['questions'] }}</p>
                            <ul class="list-group">
                                @foreach ($item['answer_types'] as $type)
                                    <li class="list-group-item" style="border: none;">
                                        @if (str_contains($type, 'rate'))
                                            <div>
                                                @php
                                                    // Extract the number inside square brackets from rate:[x]
                                                    preg_match('/rate:\\[(\\d+)\\]/', $type, $matches);
                                                    $rateValue = $matches[1] ?? 0; // Default to 0 if no match
                                                @endphp
                                                <label>
                                               
                                                    @for ($i = $rateValue + 1; $i <= 5; $i++)
                                                        <span class="star">&#9734;</span>
                                                    @endfor
                                                </label>
                                            </div>
                                        @elseif ($type === 'radiobutton')
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="question_{{ $index }}"
                                                    id="radiobutton_{{ $loop->index }}" value="yes">
                                                <label class="form-check-label"
                                                    for="radiobutton_{{ $loop->index }}">Yes</label>
                                                <input class="form-check-input" type="radio"
                                                    name="question_{{ $index }}"
                                                    id="radiobutton_{{ $loop->index }}_no" value="no">
                                                <label class="form-check-label"
                                                    for="radiobutton_{{ $loop->index }}_no">No</label>
                                            </div>
                                        @elseif ($type === 'comments')
                                            <div>
                                                <textarea class="form-control" name="comments_{{ $index }}" rows="3" placeholder="Add your comments here"></textarea>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endforeach




                <div class="text-center">
                    <button class="btn-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
