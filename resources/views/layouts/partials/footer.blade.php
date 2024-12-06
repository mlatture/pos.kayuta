<footer class="main-footer">
    <div class="float-right d-none d-sm-block">

    </div>
    <strong>&copy; <?php echo date('Y'); ?> - WebDaVinci Flow</strong>
</footer>

<script>
    const webdavinci_api = "{{ env('WEBDAVINCI_API') }}";
    const webdavinci_api_key = "{{ env('WEBDAVINCI_API_KEY') }}";


    function get_data() {
        $.ajax({
            url: "get-data",
            method: 'GET',
            dataType: 'json',

            success: function(data) {
               
                push_data(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    function push_data(data) {
        console.log('Pushing Data:', data);
        $.ajax({
            url: `${webdavinci_api}/api/push_data`,
            method: 'POST',
            contentType: "application/json",
            data: JSON.stringify({
                data: data
            }),

            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(r
                    "content"
                ),
                "X-API-KEY": `${webdavinci_api_key}`,
                "X-DOMAIN": window.location.protocol +
                    "//" +
                    window.location.hostname +
                    (window.location.port ?
                        `:${window.location.port}` :
                        ""),
            },
            success: function(response) {
                console.log('Push Data:', response);
            },
            error: function(err) {
                console.log(err);
            }
        });
    }

    get_data();
</script>
