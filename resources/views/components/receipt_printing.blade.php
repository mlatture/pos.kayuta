<div class="modal fade" id="receiptPrintingModal" tabindex="-1" aria-labelledby="receiptPrintingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptPrintingModalLabel">Receipt Printing</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Upload Header Logo -->
                <div class="mb-3">
                    <label for="logoUpload" class="form-label">Upload Header Logo</label>
                    <input type="file" class="form-control" id="logoUpload">
                </div>

                <!-- Header Text -->
                <div class="mb-3">
                    <label for="headerText" class="form-label">Header Text</label>
                    <input type="text" class="form-control" id="headerText" placeholder="Enter header text">
                </div>

                <!-- Receipt Preview -->
                <div class="border p-3" id="receiptPreview">
                    <img id="logoPreview" src="" alt="Header Logo" class="d-block mx-auto mb-3"
                        style="max-width: 100px; display: none;">
                    <p id="headerTextPreview" class="text-center"></p>
                    <hr>
                    <p>Receipt content goes here...</p>
                    <hr>
                    <p id="footerTextPreview" class="text-center"></p>
                </div>

                <!-- Footer Text -->
                <div class="mb-3">
                    <label for="footerText" class="form-label">Footer Text</label>
                    <input type="text" class="form-control" id="footerText" placeholder="Enter footer text">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveSettings">Save</button>

            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {

        // loadStoredSettings();

        $("#logoUpload").change(function() {
            let file = this.files[0];
            let formData = new FormData();
            formData.append("logo", file);
            formData.append("_token", "{{ csrf_token() }}");

            $.ajax({
                url: '{{ route('receipt.upload.logo') }}',
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        let timestampedFilename = response.filename;
                        localStorage.setItem("receiptLogo", timestampedFilename);
                        $("#logoPreview").attr("src", "/storage/receipt_logos/" +
                            timestampedFilename).show();
                    }
                }
            });
        });

        $("#saveSettings").click(function() {
            // let headerText = $("#headerText").val();
            // let footerText = $("#footerText").val();

            // localStorage.setItem("receiptHeaderText", headerText);
            // localStorage.setItem("receiptFooterText", footerText);

            // $("#headerTextPreview").text(headerText);
            // $("#footerTextPreview").text(footerText);

            // $.toast({
            //     heading: 'Success',
            //     text: "Settings saved successfully",
            //     position: 'top-right',
            //     loaderBg: '#00c263',
            //     icon: 'success',
            //     hideAfter: 2000,
            //     stack: 6
            // });

            // setTimeout(function () {
            //     $("#receiptPrintingModal").modal("hide");
            // }, 1000);

            let headerText = $("#headerText").val();
            let footerText = $("#footerText").val();
            let logo = localStorage.getItem("receiptLogo");

            $.post('{{ route('receipt.save.settings') }}', {
                _token: '{{ csrf_token() }}',
                logo: logo,
                headerText: headerText,
                footerText: footerText
            }, function(response) {
                console.log(response);
                $.toast({
                    heading: 'Success',
                    text: "Settings saved successfully",
                    position: 'top-right',
                    loaderBg: '#00c263',
                    icon: 'success',
                    hideAfter: 2000,
                    stack: 6
                });

                setTimeout(function() {
                    $("#receiptPrintingModal").modal("hide");
                }, 1000);
            });



        });


        // function loadStoredSettings() {
        //     let storedLogo = localStorage.getItem("receiptLogo");
        //     let storedHeaderText = localStorage.getItem("receiptHeaderText");
        //     let storedFooterText = localStorage.getItem("receiptFooterText");

        //     if (storedLogo) {
        //         $("#logoPreview").attr("src", "/storage/receipt_logos/" + storedLogo).show();
        //     }
        //     if (storedHeaderText) {
        //         $("#headerText").val(storedHeaderText);
        //         $("#headerTextPreview").text(storedHeaderText);
        //     }
        //     if (storedFooterText) {
        //         $("#footerText").val(storedFooterText);
        //         $("#footerTextPreview").text(storedFooterText);
        //     }
        // }


    });
</script>
