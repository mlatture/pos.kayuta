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
                <button class="btn btn-light d-block mb-2 dropdown-toggle" type="button" id="addAnswerType" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-check"></i>
                </button>
                <ul class="dropdown-menu" aria-labelledby="addAnswerType">
                    <li><a class="dropdown-item" href="#" data-type="rate">Rating</a></li>
                    <li><a class="dropdown-item" href="#" data-type="yesno">Yes/No</a></li>
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
        <!-- Tabbed Navigation Bar -->
        <div class="flex-grow-1">
            <ul class="nav nav-tabs" id="surveyTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="questions-tab" data-bs-toggle="tab" data-bs-target="#questions" type="button" role="tab" aria-controls="questions" aria-selected="true">Questions</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="responses-tab" data-bs-toggle="tab" data-bs-target="#responses" type="button" role="tab" aria-controls="responses" aria-selected="false">Responses</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Settings</button>
                </li>
            </ul>
            <div class="tab-content" id="surveyTabsContent">
                <!-- Questions Tab -->
                <div class="tab-pane fade show active" id="questions" role="tabpanel" aria-labelledby="questions-tab">
                    <div class="row mt-4 justify-content-center">
                        <!-- Question List -->
                        <div class="col-md-9">
                            <div id="questionContainer" class="d-flex flex-wrap">
                                <div class="card mb-3 me-3 hover-card" style="width: 300px;" data-id="1">
                                    <div class="card-body">
                                        <h5 class="card-title editable" contenteditable="true" placeholder="Type your question here">
                                            Question 1
                                        </h5>
                                        <div class="answer-types mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Responses Tab -->
                <div class="tab-pane fade" id="responses" role="tabpanel" aria-labelledby="responses-tab">
                    <div class="mt-4">
                        <h5>Survey Responses</h5>
                        <p>Response data will be displayed here.</p>
                    </div>
                </div>

                <!-- Settings Tab -->
                <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                    <div class="mt-4">
                        <h5>Survey Settings</h5>
                        <p>Settings options will be displayed here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .vertical-toolbar {
        width: 50px;
        background: #f8f9fa;
        border-radius: 10px;
        padding: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: sticky;
        top: 10px;
        height: fit-content;
    }
    .vertical-toolbar button {
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: none;
        background: none;
        transition: background 0.2s;
    }
    .vertical-toolbar button:hover {
        background: #e9ecef;
        border-radius: 50%;
    }
    .vertical-toolbar i {
        font-size: 20px;
    }
    #questionContainer {
        gap: 15px;
    }
    .editable {
        border: 1px solid transparent;
        cursor: text;
        padding: 5px;
        border-radius: 4px;
        transition: border-color 0.3s, background-color 0.3s;
    }
    .editable:focus {
        outline: none;
        border-color: #007bff;
        background-color: #f8f9fa;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }
    .editable::placeholder {
        color: #6c757d;
    }
    .hover-card {
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    .card.selected {
        border: 2px solid #007bff;
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
    }
    .rating-stars {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
    }
    .rating-stars span {
        cursor: pointer;
        font-size: 24px;
        color: #ccc;
        transition: color 0.3s;
    }
    .rating-stars span:hover,
    .rating-stars span.active {
        color: #ffcc00;
    }
</style>
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
                        <h5 class="card-title editable" contenteditable="true" placeholder="Type your question here">
                          Question ${questionCount}
                        </h5>
                        <div class="answer-types mt-3"></div>
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

            if (selectedType === 'rate') {
                const ratingHTML = `
                    <div>
                        <select id="ratingScale${questionId}" class="form-select mb-3">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5"selected>5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>                          
                            <option value="10">10</option>
                                                    
                        </select>
                        <div class="rating-stars">
                            ${[...Array(10).keys()].map(i => `<span data-value="${i + 1}">&#9734;</span>`).join('')}
                        </div>
                    </div>`;
                selectedQuestion.find('.answer-types').html(ratingHTML);

                // Handle scale change
                $(`#ratingScale${questionId}`).change(function() {
                    const scale = $(this).val();
                    const stars = [...Array(parseInt(scale)).keys()].map(i => `<span data-value="${i + 1}">&#9734;</span>`).join('');
                    selectedQuestion.find('.rating-stars').html(stars);
                });
            } else {
                const answerTypeHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${selectedType}" id="${selectedType}Option${questionId}">
                        <label class="form-check-label" for="${selectedType}Option${questionId}">${selectedType.charAt(0).toUpperCase() + selectedType.slice(1)}</label>
                    </div>`;
                selectedQuestion.find('.answer-types').append(answerTypeHTML);
            }
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
    });
</script>
@endpush