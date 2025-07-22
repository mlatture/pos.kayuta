 <div class="row g-5">
     <div class="col">
         <div class="border rounded-3 p-4 shadow-sm">
             <h5 class="mb-3 text-primary">📄 Upload Document Templates</h5>
             <form method="POST" action="{{ route('settings.storeTemplate') }}" enctype="multipart/form-data">
                 @csrf
                 <div class="form-floating mb-3">
                     <input name="name" class="form-control" id="templateName" placeholder="Template Name" required>
                     <label for="templateName">Name</label>
                 </div>
                 <div class="form-floating mb-3">
                     <input name="description" class="form-control" id="templateDescription" placeholder="Description">
                     <label for="templateDescription">Description</label>
                 </div>
                 <div class="mb-3">
                     <label class="form-label">Upload File (DOC, DOCX, PDF)</label>
                     <input type="file" name="file" class="form-control" required>
                 </div>
                 <button class="btn btn-success w-100">Upload Template</button>
             </form>

             <hr class="my-4">
             <h6>📂 Existing Templates</h6>
             <ul class="list-group">
                 @forelse ($documentTemplates as $template)
                     <li class="list-group-item d-flex justify-content-between align-items-center">
                         <strong>{{ $template->name }}</strong>
                         <div class="d-flex gap-2">
                             <a class="btn btn-sm btn-outline-secondary"
                                 href="{{ asset('storage/' . $template->file) }}" target="_blank">Download</a>
                             <button class="btn btn-sm btn-outline-danger btn-delete"
                                 data-url="{{ route('template.destroy', $template->id) }}">Delete</button>
                         </div>
                     </li>

                 @empty
                     <li class="list-group-item">No templates uploaded yet.</li>
                 @endforelse
             </ul>
         </div>


     </div>



 </div>
