$(document).ready(function() {
    // $(".select2").select2();
    $("#customer_id").select2();

    $("#customer_id").on('change', function(e) {
        if (e.target.value === 'add_new_user') {
            addUserModal.show();
        }
    });


    // $('.select2').selectize({
    //     sortField: 'text'
    // });

    var isFocused = false;
    var isSelectFocused = false;
    $(document).on('focusin', 'input, textarea', function() {
        isFocused = true;
        console.log('focused');
    });
    $(document).on('focusout', 'input, textarea', function() {
        isFocused = false;
        console.log('unfocused');
    });

    $(".select2").on('select2:open', function(e) {
        isSelectFocused = true;
    });
    $(".select2").on('select2:close', function(e) {
        isSelectFocused = false;
    });

    var storeCartTimeout = null;
    $(window).keypress(function(event) {
        if (!isFocused && !isSelectFocused) {
            if (storeCartTimeout) {
                clearTimeout(storeCartTimeout);
            }
            // Process barcode input if no form field element is focused
            var barcode = String.fromCharCode(event.which);
            // Do something with the barcode value
            var code = event.which || event.keyCode;
            var character = String.fromCharCode(code);
            barcode += character;
            var currentValue = $('#searchterm').val();
            if (currentValue == null) {
                currentValue = '';
            }
            $('#searchterm').val(currentValue + character);
            storeCartTimeout = setTimeout(function() {
                storeCart(currentValue + character);
            }, 500)
        }
    });

    $(document).on('click', '.product-item', function() {
        var barcode = $(this).data('barcode');
        var productId = $(this).data('id');
        storeCart(barcode, productId);
    });

  

    $(document).on('click', '.product-delete', function() {
        var productId = $(this).data('id');

        $.ajax({
            url: cartDeleteUrl,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                product_id: productId,
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

    $(document).on('click', '.cart-empty', function() {
        $.ajax({
            url: cartEmptyUrl,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {},
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

    $(document).on('click', '.category-item', function(e) {
        e.preventDefault(); 
        var category_id = $(this).data('id');
        console.log(category_id);
    
        $.ajax({
            url: cartCategoryUrl,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                category_id: category_id,
            },
            success: function(response) {
    
                let products = response.data;  
                
                let html = '';
    
                if (products.length > 0) {
                    html += '<div class="row">';
            
                    $.each(products, function(key, val) {
                        let truncatedName = val.name.length > 10 ? val.name.substring(0, 10) + '...' : val.name;
            
                        let imagePath = `/storage/products/${val.image}`;
                        let fallbackImageUrl = '/images/product-thumbnail.jpg'; 
                        let imageUrl = imagePath; 
            
                        html += `<div class="col-md-3">
                            <div class="card product-item" data-barcode="${val.barcode}" data-id="${val.id}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="Product Name: ${val.name}">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ${val.quantity < 0 ? 0 : val.quantity}
                                </span>
                                <img src="${imageUrl}" class="rounded mx-auto d-block img-fluid" alt="Product Image" onError="this.onerror=null; this.src='${fallbackImageUrl}';">
                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text">${truncatedName}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
            
                    html += '</div>';
            
                    $(".category-section").html(html);
                } else {
                    html = `<div class="col-md-6">
                                <h2 class="font-weight-lighter">
                                    No Products
                                </h2>
                            </div>`;
                    $(".category-section").html(html);
                }
            
                $('[data-bs-toggle="tooltip"]').tooltip(); 
            },
            
            error: function(reject) {
                if (reject.status === 422) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function(key, val) {
                        $.toast({
                            heading: 'Error',
                            text: val,
                            position: 'top-right',
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
    

    $('#search-product').on('input', function() {
        let searchTerm = $(this).val();
   
        $.ajax({
            url: cartCategoryUrl, 
            type: 'GET',
            data: { search: searchTerm },
            success: function(response) {
                console.log('Response:', response); 
                let productsHtml = '';
                $.each(response.data, function(index, product) {
                 
                    let imagePath = `/storage/products/${product.image}`;
                    let fallbackImageUrl = '/images/product-thumbnail.jpg'; 
                    let imageUrl = imagePath; 
                    
                    productsHtml += `
                        <div class="col-md-3" style="cursor: pointer">
                            <div class="card product-item" data-barcode="${product.barcode}"
                                data-id="${product.id}" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-html="true" title="Product Name: ${product.name}">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ${product.quantity < 0 ? 0 : product.quantity}
                                </span>
                                <img src="${imageUrl}" class="rounded mx-auto d-block img-fluid" alt="Product Image" onError="this.onerror=null; this.src='${fallbackImageUrl}';">
                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text t">${product.name.substring(0, 10)}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });
                $('#product-list').html(productsHtml);
            },
            error: function(xhr) {
                console.error('Failed to fetch products:', xhr);
            }
        });
    });
    
    $(document).on('click', '#catgories-tab', function() {
        $.ajax({
            url: cartAllCategoryUrl,
            type: 'GET',
            success: function(response) {
                let categories = response.response.data;
                if (categories.length > 0) {
                    var html = '';
                    $.each(categories, function(key, val) {
                        html += `<div class="item category-item" data-id="${val.id}}}"><img
                                    src="{{ asset('images/product-thumbnail.jpg') }}"
                                    class="rounded mx-auto d-block" alt="Product Image">
                                <h5>${limitText(val.name, 10)}</h5>
                            </div>`;
                    });
                    $(".category-section").html(html);
                }
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

    $(document).on('click', '.submit-order', function() {
        var totalAmount = $("#total-amount").val();
        Swal.fire({
            title: "Enter Order Amount",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Submit",
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (!value) {
                    return "Please Enter Amount!";
                }
            },
            // showLoaderOnConfirm: true,
            // preConfirm: async (amount) => {

            // },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {

                let amount = result.value;
                let customer_id = $('#customer_id').val();
                let gift_card_id = $('#gift_card_id').val();
                let gift_card_discount = $('#gift_card_discount').val();

                let change = totalAmount - amount;
                if ((change) < 0) {
                    change = change * -1;
                } else {
                    change = 0;
                }

                Swal.fire({
                    title: "Change is : " + change.toFixed(2) +
                        ". Do you want to proceed?",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: "Save",
                    denyButtonText: `Don't save`,
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            $.ajax({
                                url: cartOrderStoreUrl,
                                type: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $(
                                        'meta[name="csrf-token"]'
                                    ).attr('content')
                                },
                                data: {
                                    amount: amount,
                                    customer_id: customer_id,
                                    gift_card_id: gift_card_id,
                                    gift_card_discount: gift_card_discount
                                },
                                success: function(response) {
                                    resolve(response);
                                },
                                error: function(reject) {
                                    // Error handling code
                                    resolve(reject);
                                }
                            });
                        });
                    }
                }).then((result) => {
                   
                    if (result.isConfirmed && result.value) {
                        $.toast({
                            heading: 'Success',
                            text: result.value.message,
                            position: 'top-right',
                            loaderBg: '#00c263',
                            icon: 'success',
                            hideAfter: 2000,
                            stack: 6
                        });
                        window.location.reload();
                    } else if (result.isDenied) {
                        Swal.fire("Changes are not saved", "", "info");
                    }
                });


                // Swal.fire({
                //     title: `${result.value.login}'s avatar`,
                //     imageUrl: result.value.avatar_url
                // });
            }
        });
    });

    $(document).on('click', '.apply-gift-card', function() {
        var customer_id = $("#customer_id").val();

        Swal.fire({
            title: "Enter Gift Card",
            input: "text",
            inputAttributes: {
                autocapitalize: "off"
            },
            showCancelButton: true,
            confirmButtonText: "Submit",
            showLoaderOnConfirm: true,
            inputValidator: (value) => {
                if (!value) {
                    return "Please Enter Gift Card!";
                }
            },
            preConfirm: async (code) => {
                try {
                    const apiUrl = giftCard;

                    const requestBody = {
                        customer_id: customer_id,
                        code: code
                    };

                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': $(
                                'meta[name="csrf-token"]'
                            ).attr('content')
                        },
                        body: JSON.stringify(requestBody),
                    });

                    if (!response.ok) {
                        const responseJson = await response.json()
                        console.log(responseJson);
                        return Swal.showValidationMessage(`${responseJson.message}`);
                    }
                    return response.json();

                } catch (error) {
                    Swal.showValidationMessage(`
                        Request failed: ${error}
                    `);
                }
       
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const response = result.value;
                console.log(response);
                $('#gift_card_discount').val(response.response.data.gift_card_discount);
                $('#gift_card_id').val(response.response.data.gift_card.id);
                $('.show-gift-discount').text('$ ' + response.response.data
                    .gift_card_discount);
                var totalamount = $('#subtotal-amount').val() - response.response.data
                    .gift_card_discount;
                $('#total-amount').val(totalamount.toFixed(2));
                $('.show-total-amount').text('$ ' + totalamount.toFixed(2));

                console.log(result);
                $.toast({
                    heading: 'Success',
                    text: result.value.message,
                    position: 'top-right',
                    loaderBg: '#00c263',
                    icon: 'success',
                    hideAfter: 2000,
                    stack: 6
                });

            } else if (result.isDenied) {
                Swal.fire("Changes are not saved", "", "info");
            }
        });
    });

});