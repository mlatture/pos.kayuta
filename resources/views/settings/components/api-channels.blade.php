<div class="tab-pane fade" id="apiChannels" role="tabpanel" aria-labelledby="apiChannels-tab">
     <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                
                <div class="card-body">

                    {{-- One-time Key Alerts --}}
                    @if(session('created_key_plain'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Copy now:</strong>
                            <code class="ml-1">{{ session('created_key_plain') }}</code>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('rotated_key_plain'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>New key (copy once):</strong>
                            <code class="ml-1">{{ session('rotated_key_plain') }}</code>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    {{-- Filters --}}
                    <form method="get" class="mb-4">
                        <div class="form-row">
                            <div class="col-md-4 mb-2">
                                <label for="filter_channel_id" class="mb-1">Channel ID</label>
                                <input type="text"
                                       id="filter_channel_id"
                                       name="channel_id"
                                       class="form-control"
                                       placeholder="e.g., 101"
                                       value="{{ $settings['filters']['channel_id'] ?? '' }}">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label for="filter_status" class="mb-1">Status</label>
                                <select id="filter_status" name="status" class="form-control">
                                    <option value="">Any status</option>
                                    <option value="active" @selected(($settings['filters']['status'] ?? '')==='active')>Active</option>
                                    <option value="inactive" @selected(($settings['filters']['status'] ?? '')==='inactive')>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2 d-flex align-items-end">
                                <button class="btn btn-primary mr-2" type="submit">
                                    <i class="tio-filter-list mr-1"></i> Filter
                                </button>
                                <a href="" class="btn btn-outline-secondary">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th style="width: 70px;">ID</th>
                                    <th>Property</th>
                                    <th>Channel</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Sandbox</th>
                                    <th>Last Used</th>
                                    <th>Created</th>
                                    <th style="width: 160px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($settings['items'] as $it)
                                <tr>
                                    <td>{{ $it->id }}</td>
                                    <td><span class="text-monospace">{{ $it->property_id }}</span></td>
                                    <td><span class="text-monospace">{{ $it->channel_id }}</span></td>
                                    <td>{{ $it->name }}</td>
                                    <td>
                                        @php $isActive = (string)$it->status === 'active'; @endphp
                                        <span class="badge badge-{{ $isActive ? 'success' : 'secondary' }}">
                                            {{ $isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td><span class="badge badge-dark">{{ $it->sandbox ? 'Yes' : 'No' }}</span></td>
                                    <td>
                                        {{ $it->last_used_at ? \Illuminate\Support\Carbon::parse($it->last_used_at)->format('Y-m-d H:i') : '—' }}
                                    </td>
                                    <td>{{ $it->created_at ? \Illuminate\Support\Carbon::parse($it->created_at)->format('Y-m-d H:i') : '—' }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                            <div class="d-flex align-items-center gap-2">
                                                <form method="post" action="{{ route('admin.api_channels.rotate', $it->id) }}" class="m-0 p-0">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary" data-bs-toggle="tooltip" title="Rotate Key">
                                                        Rotate
                                                    </button>
                                                </form>
                                            
                                                <button type="button"
                                                        class="btn btn-outline-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#revokeModal"
                                                        data-action="{{ route('admin.api_channels.revoke', $it->id) }}"
                                                        data-name="{{ $it->name }}">
                                                    Revoke
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        No API channels found. Try adjusting the filters or create a key below.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-3">
                        {{ $settings['items']->appends(request()->query())->links() }}
                    </div>

                    {{-- Create Key --}}
                    <hr class="my-4">

                    <h6 class="text-uppercase mb-3">Create Key</h6>
                    <form method="post" action="{{ route('admin.api_channels.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="property_id">Property ID <span class="text-danger">*</span></label>
                                <input required type="number" name="property_id" id="property_id" class="form-control" placeholder="e.g., 2001">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="channel_id">Channel ID <span class="text-danger">*</span></label>
                                <input required type="number" name="channel_id" id="channel_id" class="form-control" placeholder="e.g., 101">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="code">Code <span class="text-danger">*</span></label>
                                <input required type="text" name="code" id="code" class="form-control" placeholder="e.g., AFFIL">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input required type="text" name="name" id="name" class="form-control" placeholder="Partner / Service name">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="rate_limit_per_minute">Rate Limit / min</label>
                                <input type="number" name="rate_limit_per_minute" id="rate_limit_per_minute" class="form-control" placeholder="Default 100">
                            </div>
                            <div class="form-group col-md-3">
                                <label for="rate_burst_per_minute">Burst / min</label>
                                <input type="number" name="rate_burst_per_minute" id="rate_burst_per_minute" class="form-control" placeholder="Default 300">
                            </div>
                            <div class="form-group col-md-3">
                                <div class="custom-control custom-checkbox mt-4">
                                    <input type="checkbox" class="custom-control-input" id="sandbox" name="sandbox" value="1">
                                    <label class="custom-control-label" for="sandbox">Sandbox</label>
                                </div>
                            </div>
                        </div>

                        <p class="small text-muted mt-3 mb-0">
                            Use <code>Authorization: Bearer &lt;key&gt;</code> for partners.
                            Internal services may send <code>X-Internal-Service: book|admin</code>.
                        </p>
                </div> <!-- /card-body -->

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="tio-add mr-1"></i> Create
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Revoke Modal --}}
<div class="modal fade" id="revokeModal" tabindex="-1" role="dialog" aria-labelledby="revokeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form method="post" id="revokeForm" action="#">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revokeModalLabel">Revoke API Key</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to revoke the key for <strong id="revokeName">this channel</strong>?
                This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Revoke</button>
            </div>
        </div>
    </form>
  </div>
</div>
@push('js')
<script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();

    $('#revokeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var action = button.data('action');
        var name   = button.data('name') || 'this channel';
        $('#revokeForm').attr('action', action);
        $('#revokeName').text(name);
    });
});
</script>
@endpush