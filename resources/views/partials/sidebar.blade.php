<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="height: 76px; padding: 0 1.25rem;">
        <a href="{{ route('dashboard') }}" class="app-brand-link d-flex align-items-center">
            <img src="{{ asset('assets/img/logo.svg') }}" alt="ApexPMS Logo" style="height: 42px; width: auto;" />
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div>Executive Dashboard</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Projects & Delivery</span>
        </li>

        <!-- Projects -->
        <li class="menu-item {{ request()->routeIs('projects.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-briefcase-alt-2"></i>
                <div>Projects</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('projects.index') ? 'active' : '' }}">
                    <a href="{{ route('projects.index') }}" class="menu-link">
                        <div>All Projects</div>
                    </a>
                </li>
                @if(auth()->user()->role !== 'client')
                <li class="menu-item {{ request()->routeIs('projects.create') ? 'active' : '' }}">
                    <a href="{{ route('projects.create') }}" class="menu-link">
                        <div>Create Project</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <!-- Tasks & Kanban -->
        <li class="menu-item {{ request()->routeIs('tasks.*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-task"></i>
                <div>Tasks & Agile</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ request()->routeIs('tasks.kanban') ? 'active' : '' }}">
                    <a href="{{ route('tasks.kanban') }}" class="menu-link">
                        <i class="bx bx-grid-alt me-2 text-primary"></i>
                        <div>Interactive Kanban</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('tasks.index') ? 'active' : '' }}">
                    <a href="{{ route('tasks.index') }}" class="menu-link">
                        <i class="bx bx-list-ul me-2"></i>
                        <div>Task Directory</div>
                    </a>
                </li>
                @if(auth()->user()->role !== 'client')
                <li class="menu-item {{ request()->routeIs('tasks.create') ? 'active' : '' }}">
                    <a href="{{ route('tasks.create') }}" class="menu-link">
                        <i class="bx bx-plus me-2"></i>
                        <div>Create Task</div>
                    </a>
                </li>
                @endif
            </ul>
        </li>

        <!-- Timesheets -->
        <li class="menu-item {{ request()->routeIs('timelogs.*') ? 'active' : '' }}">
            <a href="{{ route('timelogs.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-time-five"></i>
                <div>Time Tracking</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Organization & CRM</span>
        </li>

        <!-- Clients -->
        <li class="menu-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
            <a href="{{ route('clients.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-buildings"></i>
                <div>Clients (CRM)</div>
            </a>
        </li>

        <!-- Team -->
        <li class="menu-item {{ request()->routeIs('team.*') ? 'active' : '' }}">
            <a href="{{ route('team.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div>Team Members</div>
            </a>
        </li>

        <!-- Reports -->
        <li class="menu-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <a href="{{ route('reports.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                <div>Reports & Analytics</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Administration</span>
        </li>

        <!-- Settings -->
        <li class="menu-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div>Company Settings</div>
            </a>
        </li>
    </ul>

    <!-- Quick Switch Persona Card in Sidebar footer -->
    <div class="p-3 mx-2 my-2 bg-light rounded border">
        <div class="d-flex align-items-center mb-2">
            <div class="avatar avatar-sm me-2">
                <span class="avatar-initial rounded-circle bg-primary text-white">
                    {{ auth()->user()->initials }}
                </span>
            </div>
            <div class="overflow-hidden">
                <h6 class="mb-0 text-truncate font-weight-bold" style="font-size: 0.85rem;">{{ auth()->user()->name }}</h6>
                <span class="badge bg-label-info text-capitalize" style="font-size: 0.7rem;">
                    {{ str_replace('_', ' ', auth()->user()->role) }}
                </span>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-xs btn-outline-danger w-100 d-flex align-items-center justify-content-center">
                <i class="bx bx-power-off me-1"></i> Sign Out
            </button>
        </form>
    </div>
</aside>
