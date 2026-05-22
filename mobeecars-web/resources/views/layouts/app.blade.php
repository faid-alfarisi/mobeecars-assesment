<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dataTables.bootstrap5.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container-fluid">

            <a class="navbar-brand" href="/dashboard">
                Car Admin
            </a>

            <button class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarContent">

                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">

                <ul class="navbar-nav mb-2 mb-lg-0">

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                            User Management
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('cars*') ? 'active' : '' }}"
                        href="{{ route('cars.index') }}">
                            Car Inventory
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('reports*') ? 'active' : '' }}"
                        href="/reports">
                            Reports
                        </a>
                    </li>

                </ul>

                <div class="ms-lg-auto pt-2 pt-lg-0">

                    <div class="dropdown">

                        <button class="btn btn-danger dropdown-toggle w-100 w-lg-auto"
                                type="button"
                                data-bs-toggle="dropdown">
                            <i class="fas fa-user fa-fw"></i> {{ auth()->user()->name }}
                        </button>

                        <ul class="dropdown-menu dropdown-menu-lg-end">

                            <li>
                                <a class="dropdown-item {{ request()->is('account*') ? 'active' : '' }}" href="{{ route('account.index') }}">
                                    Account Settings
                                </a>
                            </li>

                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <button type="submit" class="dropdown-item">
                                        Logout
                                    </button>
                                </form>
                            </li>

                        </ul>

                    </div>

                </div>

            </div>
        </div>
    </nav>

    <div class="container py-4">

        @yield('content')

    </div>

    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script src="{{ asset('js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    @stack('scripts')

</body>

</html>
