$(document).ready(function() {
    var currentPage = 1;
    $('.secondpage-modal').hide();
    $('.thirdpage-modal').hide();
    $('#backInfo').hide();
    $('#submitReservations').hide();
    $('#nextInfo').on('click', function(){
        var fromDate = $('#fromDate').val();
        var toDate = $('#toDate').val();
        var fname = $('#fname').val();
        var lname = $('#lname').val();
        var email = $('#email').val();
        var siteclass = $('#siteclass').val();
        var riglength = $('#riglength').val();
        var hookup = $('#hookup').val();

        sessionStorage.setItem('fromDate', fromDate);
        sessionStorage.setItem('toDate', toDate);
        sessionStorage.setItem('fname', fname);
        sessionStorage.setItem('lname', lname);
        sessionStorage.setItem('email', email);
        sessionStorage.setItem('siteclass', siteclass);
        sessionStorage.setItem('riglength', riglength);
        sessionStorage.setItem('hookup', hookup);

        $('.firstpage-modal').fadeOut(400, function(){
            $('#closeModal').hide();
            $('.secondpage-modal').fadeIn(400, function(){
                $('#backInfo').show();
                // $('.thirdpage-modal').hide();
                $('#nextInfo').hide();
                $('#submitReservations').show();
            });
        });

        // $('.secondpage-modal').fadeOut(400, function(){
        //     $('#closeModal').hide();
        //     $('.thirdpage-modal').fadeIn(400, function(){
                
        //     })
        // })
    });

    


    $('#backInfo').on('click', function(){
        $('.secondpage-modal').fadeOut(400, function(){
            $('#backInfo').hide();
          
        $('.firstpage-modal').fadeIn(400, function(){
               
                $('#closeModal').show();
            });
        });
    });

  


  

    function loadSites() {  
        $.ajax({
            type: "GET",
            url: "getsite",
            dataType: "json",
            cache: false,
            success: function(data) {
                $("#siteSelector").empty();
                $("#siteSelector").append('<option value="" disabled selected>Select a Site</option>');
                $.each(data, function(index, item) {
                    if (
                        $('#siteSelector option[value="' + item.id + '"]')
                            .length == 0
                    ) {
                        $("#siteSelector").append(
                            `<option value="${item.siteid}">${item.siteid}</option>`
                        );
                    }
                });
            }
        })
    }
    
    function loadCustomers() {
        $.ajax({
            type: "GET",
            url: "getcustomers",
            dataType: "json",
            cache: false,
            success: function(data) {
                $("#customerSelector").empty();
                $("#customerSelector").append('<option value="" disabled selected>Select a Customer</option>');

                $.each(data, function(index, item) {
                    if (
                        $('#customerSelector option[value="' + item.id + '"]')
                            .length == 0
                    ) {
                        $("#customerSelector").append(
                            `<option value="${item.id}" data-fname="${item.first_name}" data-lname="${item.last_name}" data-email="${item.email}">${item.first_name} ${item.last_name}</option>`
                        );
                    }
                });
            }
        });
    }

    $("#customerSelector").change(function() {
        var selectedOption = $(this).find("option:selected");
        var fname = selectedOption.data("fname");
        var lname = selectedOption.data("lname");
        var email = selectedOption.data("email");
        $("#fname").val(fname);
        $("#lname").val(lname);
        $("#email").val(email);
    });

    function loadSiteClasses() {
        $.ajax({
            type: "GET",
            url: "getsiteclasses",
            dataType: "json",
            cache: false,
            success: function(data) {
                $("#siteclass").empty();
    
                $("#siteclass").append('<option value="" disabled selected>Select a Site Class</option>');
    
                $.each(data, function(index, item) {
                    if ($('#siteclass option[value="' + item.id + '"]').length == 0) {
                        $("#siteclass").append(
                            `<option value="${item.id}" data-siteclass='${item.siteclass}'>${item.siteclass}</option>`
                        );
                    }
                });
            }
        });
    }

    $('#siteclass').on('change', function() {
        var selectedOption = $(this).find("option:selected");
        var siteclass = selectedOption.data("siteclass");
        $("#siteclasses").val(siteclass);
    });

    function loadSiteHookups() {
        $.ajax({
            type: "GET",
            url: "getsitehookups",
            dataType: "json",
            cache: false,
            success: function(data) {
               $('#hookup').empty();
               $('#hookup').append('<option value="" disabled selected>Select a Site Hookup</option>');
                $.each(data, function(index, item) {
                    if (
                        $('#hookup option[value="' + item.id + '"]').length == 0
                    ) {
                        $("#hookup").append(
                            `<option value="${item.id}" data-sitehookup='${item.sitehookup}'>${item.sitehookup}</option>`
                        );
                    }
                });
            }
        });
    }

    $('#hookup').on('change', function() {
        var selectedOption = $(this).find("option:selected");
        var siteclass = selectedOption.data("sitehookup");
        $("#hookups").val(siteclass);
    });

    $('#siteclass').on('change', function() {
        var selectedValue = $(this).val();
        console.log("Selected site class value:", selectedValue);
        if ($(this).val() != '1') {
            $('#forRv').hide();
        } else {
          
           $('#forRv').show();
        }
    });

    $('#siteclass').trigger('change');

 
 

    setInterval(function() {
        loadSites();
        loadCustomers();
        loadSiteClasses();
        loadSiteHookups();
    }, 5000);

});
//Customers


