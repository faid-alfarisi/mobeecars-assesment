<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Login') }}</title>

    @fonts

    <!-- Styles / Scripts -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
</head>

<body class="m-0">
    <section class="bg-danger bg-gradient bg-opacity-10 min-vh-100 d-flex align-items-center py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-7 col-xl-6 col-xxl-5">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-5">
                                        <h2 class="h3">Login</h2>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="">
                                <div class="row gy-3 overflow-hidden">
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input
                                                type="email"
                                                class="form-control"
                                                name="email"
                                                id="email"
                                                placeholder="name@example.com"
                                                required
                                            >
                                            <label for="email" class="form-label">Email</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <input
                                                type="password"
                                                class="form-control"
                                                name="password"
                                                id="password"
                                                placeholder="Password"
                                                required
                                            >
                                            <label for="password" class="form-label">Password</label>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="d-grid">
                                            <button class="btn bsb-btn-2xl btn-danger" type="submit">
                                                Login
                                            </button>
                                        </div>
                                    </div>
                                    
                                    @error('email')
                                    <div class="col-12">
                                        <div class="text-danger text-center text-bold">
                                            {{ $message }}
                                        </div>
                                    </div>
                                    @enderror
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>
