<nav>
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('sites.index') ? 'active' : '' }}"
                href="{{ route('sites.index') }}">Sites</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('rate-tier.index') ? 'active' : '' }}"
                href="{{ route('rate-tier.index') }}">Rate Tier</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('addons.index') ? 'active' : '' }}"
                href="{{ route('addons.index') }}">Add Ons</a>
        </li>
    </ul>
</nav>
<style>
    .nav-tabs .nav-link {
        color: #333 !important; /* Darker text for non-active tabs */
        font-weight: 500;
    }

    .nav-tabs .nav-link:hover {
        color: #000 !important; /* Even darker on hover */
        background-color: #f0f0f0;
    }

    .nav-tabs .nav-link.active {
        color: #fff !important;
        background-color: #0d6efd !important; /* Bootstrap primary */
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: bold;
    }

    .nav-tabs .nav-link {
    border: 1px solid #dee2e6;
    border-bottom: none;
    border-radius: 0.375rem 0.375rem 0 0;
}

</style>
