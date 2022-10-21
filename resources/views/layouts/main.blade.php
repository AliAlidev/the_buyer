<!doctype html>
@if (session()->get('locale') == 'en')
    <html lang="en">

    <head>
        @include('layouts.main_head')

        @stack('styles')
    </head>

    <body data-topbar="dark" data-sidebar-user="false" data-sidebar="dark">

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">

                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="{{ route('home') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm-dark.png') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="24">
                                </span>
                            </a>

                            <a href="{{ route('home') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo1.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo1.png') }}" alt="" height="40">
                                </span>
                            </a>
                        </div>

                        <!-- Menu Icon -->

                        <button type="button" class="btn px-3 font-size-24 header-item waves-effect"
                            id="vertical-menu-btn">
                            <i class="mdi mdi-menu"></i>
                        </button>


                    </div>

                    <div class="d-flex">
                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">

                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..."
                                                aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i
                                                        class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- User -->
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle header-profile-user"
                                    src="{{ asset('assets/images/users/no_image.png') }}" alt="Header Avatar">
                            </button>

                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="#"><i
                                        class="mdi mdi-account-circle font-size-16 align-middle me-2 text-muted"></i>
                                    <span>Profile</span></a>
                                <a class="dropdown-item text-primary" href="/logout"><i
                                        class="mdi mdi-power font-size-16 align-middle me-2 text-primary"></i>
                                    <span>Logout</span></a>
                            </div>
                        </div>

                        {{-- language --}}
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown1"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ strtoupper(session()->get('locale')) }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item dropdown-item11" href="#" id="ar">
                                    <span>AR</span></a>
                                <a class="dropdown-item dropdown-item11" href="#" id="en">
                                    <span>EN</span></a>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">
                    <div class="user-details">
                        <div class="d-flex">
                            <div class="me-2">
                                <img src="{{ asset('assets/images/users/avatar-4.jpg') }}" alt=""
                                    class="avatar-md rounded-circle">
                            </div>
                            <div class="user-info w-100">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        Donald Johnson
                                        <i class="mdi mdi-chevron-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" class="dropdown-item"><i
                                                    class="mdi mdi-account-circle text-muted me-2"></i>
                                                Profile<div class="ripple-wrapper me-2"></div>
                                            </a></li>
                                        <li><a href="/logout" class="dropdown-item"><i
                                                    class="mdi mdi-power text-muted me-2"></i>
                                                Logout</a></li>
                                    </ul>
                                </div>

                                <p class="text-white-50 m-0">Administrator</p>
                            </div>
                        </div>
                    </div>

                    @include('layouts.sidemenu')

                </div>
            </div>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        @yield('content')

                    </div>
                    <!-- Container-fluid -->
                </div>
                <!-- End Page-content -->


                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> © Buyer <span class="d-none d-sm-inline-block">
                            </div>

                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        <!-- JAVASCRIPT -->
        @include('layouts.main_scripts')

        <script>
            $(".dropdown-item11").click(function() {
                $.ajax({
                    url: "{{ route('change_lang') }}",
                    type: "post",
                    data: {
                        _token: "{{ csrf_token() }}",
                        language: $(this).text()
                    },
                    complete: function(resutl) {
                        $('#page-header-user-dropdown1').text(resutl.responseText);
                        location.reload();
                    }
                });
            });
        </script>


        @stack('scripts')

    </body>
@else
    <html lang="ar" dir="rtl">

    <head>

        @include('layouts.main_head')

        @stack('styles')

    </head>

    <body data-topbar="dark" data-sidebar-user="false" data-sidebar="dark">

        <!-- Begin page -->
        <div id="layout-wrapper">

            <header id="page-topbar">
                <div class="navbar-header">
                    <div class="d-flex">

                        <!-- LOGO -->
                        <div class="navbar-brand-box">
                            <a href="{{ route('home') }}" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo-sm-dark.png') }}" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo-dark.png') }}" alt=""
                                        height="24">
                                </span>
                            </a>

                            <a href="{{ route('home') }}" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="{{ asset('assets/images/logo1.png') }}" alt="" height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="{{ asset('assets/images/logo1.png') }}" alt="" height="40">
                                </span>
                            </a>
                        </div>

                        <!-- Menu Icon -->

                        <button type="button" class="btn px-3 font-size-24 header-item waves-effect"
                            id="vertical-menu-btn">
                            <i class="mdi mdi-menu"></i>
                        </button>


                    </div>

                    <div class="d-flex">
                        <div class="dropdown d-inline-block d-lg-none ms-2">
                            <button type="button" class="btn header-item noti-icon waves-effect"
                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="mdi mdi-magnify"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                                aria-labelledby="page-header-search-dropdown">

                                <form class="p-3">
                                    <div class="form-group m-0">
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Search ..."
                                                aria-label="Recipient's username">
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit"><i
                                                        class="mdi mdi-magnify"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- User -->
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect"
                                id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <img class="rounded-circle header-profile-user"
                                    src="{{ asset('assets/images/users/no_image.png') }}" alt="Header Avatar">
                            </button>


                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item" href="#"><i
                                        class="mdi mdi-account-circle font-size-16 align-middle me-2 text-muted"></i>
                                    <span>Profile</span></a>
                                <a class="dropdown-item text-primary" href="/logout"><i
                                        class="mdi mdi-power font-size-16 align-middle me-2 text-primary"></i>
                                    <span>Logout</span></a>
                            </div>
                        </div>

                        {{-- language --}}
                        <div class="dropdown d-inline-block">
                            <button type="button" class="btn header-item waves-effect"
                                id="page-header-user-dropdown1" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                {{ session()->get('locale') != null ? strtoupper(session()->get('locale')) : strtoupper(Auth::user()->language) }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <!-- item-->
                                <a class="dropdown-item dropdown-item11" href="#" id="ar">
                                    <span>AR</span></a>
                                <a class="dropdown-item dropdown-item11" href="#" id="en">
                                    <span>EN</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- ========== Left Sidebar Start ========== -->
            <div class="vertical-menu">

                <div data-simplebar class="h-100">
                    <div class="user-details">
                        <div class="d-flex">
                            <div class="me-2">
                                <img src="{{ asset('assets/images/users/avatar-4.jpg') }}" alt=""
                                    class="avatar-md rounded-circle">
                            </div>
                            <div class="user-info w-100">
                                <div class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                        Donald Johnson
                                        <i class="mdi mdi-chevron-down"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="javascript:void(0)" class="dropdown-item"><i
                                                    class="mdi mdi-account-circle text-muted me-2"></i>
                                                Profile<div class="ripple-wrapper me-2"></div>
                                            </a></li>
                                        <li><a href="/logout" class="dropdown-item"><i
                                                    class="mdi mdi-power text-muted me-2"></i>
                                                Logout</a></li>
                                    </ul>
                                </div>

                                <p class="text-white-50 m-0">Administrator</p>
                            </div>
                        </div>
                    </div>

                    @include('layouts.sidemenu')

                </div>
            </div>
            <!-- Left Sidebar End -->



            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="main-content">

                <div class="page-content">
                    <div class="container-fluid">
                        @yield('content')

                    </div>
                    <!-- Container-fluid -->
                </div>
                <!-- End Page-content -->


                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> © Buyer <span class="d-none d-sm-inline-block">
                            </div>

                        </div>
                    </div>
                </footer>
            </div>
            <!-- end main content-->

        </div>
        <!-- END layout-wrapper -->

        <!-- Right bar overlay-->
        <div class="rightbar-overlay"></div>

        @include('layouts.main_scripts')

        <script>
            $(".dropdown-item11").click(function() {
                $.ajax({
                    url: "{{ route('change_lang') }}",
                    type: "post",
                    data: {
                        _token: "{{ csrf_token() }}",
                        language: $(this).text()
                    },
                    complete: function(resutl) {
                        $('#page-header-user-dropdown1').text(resutl.responseText.toUpperCase());
                        location.reload();
                    }
                });
            });
        </script>


        @stack('scripts')

    </body>
@endif

</html>
