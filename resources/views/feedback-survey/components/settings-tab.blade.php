<div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
    <div class="mt-4">
      
        <div id="survey-cards" class="row row-cols-1 row-cols-md-2 g-4">
        </div>
    </div>
</div>

<script>
    function renderSurveyCards(surveys) {
        const container = document.getElementById("survey-cards");
        container.innerHTML = ""; 

        surveys.forEach(survey => {
            const card = document.createElement("div");
            card.className = "col";
            card.innerHTML = `
                <div class="card h-100">
                    <div class="card-body position-relative">
                        <h5 class="card-title">${survey.title}</h5>
                        <p class="card-text">${survey.questions ? JSON.parse(survey.questions).join('<br>') : "No description available"}</p>
                        <div class="form-check form-switch position-absolute top-0 end-0 m-3">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                id="toggle-${survey.id}" 
                                ${survey.active ? "checked" : ""}
                                onchange="updateSurveyStatus(${survey.id}, this.checked)"
                            >
                            <label class="form-check-label" for="toggle-${survey.id}">Active</label>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    function updateSurveyStatus(id, isActive) {
        $.ajax({
            url: "{{ route('surveys.update_status') }}",
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { 
                id: id, 
                isActive: isActive ? 1 : 0 
            },
            success: function (result) {
                console.log('Survey status updated successfully:', result);
                alert('Survey status updated successfully!');
            },
            error: function (xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    console.error('Error updating survey status:', xhr.responseJSON.message);
                    alert('Failed to update the survey status: ' + xhr.responseJSON.message);
                } else {
                    console.error('Error updating survey status:', error);
                    alert('Failed to update the survey status. Please try again.');
                }
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        $.ajax({
            url: "{{ route('surveys.get_published_survey') }}",
            type: 'GET',
            success: function(data) {
                console.log(data);
                renderSurveyCards(data);
            },
            error: function(xhr, status, error) {
                console.error('Error fetching surveys:', error);
            }
        });
    });
</script>