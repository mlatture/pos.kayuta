<div class="row g-5">
    <div class="col">
        <div class="border rounded-3 p-4 shadow-sm">
            <h5 class="mb-3 text-primary">📄 Upload Document Templates</h5>

            {{-- Merge Instructions --}}
            <div class="alert alert-info mb-4">
                <strong>Merge Instructions:</strong><br>
                @verbatim
                    Use these identifiers in your document file. They will be replaced in the generated PDF:
                    <ul class="mb-0">
                        <li><code>{{ first_name }}</code></li>
                        <li><code>{{ last_name }}</code></li>
                        <li><code>{{ site_number }}</code></li>
                        <li><code>{{ seasonal_rate }}</code></li>
                        <li><code>{{ deadline }}</code></li>
                        <li><code>{{ discount_amount }}</code></li>
                        <li><code>{{ year }}</code></li>
                    </ul>
                @endverbatim
            </div>

            <form method="POST" action="{{ route('settings.storeTemplate') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-floating mb-3">
                    <input name="name" class="form-control" id="templateName" placeholder="Template Name" required>
                    <label for="templateName">Name</label>
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
                            <a class="btn btn-sm btn-outline-secondary" href="{{ asset('storage/' . $template->file) }}"
                                target="_blank">Download</a>
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
