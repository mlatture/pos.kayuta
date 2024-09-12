$(document).ready(function(){
    $("#collapsePlanner").on('shown.bs.collapse', function () {
        loadSites();
        loadSiteClasses();
       

    });
    function loadSites() {  
        $.ajax({
            type: "GET",
            url: "getsite",
            dataType: "json",
            cache: false,
            success: function(data) {
                $("#siteSelectors").empty();
                $("#siteSelectors").append('<option value="" disabled selected>Select a Site</option>');
                $("#siteSelectors").append('<option value="">All Sites</option>');

                $.each(data, function(index, item) {
                    if (
                        $('#siteSelector option[value="' + item.id + '"]')
                            .length == 0
                    ) {
                        $("#siteSelectors").append(
                            `<option value="${item.siteid}" data-siteid="${item.siteid}">${item.siteid}</option>`
                        );
                    }
                });
            }
        })
    }

    function loadSiteClasses() {
        $.ajax({
            type: "GET",
            url: "getsiteclasses",
            dataType: "json",
            cache: false,
            success: function(data) {
                let typeSelect = $("#type");
                typeSelect.empty();
    
              
                typeSelect.append('<option></option>');
    
                typeSelect.append('<option value="">All Types</option>');

    
                $.each(data, function(index, item) {
                    if (typeSelect.find(`option[value="${item.siteclass}"]`).length == 0) {

                        typeSelect.append(
                            `<option value="${item.siteclass}">${item.siteclass}</option>`
                        );
                    }
                });
    
             
                typeSelect.select2({
                    placeholder: "Select a Site Class",
                    allowClear: true,
                    width: '100%'
                });
    
                typeSelect.trigger('change');
            }
        });
    }
    


    // $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker){
        
    //     let startDate = picker.startDate.format('YYYY-MM-DD');
    //     let endDate = picker.endDate.format('YYYY-MM-DD');

    //     fetchReservations(1,10,'', [], startDate, endDate);
        
    // })
})