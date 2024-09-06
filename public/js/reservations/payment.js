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
            
            $("#cash, #creditcard-manual, #creditcard-terminal, #gift-card").hide();
            $("#xExpGroup").show(); 
            
            switch (selectedValue) {
                case "Cash":
                case "Other":
                    $("#cash").show();
                    break;
                case "Check":
                    $("#creditcard-manual").show();
                    $("#xCardNum").attr('placeholder', 'Account Number');
                    $("#xExpGroup").hide();
                    break;
                case "Manual":
                    $("#creditcard-manual").show();
                    $("#xCardNum").attr('placeholder', 'Card Number');
                    break;
                case "Terminal":
                    $("#creditcard-terminal").show();
                    break;
                case "Gift Card":
                    $("#gift-card").show();
                    break;
                default:
                    console.log('Unexpected payment type');
            }
        });

        $("#xBarcode").on('input', function(){
            var xBarcode = $(this).val();

            if(xBarcode.length > 0){
                $.ajax({
                    url: checkGiftCart,
                    method: 'GET',
                    data: { barcode: xBarcode },
                    success: function(response){
                        if (response.exists) {
                            $('#gift-card-message').text('Gift card found: ' + response.data.amount + ' available.');
                        } else {
                            $('#gift-card-message').text('Gift card not found.');
                        }
                    
                    },
                    error: function(xhr, status, error){
                        alart('Error checking gift card', error)
                    }
                })
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
