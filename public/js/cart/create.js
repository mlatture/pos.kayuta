function storeCart(barcode = null, productId = null) {
    let data = {};
    if (barcode) {
        data.barcode = barcode;
    } else if (productId) {
        data.product_id = productId;
    }
    $.ajax({
        url: cartStoreUrl,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data,
        success: function(response) {
           
            if(response.response.data.upsell_message){
                localStorage.setItem('upsell_message', response.response.data.upsell_message)
            }
            window.location.reload();
        },
        error: function(reject) {
            if (reject.status === 422) {
                var errors = $.parseJSON(reject.responseText);
                $.each(errors.errors, function(key, val) {
                    $.toast({
                        heading: 'Error',
                        text: val,
                        position: 'top-right',
                        // bgColor: '#FF1356',
                        loaderBg: '#a94442',
                        icon: 'error',
                        hideAfter: 4000,
                        stack: 6
                    });
                });
            }
            if (reject.status === 401) {
                var errors = $.parseJSON(reject.responseText);
                $.toast({
                    heading: 'Error',
                    text: errors.message,
                    position: 'top-right',
                    // bgColor: '#FF1356',
                    loaderBg: '#a94442',
                    icon: 'error',
                    hideAfter: 4000,
                    stack: 6
                });
            }

            if (reject.status === 400) {
                var errors = $.parseJSON(reject.responseText);
                $.each(errors.errors, function(key, val) {
                    $.toast({
                        heading: 'Error',
                        text: val,
                        position: 'top-right',
                        // bgColor: '#FF1356',
                        loaderBg: '#a94442',
                        icon: 'error',
                        hideAfter: 4000,
                        stack: 6
                    });
                });
            }
        }
    });
}
$(document).ready(function(){
    var upsellMessage = localStorage.getItem('upsell_message');
    if(upsellMessage) {
        $("#upsell_message").text(upsellMessage);

        localStorage.removeItem('upsell_message')
    }
})

$(document).on('change', '.product-quantity', function() {
    var productId = $(this).data('id');
    var qty = $(this).val();

    $.ajax({
        url: cartChangeUrl,
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            product_id: productId,
            quantity: qty
        },
        success: function(response) {
            $.toast({
                heading: 'Success',
                text: response.message,
                position: 'top-right',
                // bgColor: '#FF1356',
                loaderBg: '#00c263',
                icon: 'success',
                hideAfter: 2000,
                stack: 6
            });
            window.location.reload();
        },
        error: function(reject) {
            if (reject.status === 422) {
                var errors = $.parseJSON(reject.responseText);
                $.each(errors.errors, function(key, val) {
                    $.toast({
                        heading: 'Error',
                        text: val,
                        position: 'top-right',
                        // bgColor: '#FF1356',
                        loaderBg: '#a94442',
                        icon: 'error',
                        hideAfter: 4000,
                        stack: 6
                    });
                });
            }
            if (reject.status === 401) {
                var errors = $.parseJSON(reject.responseText);
                $.toast({
                    heading: 'Error',
                    text: errors.message,
                    position: 'top-right',
                    // bgColor: '#FF1356',
                    loaderBg: '#a94442',
                    icon: 'error',
                    hideAfter: 4000,
                    stack: 6
                });
            }

            if (reject.status === 400) {
                var errors = $.parseJSON(reject.responseText);
                $.each(errors.errors, function(key, val) {
                    $.toast({
                        heading: 'Error',
                        text: val,
                        position: 'top-right',
                        // bgColor: '#FF1356',
                        loaderBg: '#a94442',
                        icon: 'error',
                        hideAfter: 4000,
                        stack: 6
                    });
                });
            }
        }
    });
});