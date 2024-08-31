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
        var selectedValue = $(this).val();

        switch(selectedValue){
            case "Full":
                $("#xAmount").attr('readonly', true);
                break;
            case "Partial":
                $("#xAmount").attr('readonly', false);
                break;
            default:
                console.log('Unexpected transaction type');
                break;
        }
        
    })
    $("#paymentType").change(function() {
        var selectedValue = $(this).val();
    
        switch (selectedValue) {
            case "Cash":
            case "Other":
                $("#cash").show();
                $("#creditcard-manual").hide();
                $("#creditcard-terminal").hide();
                break;
            case "Check":
                $("#creditcard-manual").show();
                $("#xCardNum").attr('placeholder', 'Account Number: ');
                $("#xExp").hide();
                $("#creditcard-terminal").hide();
                $("#cash").hide();
                break;
            case "Manual":
                $("#creditcard-manual").show();
                $("#creditcard-terminal").hide();
                $("#xCardNum").attr('placeholder', 'Card Number: ');
                $("#xExp").show();
                $("#cash").hide();
                break;
            case "Terminal":
                $("#creditcard-terminal").show();
                $("#creditcard-manual").hide();
                $("#cash").hide();
                break;
            default:
                // Optionally handle unexpected values
                console.log('Unexpected payment type');
                break;
        }
    });
    
    
    $("#xCash, #xAmount").on('input', function() {
      
        var totalText = $('#xAmount').val().replace(/,/g, '');
        var total = parseFloat(totalText) || 0;
    
        var cashText = $(this).val().replace(/,/g, '');
        var cash = parseFloat(cashText) || 0;
    
        var change = cash - total;
    
        $('#xChange').val(change.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    });
    
});
