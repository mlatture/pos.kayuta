$(document).ready(function(){
    $(document).on('click', '#notReserveTable tbody .btn-info', function(){
        console.log($(this).data('id'));
        url = "reservations/payment/" + $(this).data('id');
        window.location = url;
    });

    $(document).on('click', '#reservationTable tbody .btn-info', function(){
        console.log($(this).data('id'));
        url = "reservations/invoice/" + $(this).data('id');
        window.location = url;
    });

   
    $("#transactionType").change(function(){
       
        if($(this).val() == "Cash"){
            $("#cash").show();
            $("#creditcard-manual").hide();
            $("#check").hide();
        }else if($(this).val() == "Check"){
            $("#check").show();
            $("#creditcard-manual").hide();
            $("#cash").hide();
        }else if($(this).val() == "Credit Card - Manual"){
            
            $("#creditcard-manual").show();
            $("#check").hide();
            $("#cash").hide();
        } else {
            $("#creditcard-manual").show();
        }
    })

    $("#xCash, #xAmount").on('input', function() {
      
        var totalText = $('#xAmount').val().replace(/,/g, '');
        var total = parseFloat(totalText) || 0;
    
        var cashText = $(this).val().replace(/,/g, '');
        var cash = parseFloat(cashText) || 0;
    
        var change = cash - total;
    
        $('#xChange').val(change.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    });
    
});
