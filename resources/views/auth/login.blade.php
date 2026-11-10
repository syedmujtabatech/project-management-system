<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="{{ asset('assets') }}/">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Enterprise Sign In | ApexPMS</title>
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/img/logo-symbol.svg') }}" />
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />
    <style>
        .demo-chip {
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid #d9dee3;
            background: #fff;
            padding: 8px 12px;
            border-radius: 8px;
            text-align: left;
        }
        .demo-chip:hover {
            border-color: #696cff;
            background: rgba(105, 108, 255, 0.05);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Login Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <img src="{{ asset('assets/img/logo.svg') }}" alt="ApexPMS Enterprise" style="height: 52px; width: auto;" />
                        </div>

                        <h4 class="mb-1 text-center font-weight-bold">Enterprise Sign In 🚀</h4>
                        <p class="mb-4 text-center text-muted">Welcome to ApexPMS. Enter your credentials to manage company projects.</p>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible mb-3" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form id="formAuthentication" class="mb-4" action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Work Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', 'admin@apexcompany.com') }}" placeholder="Enter work email" required autofocus />
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="javascript:void(0);" onclick="alert('For demo environment, please use one of the quick 1-click login buttons below!')">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" value="admin123" placeholder="••••••••••••" required />
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember-me" name="remember" checked />
                                    <label class="form-check-label" for="remember-me"> Keep me signed in </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100 py-2 fs-6 fw-semibold" type="submit">
                                    <i class="bx bx-log-in-circle me-1"></i> Access Workspace
                                </button>
                            </div>
                        </form>

                        <!-- One Click Demo Login Roles -->
                        <div class="divider my-4">
                            <div class="divider-text text-uppercase text-muted small fw-semibold">Quick 1-Click Demo Login</div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ route('quick-login', 'admin') }}" class="demo-chip d-block text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-danger p-1 me-2"><i class="bx bx-shield-quarter"></i></span>
                                        <div>
                                            <div class="fw-bold text-dark small">Super Admin</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Sarah Jenkins</div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('quick-login', 'pm') }}" class="demo-chip d-block text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary p-1 me-2"><i class="bx bx-briefcase"></i></span>
                                        <div>
                                            <div class="fw-bold text-dark small">Project Manager</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Alex Rivera</div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('quick-login', 'dev') }}" class="demo-chip d-block text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success p-1 me-2"><i class="bx bx-code-alt"></i></span>
                                        <div>
                                            <div class="fw-bold text-dark small">Lead Engineer</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">David Kim</div>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-6">
                                <a href="{{ route('quick-login', 'client') }}" class="demo-chip d-block text-decoration-none">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info p-1 me-2"><i class="bx bx-user-check"></i></span>
                                        <div>
                                            <div class="fw-bold text-dark small">Client Portal</div>
                                            <div class="text-muted" style="font-size: 0.72rem;">Finova Global</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <small class="text-muted">ApexPMS Enterprise Edition &bull; Production Ready</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
</body>
</html>
