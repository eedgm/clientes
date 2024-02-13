@can('view-any', App\Models\Clients::class)
    <x-dashboard.sidebar-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="{{ 'bxs-dashboard' }}">
        Dashboard
    </x-dashboard.sidebar-link>
@endcan
@can('view-any', App\Models\Clients::class)
    <x-dashboard.sidebar-link href="{{ route('clients.index') }}" :active="request()->routeIs('clients.index')" icon="{{ 'bxs-group' }}">
        Clients
    </x-dashboard.sidebar-link>
@endcan
@can('view-any', App\Models\User::class)
    <x-dashboard.sidebar-link href="{{ route('users.index') }}" :active="request()->routeIs('users.index')" icon="{{ 'bx-user-circle' }}">
        Users
    </x-dashboard.sidebar-link>
@endcan
@can('view-any', App\Models\Receipt::class)
    <x-dashboard.sidebar-link href="{{ route('receipts.index') }}" :active="request()->routeIs('receipts.index')" icon="{{ 'bx-receipt' }}">
        Receipts
    </x-dashboard.sidebar-link>
@endcan
@can('view-any', App\Models\Supplier::class)
    <x-dashboard.sidebar-link href="{{ route('suppliers.index') }}" :active="request()->routeIs('suppliers.index')" icon="{{ 'bx-network-chart' }}">
        Suppliers
    </x-dashboard.sidebar-link>
@endcan
@can('view-any', App\Models\Payable::class)
    <x-dashboard.sidebar-link href="{{ route('payables.clone') }}" :active="request()->routeIs('payables.clone')" icon="{{ 'bxs-wallet-alt' }}">
        Payables
    </x-dashboard.sidebar-link>
@endcan


@if (Auth::user()->can('create', Spatie\Permission\Models\Role::class) ||
            Auth::user()->can('create', Spatie\Permission\Models\Permission::class))
    <!-- Section Devider -->
    <div class="pt-4 pb-1 pl-0 mb-4 text-xs text-white section" :class="isSidebarExpanded ? 'md:block' : 'hidden group-hover:md:block'">
        Permissions
    </div>
    <hr>
    @can('create', Spatie\Permission\Models\Role::class)
        <x-dashboard.sidebar-link href="{{ route('roles.index') }}" :active="request()->routeIs('roles.index')" icon="{{ 'bx-tag-alt' }}">
            Roles
        </x-dashboard.sidebar-link>
    @endcan
    @can('create', Spatie\Permission\Models\Permission::class)
        <x-dashboard.sidebar-link href="{{ route('permissions.index') }}" :active="request()->routeIs('permissions.index')" icon="{{ 'bx-badge-check
            ' }}">
            Permissions
        </x-dashboard.sidebar-link>
    @endcan
@endif

