<!DOCTYPE html>
<html lang="en" class="layout-menu-fixed layout-compact" dir="ltr" data-assets-path="{{ asset('assets') }}/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | ApexPMS Enterprise</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/logo-symbol.svg') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <!-- Custom Enterprise Polish -->
    <style>
        :root {
            --bs-primary: #696cff;
            --bs-primary-rgb: 105, 108, 255;
        }
        .bg-menu-theme .menu-inner > .menu-item.active > .menu-link {
            color: #696cff;
            background-color: rgba(105, 108, 255, 0.12) !important;
        }
        .bg-menu-theme .menu-inner > .menu-item.active:before {
            background: #696cff;
        }
        .avatar-initial {
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .kanban-board {
            display: flex;
            gap: 1.25rem;
            overflow-x: auto;
            padding-bottom: 1.5rem;
            min-height: calc(100vh - 240px);
        }
        .kanban-col {
            flex: 0 0 320px;
            background: #f5f5f9;
            border-radius: 0.5rem;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 220px);
        }
        .kanban-col-body {
            overflow-y: auto;
            flex: 1;
            padding-right: 4px;
            min-height: 150px;
        }
        .kanban-card {
            background: #fff;
            border-radius: 0.375rem;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
            padding: 1rem;
            margin-bottom: 0.85rem;
            cursor: grab;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            border-left: 4px solid transparent;
        }
        .kanban-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px 0 rgba(67, 89, 113, 0.2);
        }
        .kanban-card.dragging {
            opacity: 0.4;
        }
        .kanban-col.drag-over {
            background-color: #e8ebf8;
            outline: 2px dashed #696cff;
        }
        .priority-urgent { border-left-color: #ff3e1d !important; }
        .priority-high   { border-left-color: #ffab00 !important; }
        .priority-medium { border-left-color: #03c3ec !important; }
        .priority-low    { border-left-color: #71dd37 !important; }
        .stat-card-glow {
            transition: all 0.2s ease-in-out;
        }
        .stat-card-glow:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1.25rem rgba(67, 89, 113, 0.15);
        }
    </style>

    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu / Sidebar -->
            @include('partials.sidebar')

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                @include('partials.navbar')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Flash Notifications -->
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible d-flex align-items-center mb-4" role="alert">
                                <i class="bx bx-check-circle me-2 fs-4"></i>
                                <div class="flex-grow-1">{{ session('success') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible d-flex align-items-center mb-4" role="alert">
                                <i class="bx bx-error-circle me-2 fs-4"></i>
                                <div class="flex-grow-1">{{ session('error') }}</div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible mb-4" role="alert">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="bx bx-error me-2 fs-5"></i>
                                    <strong>Please fix the following errors:</strong>
                                </div>
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                    <!-- / Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-3 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                © {{ date('Y') }}
                                <a href="javascript:void(0)" class="footer-link fw-semibold text-primary">ApexPMS Enterprise</a>
                                — High-Performance Project Management Architecture
                            </div>
                            <div>
                                <span class="badge bg-label-success">Enterprise Edition</span>
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- / Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Global Toast & AJAX Handler -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
