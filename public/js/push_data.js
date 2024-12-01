$.ajax({
    url: `${getData}`,
    type: 'GET',
    dataType: 'JSON',
    success: function(response) {
        console.log('Get Data', response);
        pushTheData(response);
    },
    error: function(response) {
        console.log(response);
    }
});

function pushTheData(response){
    
    var resData = response.data;
    $.ajax({
        url: `${webdavinci_api}/api/push_data`,
        type: 'POST',
        dataType: "json",
        contentType: "application/json",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            "X-API-KEY": `${webdavinci_api_key}`,
            "X-DOMAIN":
                window.location.protocol +
                "//" +
                window.location.hostname +
                (window.location.port
                    ? `:${window.location.port}`
                    : ""),
            
        },
        data: JSON.stringify({ data: resData }),
        success: function(data) {
            console.log('Push Data', data);
        },
        error: function(data) {
            console.log(data);
        }
    })


}