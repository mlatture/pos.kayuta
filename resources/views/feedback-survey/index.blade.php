@extends('layouts.admin')

@section('title', 'Survey Builder')
@section('content-header', 'Survey Builder')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

@section('content')
    <div class="container-fluid mt-4">
        <!-- Vertical Toolbar -->
        <div class="d-flex">
            <div class="vertical-toolbar me-3">
                <button class="btn btn-light d-block mb-2" title="Add New Question" id="addQuestion">
                    <i class="fa fa-plus-circle"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-light d-block mb-2 dropdown-toggle" type="button" id="addAnswerType"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-check"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="addAnswerType">
                        <li><a class="dropdown-item" href="#" data-type="rate">Rating</a></li>
                        <li><a class="dropdown-item" href="#" data-type="yes/no">Yes/No</a></li>
                        <li><a class="dropdown-item" href="#" data-type="comments">Comments</a></li>
                    </ul>
                </div>
                <button class="btn btn-light d-block mb-2" title="Duplicate Question">
                    <i class="fa fa-clone"></i>
                </button>
                <button class="btn btn-light d-block mb-2" title="Add Text">
                    <i class="fa fa-font"></i>
                </button>
                <button class="btn btn-light d-block mb-2" title="Add Image">
                    <i class="fa fa-image"></i>
                </button>
                <button class="btn btn-light d-block mb-2" title="Add Video">
                    <i class="fa fa-video"></i>
                </button>
            </div>
            <div class="flex-grow-1">
                @include('feedback-survey.components.nav-items')
                <div class="tab-content" id="surveyTabsContent">
                    <!-- Questions Tab -->
                    <form action="" id="survey=">
                        <div class="tab-pane fade show active" id="questions" role="tabpanel"
                            aria-labelledby="questions-tab">
                            <div class="row mt-4 justify-content-center">
                                <!-- Question List -->
                                <div class="col-md-9">
                                    <div id="questionContainer" class="d-flex flex-wrap">
                                        <div class="card mb-3 me-3 hover-card" style="width: 300px;" data-id="1">
                                            <div class="card-body">
                                                <h5 class="card-title editable" id="question" contenteditable="true"
                                                    placeholder="Type your question here">
                                                    Question 1
                                                </h5>
                                                <br>
                                                <div class="answer-types mt-3" id="selected-answer-types"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Responses Tab -->
                    @include('feedback-survey.components.responses-tab')

                    <!-- Settings Tab -->
                    @include('feedback-survey.components.settings-tab')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href=" {{ asset('css/feedback-survey.css') }}  ">
@endpush

@push('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let questionCount = 1;

            // Add New Question
            $('#addQuestion').click(function() {
                questionCount++;
                const questionHTML = `
                    <div class="card mb-3 me-3 hover-card" style="width: 300px;" data-id="${questionCount}">
                        <div class="card-body">
                            <h5 class="card-title editable" id="question" contenteditable="true" placeholder="Type your question here">
                            Question ${questionCount}
                            </h5>
                            <div class="answer-types mt-3" id="selected-answer-types"></div>
                        </div>
                    </div>`;
                $('#questionContainer').append(questionHTML);
            });

            // Add Answer Type via Submenu
            $(document).on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                const selectedType = $(this).data('type');
                const selectedQuestion = $('.card.selected');

                if (selectedQuestion.length === 0) {
                    alert('Please select a question to add answer types.');
                    return;
                }

                const questionId = selectedQuestion.attr('data-id');
                const answerTypesContainer = selectedQuestion.find('.answer-types');

                // Check if container already has the type and remove it before adding again
                answerTypesContainer.find(`.answer-type[data-type="${selectedType}"]`).remove();

                let answerHTML = '';
                if (selectedType === 'rate') {
                    // Rating Scale HTML
                    answerHTML = `
                    <div class="answer-type" data-type="rate">
                        <select id="ratingScale${questionId}" class="form-select mb-3">
                            ${[...Array(10).keys()].map(i => `<option value="${i + 1}" ${i + 1 === 5 ? 'selected' : ''}>${i + 1}</option>`).join('')}
                        </select>
                        <div class="rating-stars" id="ratingStars${questionId}">
                            ${[...Array(5).keys()].map(i => `<span data-value="${i + 1}" class="star">&#9734;</span>`).join('')}
                        </div>
                    </div>`;
                } else if (selectedType === 'yes/no') {
                    // Yes/No HTML
                    answerHTML = `
                    <div class="answer-type" data-type="yes/no">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="yesNo${questionId}" id="yesOption${questionId}" value="yes">
                            <label class="form-check-label" for="yesOption${questionId}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="yesNo${questionId}" id="noOption${questionId}" value="no">
                            <label class="form-check-label" for="noOption${questionId}">No</label>
                        </div>
                    </div>`;
                } else if (selectedType === 'comments') {
                    // Comments HTML
                    answerHTML = `
                    <div class="answer-type" data-type="comments">
                        <textarea class="form-control" id="comments${questionId}" rows="3" placeholder="Type your comments here..."></textarea>
                    </div>`;
                }

                // Append the new answer type and sort them
                answerTypesContainer.append(answerHTML);
                sortAnswerTypes(answerTypesContainer);
            });

            // Highlight selected question
            $(document).on('click', '.card', function() {
                $('.card').removeClass('selected');
                $(this).addClass('selected');
            });

            // Handle star ratings
            $(document).on('click', '.rating-stars span', function() {
                const stars = $(this).parent().find('span');
                stars.removeClass('active');
                $(this).addClass('active');
            });

            // Sort answer types in the container based on the order they were added
            function sortAnswerTypes(container) {
                const children = container.children('.answer-type');
                children.sort(function(a, b) {
                    return $(a).data('type') > $(b).data('type') ? 1 : -1;
                });
                container.html(children);
            }

            // Publish Survey
            $('#publish-survey').click(function() {
                const data = [];
                $('#questionContainer .card').each(function() {
                    const questionText = $(this).find('.card-title').text().trim();
                    const answerTypes = [];

                    $(this).find('.answer-types > .answer-type').each(function() {
                        if ($(this).find('select').length) {
                            const rateValue = $(this).find('select').val();
                            answerTypes.push( `rate: ${rateValue}`); 
                        } else if ($(this).find('input:radio').length) {
                            answerTypes.push('radiobutton'); 
                        } else if ($(this).find('textarea').length) {
                            answerTypes.push('comments');
                        }
                    });
                    data.push({
                        questions: questionText,
                        answer_types: answerTypes
                    });
                });

                if (data.length === 0) {
                    alert('No questions to publish!');
                    return;
                }

                const surveyData = {
                    title: 'Survey Title',
                    data: data
                };

                $.ajax({
                    url: ' {{ route('surveys.store') }} ',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content')
                    },
                    contentType: 'application/json',
                    data: JSON.stringify(surveyData),
                    success: function(result) {
                        console.log('Survey published successfully:', result);
                        alert('Survey published successfully!');
                    },
                    error: function(xhr, status, error) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            console.error('Error publishing survey:', xhr.responseJSON.message);
                            alert('Failed to publish the survey: ' + xhr.responseJSON.message);
                        } else {
                            console.error('Error publishing survey:', error);
                            alert('Failed to publish the survey. Please try again.');
                        }
                    },
                    complete: function(response) {
                        if (response.status === 302 && response.responseText) {
                            console.log('Redirect detected to:', response.responseText);
                            alert('Request redirected. Please check server configuration.');
                        }
                    }
                });
            });
        });
    </script>
@endpush
