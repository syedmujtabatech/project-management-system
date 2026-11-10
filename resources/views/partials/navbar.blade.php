<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search bar -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0 me-2 text-muted"></i>
                <form action="{{ route('tasks.index') }}" method="GET" class="d-inline">
                    <input type="text" name="search" class="form-control border-0 shadow-none ps-1" placeholder="Search tasks, codes (e.g. TSK-101)..." aria-label="Search..." value="{{ request('search') }}" style="min-width: 260px;" />
                </form>
            </div>
        </div>

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            <!-- Quick New Task Button -->
            <li class="nav-item me-2 d-none d-md-block">
                <a href="{{ route('tasks.kanban') }}" class="btn btn-sm btn-outline-primary d-flex align-items-center">
                    <i class="bx bx-grid-alt me-1"></i> Kanban Board
                </a>
            </li>

            @if(auth()->user()->role !== 'client')
            <li class="nav-item me-3">
                <a href="{{ route('tasks.create') }}" class="btn btn-sm btn-primary d-flex align-items-center">
                    <i class="bx bx-plus-circle me-1"></i> Create Task
                </a>
            </li>
            @endif

            <!-- Quick Demo Role Switcher Dropdown -->
            <li class="nav-item dropdown me-3">
                <button class="btn btn-sm btn-label-secondary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bx bx-transfer-alt me-1"></i> Switch Persona
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li><h6 class="dropdown-header text-uppercase">Instant Demo Switch</h6></li>
                    <li><a class="dropdown-item" href="{{ route('quick-login', 'admin') }}"><span class="badge bg-danger me-2">Admin</span> Sarah Jenkins (Super Admin)</a></li>
                    <li><a class="dropdown-item" href="{{ route('quick-login', 'pm') }}"><span class="badge bg-primary me-2">PM</span> Alex Rivera (Project Manager)</a></li>
                    <li><a class="dropdown-item" href="{{ route('quick-login', 'dev') }}"><span class="badge bg-success me-2">Dev</span> David Kim (Lead Architect)</a></li>
                    <li><a class="dropdown-item" href="{{ route('quick-login', 'client') }}"><span class="badge bg-info me-2">Client</span> Marcus Vance (Finova Global)</a></li>
                </ul>
            </li>

            <!-- User Dropdown -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <span class="avatar-initial rounded-circle bg-primary text-white">
                            {{ auth()->user()->initials }}
                        </span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <span class="avatar-initial rounded-circle bg-primary text-white">
                                            {{ auth()->user()->initials }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="fw-semibold d-block">{{ auth()->user()->name }}</span>
                                    <small class="text-muted text-capitalize">{{ str_replace('_', ' ', auth()->user()->role) }}</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile & Security</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('timelogs.index', ['user_id' => auth()->id()]) }}">
                            <i class="bx bx-timer me-2"></i>
                            <span class="align-middle">My Timesheets</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('settings.index') }}">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Company Settings</span>
                        </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>
</nav>
