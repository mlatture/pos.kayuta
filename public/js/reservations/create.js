$('#saveCustomer').click(function() {
    var formData = new FormData($('#customerForm')[0]);

    $.ajax({
        url: 'postcustomer',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
            toastr.success('Customer added successfully'); 
        },
        error: function(xhr){
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, value) {
                    toastr.error(value[0]); 
                });
            }
        }
    });
});

$('#submitReservations').click(function() {
    var formDataReservation = new FormData($('#dateRangeForm')[0]);

    $.ajax({
        url: 'postinfo',
        type: 'POST',
        data: formDataReservation,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
            toastr.success('Information added successfully'); 
        },
        error: function(xhr){
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(key, value) {
                    toastr.error(value[0]); 
                });
            }
        }
    })
})
