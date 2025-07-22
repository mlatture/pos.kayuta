<nav>
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request('tab', 'overview') === 'overview' ? 'active' : '' }}"
                href="{{ route('admin.seasonal-settings.index', ['tab' => 'overview']) }}">
                Overview
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('tab', 'form') === 'form' ? 'active' : '' }}"
                href="{{ route('admin.seasonal-settings.index', ['tab' => 'form']) }}">
                Forms </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request('tab', 'rate') === 'rate' ? 'active' : '' }}"
                href="{{ route('admin.seasonal-settings.index', ['tab' => 'rate']) }}">
                Rates </a>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request('tab', 'addons') === 'addons' ? 'active' : '' }}"
                href="{{ route('admin.seasonal-settings.index', ['tab' => 'addons']) }}">
                Add Ons
            </a>
        </li>

    </ul>
</nav>

<style>
    .nav-tabs .nav-link {
        color: #333 !important;
        /* Darker text for non-active tabs */
        font-weight: 500;
    }

    .nav-tabs .nav-link:hover {
        color: #000 !important;
        /* Even darker on hover */
        background-color: #f0f0f0;
    }

    .nav-tabs .nav-link.active {
        color: #fff !important;
        background-color: #0d6efd !important;
        /* Bootstrap primary */
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }

    .nav-tabs .nav-link {
        border: 1px solid #dee2e6;
        border-bottom: none;
        border-radius: 0.375rem 0.375rem 0 0;
    }
</style>
