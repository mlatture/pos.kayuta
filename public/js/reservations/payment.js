$(document).ready(function(){
    $(document).on('click', '#notReserveTable tbody .btn-info', function(){
        console.log($(this).data('id'));
        url = "reservations/payment/" + $(this).data('id');
        window.location = url;
    });

   
    $("#transactionType").change(function(){
       
        if($(this).val() == "Cash"){
            $("#cash").show();
            $("#creditcard").hide();
        }else{
            $("#creditcard").show();
            $("#cash").hide();
        }
    })

 
   
});
