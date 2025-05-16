$(document).ready(function () {
    // $(".select2").select2();
    $("#customer_id").select2();

    $("#customer_id").on("change", function (e) {
        if (e.target.value === "add_new_user") {
            addUserModal.show();
        }
    });

    // $('.select2').selectize({
    //     sortField: 'text'
    // });

    var isFocused = false;
    var isSelectFocused = false;
    $(document).on("focusin", "input, textarea", function () {
        isFocused = true;
    });
    $(document).on("focusout", "input, textarea", function () {
        isFocused = false;
    });

    $(".select2").on("select2:open", function (e) {
        isSelectFocused = true;
    });
    $(".select2").on("select2:close", function (e) {
        isSelectFocused = false;
    });

    var storeCartTimeout = null;
    $(window).keypress(function (event) {
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
            var currentValue = $("#searchterm").val();
            if (currentValue == null) {
                currentValue = "";
            }
            $("#searchterm").val(currentValue + character);
            storeCartTimeout = setTimeout(function () {
                storeCart(currentValue + character);
            }, 500);
        }
    });

    $(document).on("click", ".product-item", function () {
        var barcode = $(this).data("barcode");
        var productId = $(this).data("id");
        storeCart(barcode, productId);
    });

    $(document).on("click", ".product-delete", function () {
        var productId = $(this).data("id");

        $.ajax({
            url: cartDeleteUrl,
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                product_id: productId,
            },
            success: function (response) {
                $.toast({
                    heading: "Success",
                    text: response.message,
                    position: "top-right",
                    // bgColor: '#FF1356',
                    loaderBg: "#00c263",
                    icon: "success",
                    hideAfter: 2000,
                    stack: 6,
                });
                window.location.reload();
            },
            error: function (reject) {
                if (reject.status === 422) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
                if (reject.status === 401) {
                    var errors = $.parseJSON(reject.responseText);
                    $.toast({
                        heading: "Error",
                        text: errors.message,
                        position: "top-right",
                        // bgColor: '#FF1356',
                        loaderBg: "#a94442",
                        icon: "error",
                        hideAfter: 4000,
                        stack: 6,
                    });
                }

                if (reject.status === 400) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
            },
        });
    });

    $(document).on("click", ".cart-empty", function () {
        $.ajax({
            url: cartEmptyUrl,
            type: "DELETE",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {},
            success: function (response) {
                $.toast({
                    heading: "Success",
                    text: response.message,
                    position: "top-right",
                    // bgColor: '#FF1356',
                    loaderBg: "#00c263",
                    icon: "success",
                    hideAfter: 2000,
                    stack: 6,
                });
                window.location.reload();
            },
            error: function (reject) {
                if (reject.status === 422) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
                if (reject.status === 401) {
                    var errors = $.parseJSON(reject.responseText);
                    $.toast({
                        heading: "Error",
                        text: errors.message,
                        position: "top-right",
                        // bgColor: '#FF1356',
                        loaderBg: "#a94442",
                        icon: "error",
                        hideAfter: 4000,
                        stack: 6,
                    });
                }

                if (reject.status === 400) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
            },
        });
    });

    $(document).on("click", "#categoryButtons .category-btn", function () {
        var category_id = $(this).data('categoryId');
        var category_name = $(this).data('categoryName');

        const url = new URL(window.location);
        url.searchParams.set('category_name', category_name);
        window.history.replaceState({}, '', url);
    
    
        $("#categoryButtons .category-btn")
            .removeClass("btn-primary")
            .addClass("btn-outline-primary");
        $(this)
            .removeClass("btn-outline-primary")
            .addClass("btn-primary");

    
        if (!category_id) {
            $("#category-products").html(`
                <div class="col-12 text-muted text-center">
                    <h5>No Products Available</h5>
                </div>
            `);
            return;
        }
    
    
        $.ajax({
            url: cartCategoryUrl,
            type: "GET",
            data: { category_id: category_id },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                let products = response.data;
                let html = "";
    
                if (products.length > 0) {
                    $.each(products, function (key, val) {
                        if (!val.show_in_category) return;
    
                        let truncatedName = val.name.length > 10
                            ? val.name.substring(0, 10) + "..."
                            : val.name;
    
                        let imagePath = `/storage/products/${val.image}`;
                        let fallbackImageUrl = "/images/product-thumbnail.jpg";
    
                        html += `<div class="col-md-3">
                            <div class="card product-item" data-barcode="${val.barcode}" data-id="${val.id}" title="Product Name: ${val.name}">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ${(val.quantity < 0 || val.quantity === 0) ? '*' : val.quantity}
                                </span>
                                <img src="${imagePath}" class="rounded mx-auto d-block img-fluid"
                                    onerror="this.onerror=null; this.src='${fallbackImageUrl}';">
                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text">${truncatedName}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                    });
                } else {
                    html = `<div class="col-md-6 text-muted">
                                <h5>No Products</h5>
                            </div>`;
                }
    
                $("#category-products").html(html);
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error: function (reject) {
                let message = reject.responseJSON?.message ?? "Error occurred.";
                $.toast({
                    heading: "Error",
                    text: message,
                    position: "top-right",
                    loaderBg: "#a94442",
                    icon: "error",
                    hideAfter: 4000,
                    stack: 6,
                });
            },
        });
    });

    $(document).ready(function () {
        const urlParams = new URLSearchParams(window.location.search);

        const selectedName = urlParams.get('category_name');

        if (selectedName) {
            const button = $(`#categoryButtons .category-btn[data-category-name="${selectedName}"]`);
            if (button.length) {
                button.trigger('click');
            }
        }
    })
    

    $("#search-product").on("input", function () {
        let searchTerm = $(this).val();

        $.ajax({
            url: cartCategoryUrl,
            type: "GET",
            data: { search: searchTerm },
            success: function (response) {
                console.log("Response:", response);
                let productsHtml = "";
                $.each(response.data, function (index, product) {
                    let imagePath = `/images/products/${product.image}`;
                    let fallbackImageUrl = "/images/product-thumbnail.jpg";
                    let imageUrl = imagePath;

                    productsHtml += `
                        <div class="col-md-3" style="cursor: pointer">
                            <div class="card product-item" data-barcode="${
                                product.barcode
                            }"
                                data-id="${
                                    product.id
                                }" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-html="true" title="Product Name: ${
                                    product.name
                                }">
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    ${
                                        ( product.quantity < 0 || product.quantity === 1)
                                            ? 0
                                            : product.quantity
                                    }
                                </span>
                                <img src="${imageUrl}" class="rounded mx-auto d-block img-fluid" alt="Product Image" onError="this.onerror=null; this.src='${fallbackImageUrl}';">
                                <div class="card-body">
                                    <div class="btn-products-container">
                                        <p class="card-text t">${product.name.substring(
                                            0,
                                            10
                                        )}</p>
                                    </div>
                                </div>
                            </div>
                        </div>`;
                });
                $("#product-list").html(productsHtml);
            },
            error: function (xhr) {
                console.error("Failed to fetch products:", xhr);
            },
        });
    });

    $(document).on("click", "#catgories-tab", function () {
        $.ajax({
            url: cartAllCategoryUrl,
            type: "GET",
            success: function (response) {
                let categories = response.response.data;
                if (categories.length > 0) {
                    var html = "";
                    $.each(categories, function (key, val) {
                        html += `<div class="item category-item" data-id="${
                            val.id
                        }}}"><img
                                    src="{{ asset('images/product-thumbnail.jpg') }}"
                                    class="rounded mx-auto d-block" alt="Product Image">
                                <h5>${limitText(val.name, 10)}</h5>
                            </div>`;
                    });
                    $(".category-section").html(html);
                }
            },
            error: function (reject) {
                if (reject.status === 422) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
                if (reject.status === 401) {
                    var errors = $.parseJSON(reject.responseText);
                    $.toast({
                        heading: "Error",
                        text: errors.message,
                        position: "top-right",
                        // bgColor: '#FF1356',
                        loaderBg: "#a94442",
                        icon: "error",
                        hideAfter: 4000,
                        stack: 6,
                    });
                }

                if (reject.status === 400) {
                    var errors = $.parseJSON(reject.responseText);
                    $.each(errors.errors, function (key, val) {
                        $.toast({
                            heading: "Error",
                            text: val,
                            position: "top-right",
                            // bgColor: '#FF1356',
                            loaderBg: "#a94442",
                            icon: "error",
                            hideAfter: 4000,
                            stack: 6,
                        });
                    });
                }
            },
        });
    });

  

    $(document).on("click", ".submit-order", function () {
        let totalAmount = parseFloat($("#total-amount").val().replace(/,/g, ""));
        let totalDiscount = parseFloat(
            $("#total-discount").text().replace(/[^0-9.-]+/g, "")
        );

        
    
        var offcanvas = new bootstrap.Offcanvas(
            document.getElementById("offcanvasOrder")
        );
        if (totalAmount <= 0) {
            $.toast({
                heading: "Error",
                text: "Please add some items to cart!",
                position: "top-right",
                loaderBg: "#FF1356",
                icon: "error",
                hideAfter: 4000,
                stack: 6,
            });
            return;
        }
        offcanvas.show();
    
        $("#offcanvasSubtotal").text(
            parseFloat($("#subtotal-amount").val().replace(/,/g, "")).toFixed(2)
        );


    
        if (totalDiscount > 0) {
            $("#discount-section").show();
            $("#discount-section1").show();
            $("#offcanvasDiscount").text(totalDiscount.toFixed(2));
        } else {
            $("#discount-section").hide();
            $("#discount-section1").hide();
        }
    
        $("#offcanvasTotalAmount").text(totalAmount.toFixed(2));
        $("#offcanvasTax").text(
            parseFloat($("#tax-amount").text().replace(/[^0-9.-]+/g, "")).toFixed(2)
        );
    });
    


   
});
