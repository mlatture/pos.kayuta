<div class="tab-pane fade" id="responses" role="tabpanel" aria-labelledby="responses-tab">
    <div class="mt-4">
        <h5>Survey Responses</h5>
        <div id="survey-container">
            <p>Loading survey responses...</p>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#responses-tab').on('click', function() {
        $.ajax({
            url: "{{ route('surveys.show_responses') }}",
            type: "GET",
            dataType: "json",
            success: function(data) {
                $('#survey-container').html(""); 
                if ($.isEmptyObject(data)) {
                    $('#survey-container').html("<p>No responses available.</p>");
                    return;
                }

                $.each(data, function(surveyId, surveyResponses) {
                    let surveyCard = `
                        <div class="card mb-4">
                            <div class="card-header  text-white" s style="background-color:#16261b">
                                <h6 class="mb-0">Survey ID: ${surveyId}</h6>
                            </div>
                            <div class="card-body">
                    `;

                    let ratingAnalytics = {}; 
                    let radioAnalytics = {};

                    surveyResponses.forEach(response => {
                        let questions = JSON.parse(response.questions); 
                        let answers = JSON.parse(response.answers); 

                        questions.forEach((question, index) => {
                            let answer = answers[index] || {}; 

                            if (!ratingAnalytics[question]) {
                                ratingAnalytics[question] = { totalRatings: 0, ratingCount: 0, comments: [] };
                            }
                            if (!radioAnalytics[question]) {
                                radioAnalytics[question] = { yesCount: 0, noCount: 0, comments: [] };
                            }

                            if (answer.rating !== undefined) {
                                ratingAnalytics[question].totalRatings += answer.rating;
                                ratingAnalytics[question].ratingCount++;
                                if (answer.comment && answer.comment !== "") {
                                    ratingAnalytics[question].comments.push(answer.comment);
                                }
                            }

                            if (answer.radio !== undefined) {
                                if (answer.radio.toLowerCase() === "yes") {
                                    radioAnalytics[question].yesCount++;
                                } else if (answer.radio.toLowerCase() === "no") {
                                    radioAnalytics[question].noCount++;
                                }
                                if (answer.comment && answer.comment !== "") {
                                    radioAnalytics[question].comments.push(answer.comment);
                                }
                            }
                        });
                    });

                    // Display Rating Analytics
                    if (!$.isEmptyObject(ratingAnalytics)) {
                        surveyCard += `<h5 class="text" style="color: #16261b">Rating Analysis</h5>`;
                        $.each(ratingAnalytics, function(question, stats) {
                            let avgRating = stats.ratingCount > 0 ? (stats.totalRatings / stats.ratingCount).toFixed(1) : "No Ratings";
                            
                            surveyCard += `<div class="mb-3">
                                <h6 class="text-secondary">${question}</h6>
                                <ul class="list-unstyled">
                                    <li><strong>Average Rating:</strong> ${avgRating}/5</li>
                                    <li><strong>Total Ratings:</strong> ${stats.ratingCount}</li>
                                </ul>`;
                            
                            if (stats.comments.length > 0) {
                                surveyCard += `<p><strong>Comments:</strong> ${stats.comments.join(", ")}</p>`;
                            }

                            surveyCard += `</div><hr>`;
                        });
                    }

                    // Display Yes/No Analytics
                    if (!$.isEmptyObject(radioAnalytics)) {
                        surveyCard += `<h5 class="text"  style="color: #16261b">Yes/No Response Analysis</h5>`;
                        $.each(radioAnalytics, function(question, stats) {
                            if (stats.yesCount > 0 || stats.noCount > 0) {
                                surveyCard += `<div class="mb-3">
                                    <h6 class="text-secondary">${question}</h6>
                                    <ul class="list-unstyled">
                                        <li><strong>Yes Responses:</strong> ${stats.yesCount}</li>
                                        <li><strong>No Responses:</strong> ${stats.noCount}</li>
                                        <li><strong>Total Responses:</strong> ${stats.yesCount + stats.noCount}</li>
                                    </ul>`;

                                if (stats.comments.length > 0) {
                                    surveyCard += `<p><strong>Comments:</strong> ${stats.comments.join(", ")}</p>`;
                                }

                                surveyCard += `</div><hr>`;
                            }
                        });
                    }

                    surveyCard += `</div></div>`; 
                    $('#survey-container').append(surveyCard);
                });
            },
            error: function() {
                $('#survey-container').html("<p>Error loading survey responses.</p>");
            }
        });
    });
});

</script>

