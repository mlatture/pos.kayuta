<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Survey Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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
            width: 100%;
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

        .star-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .star-row {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }

        .star {
            font-size: 24px;
            color: lightgray;
            margin-right: 5px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .star.gold {
            color: gold;
        }

        .numbers-row {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 5px;
        }

        .number {
            font-size: 14px;
            width: 32px;
            text-align: center;
        }

        textarea {
            width: 100%;
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
                    <div class="survey-card">
                        <div class="survey-card-body">
                            <p ><strong >Question {{ $index + 1 }}:</strong>
                                @php
                                    $questionText = str_replace('<Site Number>', $siteId, $item['questions']);
                                @endphp
                               <span class="questions">
                                {{ $questionText }}
                               </span >
                            </p>
                            <ul class="list-group">
                                @php
                                    $sortedAnswerTypes = collect($item['answer_types'])->sortByDesc(function ($type) {
                                        return str_contains($type, 'rate:') ? 1 : 0;
                                    });
                                @endphp

                                @foreach ($sortedAnswerTypes as $type)
                                    <li class="list-group-item" style="border: none;">
                                        @if (str_contains($type, 'rate:'))
                                            @php
                                                preg_match('/rate: (\d+)/', $type, $matches);
                                                $rateValue = $matches[1] ?? 0;
                                            @endphp
                                            <div class="star-container">
                                                <div class="numbers-row">
                                                    @for ($i = 1; $i <= $rateValue; $i++)
                                                        <div class="number">{{ $i }}</div>
                                                    @endfor
                                                </div>
                                                <div class="star-row">
                                                    @for ($i = 1; $i <= $rateValue; $i++)
                                                        <span class="star" data-rate="{{ $i }}"
                                                            onclick="handleRate(this, '{{ $index }}')">&#9733;</span>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating_{{ $index }}"
                                                    id="hidden-rating-{{ $index }}" value="">
                                            </div>
                                        @elseif ($type === 'radiobutton')
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="question_{{ $index }}"
                                                    id="radiobutton_{{ $index }}_yes" value="yes">
                                                <label class="form-check-label"
                                                    for="radiobutton_{{ $index }}_yes">Yes</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio"
                                                    name="question_{{ $index }}"
                                                    id="radiobutton_{{ $index }}_no" value="no">
                                                <label class="form-check-label"
                                                    for="radiobutton_{{ $index }}_no">No</label>
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
                    <button class="btn-submit" id="btn-submit-answers">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function handleRate(element, questionIndex) {
            const stars = element.parentElement.querySelectorAll('.star');
            const rateValue = element.getAttribute('data-rate');

            stars.forEach(star => {
                star.classList.remove('gold');
                if (star.getAttribute('data-rate') <= rateValue) {
                    star.classList.add('gold');
                }
            });

            $(`#hidden-rating-${questionIndex}`).val(rateValue);
        }
        $('#btn-submit-answers').click(function() {
            const data = [];

            $('.survey-card').each(function() {
                const questionText = $(this).find('.questions').text().trim();
                const answerObj = {};

                $(this).find('.list-group-item').each(function () {
           
                const rateValue = $(this).find('.star.gold').last().data('rate'); 
                if (rateValue) {
                    answerObj['rating'] = parseInt(rateValue, 10);
                }

           
                const radioValue = $(this).find('input:radio:checked').val();
                if (radioValue) {
                    answerObj['radio'] = radioValue;
                }

           
                const commentText = $(this).find('textarea').val();
                if (commentText && commentText.trim() !== '') {
                    answerObj['comment'] = commentText.trim();
                }
            });

                if (Object.keys(answerObj).length > 0) {
                    data.push({
                        question: questionText,
                        answers: answerObj,
                    });
                   
                }
             
            });

           
            console.log('Survey Data:', data);

            $.ajax({
                url: " {{ route('surveys.store_responses') }} ",
                type: 'POST',
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                },
                contentType: 'application/json',
                data: JSON.stringify({ 
                    survey: data,
                    email: '{{ $email }}',
                    siteId: '{{ $siteId }}',
                    surveyId: '{{ $surveyId }}',
                    token: '{{ $token }}',
                }),
                success: function (response) {
                   window.location.reload();
                },
                error: function (error) {
                    alert('An error occurred. Please try again.');
                },
            });
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
