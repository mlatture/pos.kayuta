 <div class="row g-5 p-4">
     <div class="border rounded-3 p-4 shadow-sm">
         <div class="d-flex justify-content-between align-items-center mb-3">
             <h5 class="mb-0">📋 Seasonal Guest Renewals</h5>
             <div class="d-flex gap-2">
                 <button class="btn btn-danger" id="clearAndReloadBtn">
                     <i class="bi bi-arrow-clockwise"></i> Clear and Reload
                 </button>
                 <button class="btn btn-success" id="sendEmailBtn" data-url="{{ route('seasonal.sendEmails') }}">
                     <i class="bi bi-envelope"></i> Send Emails
                 </button>

             </div>
         </div>

         <div class="alert alert-warning small">
             <strong>Warning:</strong> Clicking <em>Clear and Reload</em> will reset the renewal process.
             This should only be used for a new season.
         </div>

         
         
        </div>
        @include('admin.seasonal.component.renewal-table')

 </div>
