{{-- resources/views/admin/waivers/unattached-list.blade.php --}}

{{-- Filters (standard GET form) --}}
<form method="GET" action="" class="row g-2 mb-3">
    <div class="col-md-3">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="From">
    </div>
    <div class="col-md-3">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="To">
    </div>
    <div class="col-md-3">
        <input type="text" name="email" value="{{ request('email') }}" class="form-control" placeholder="Email">
    </div>
    <div class="col-md-3">
        <input type="text" name="name" value="{{ request('name') }}" class="form-control" placeholder="Name">
    </div>
    <div class="col-12 text-end">
        <button class="btn btn-outline-secondary btn-sm">Filter</button>
    </div>
</form>

{{-- Bulk actions --}}
<div class="d-flex gap-2 mb-2">
    <form method="POST" action="{{ route('customers.waivers.attach', ['user' => $user->id]) }}" id="attachForm">
        @csrf
        <input type="hidden" name="waiver_ids_json" id="attach_ids_json">
        <button type="button" class="btn btn-primary btn-sm" onclick="submitAttach()">Attach to this customer</button>
    </form>

    <form method="POST" action="{{ route('waivers.bulkDelete') }}" id="deleteForm">
        @csrf
        <input type="hidden" name="waiver_ids_json" id="delete_ids_json">
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="submitDelete()">Delete (soft)</button>
    </form>
</div>

{{-- Table --}}
<div class="table-responsive">
    <table class="table table-sm table-striped align-middle">
        <thead>
            <tr>
                <th style="width:28px;"><input type="checkbox" id="chkAll"></th>
                <th>Date/Time</th>
                <th>Name</th>
                <th>Email</th>
                <th>Site</th>
                <th>Booking ID</th>
                <th>IP</th>
                <th>Download</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($waivers as $w)
                <tr>
                    <td><input type="checkbox" class="rowChk" value="{{ $w->id }}"></td>
                    <td>{{ $w->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $w->name }}</td>
                    <td>{{ $w->email }}</td>
                    <td>{{ $w->site_text }}</td>
                    <td>{{ $w->booking_id }}</td>
                    <td>{{ $w->ip }}</td>
                    <td>
                        @php
                            $dl = URL::temporarySignedRoute('waivers.download', now()->addMinutes(15), ['waiver'=>$w->id]);
                        @endphp
                        <a href="{{ $dl }}" target="_blank" class="btn btn-link btn-sm">PDF</a>
                    </td>
                    <td><span class="badge bg-secondary">{{ $w->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted">No unattached waivers found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-2">
    {{ $waivers->withQueryString()->links() }}
</div>

<script>
(function(){
    // master checkbox
    const chkAll = document.getElementById('chkAll');
    const rows = () => Array.from(document.querySelectorAll('.rowChk'));
    chkAll?.addEventListener('change', function(){
        rows().forEach(r => r.checked = this.checked);
    });

    function selectedIds() {
        return rows().filter(r => r.checked).map(r => r.value);
    }

    window.submitAttach = function() {
        const ids = selectedIds();
        if (!ids.length) return alert('Select at least one waiver.');
        if (!confirm('Attach selected waivers to this customer?')) return;
        document.getElementById('attach_ids_json').value = JSON.stringify(ids);
        document.getElementById('attachForm').submit();
    };

    window.submitDelete = function() {
        const ids = selectedIds();
        if (!ids.length) return alert('Select at least one waiver.');
        if (!confirm('Soft delete selected waivers?')) return;
        document.getElementById('delete_ids_json').value = JSON.stringify(ids);
        document.getElementById('deleteForm').submit();
    };
})();
</script>
