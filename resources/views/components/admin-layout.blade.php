<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ $title ?? 'Rent a Car' }}</title>
    
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
    
    <!-- CORE CSS FILES (যা সব পেজে লাগবে) -->
    <!-- অ্যাসেট পাথ: public/assets/admin/plugins/... -->
    <link href="{{ asset('assets/admin/plugins/material/css/materialdesignicons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/simplebar/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/nprogress/nprogress.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/toaster/toastr.min.css') }}" rel="stylesheet" /> 
    <link id="main-css-href" rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}" />

    <!-- FAVICON -->
    <link href="{{ asset('assets/admin/images/favicon.png') }}" rel="shortcut icon" />

    <script src="{{ asset('assets/admin/plugins/nprogress/nprogress.js') }}"></script>
    
    <!-- **CSS PUSH LOCATION** : অন্যান্য পেজের অতিরিক্ত CSS এখানে লোড হবে -->
    @stack('css')

</head>

<body class="navbar-fixed sidebar-fixed" id="body">
    <script>
        NProgress.configure({ showSpinner: false });
        NProgress.start();
    </script>
    
    <div id="toaster"></div>

    <!-- ==================================== WRAPPER ===================================== -->
    <div class="wrapper">
        
        <!-- ==================================== LEFT SIDEBAR ===================================== -->
        <aside class="left-sidebar sidebar-dark" id="left-sidebar">
            <div id="sidebar" class="sidebar sidebar-with-footer">
                <!-- Application Brand -->
                <div class="app-brand">
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('assets/admin/images/logo.png') }}" alt="Admin">
                        <span class="brand-name">{{ config('app.name', 'ADMIN') }}</span>
                    </a>
                </div>
                <!-- Sidebar Menu: Named Slot ($sidebar) -->
                <div class="sidebar-left" data-simplebar style="height: 100%;">
                    {{ $sidebar }} 
                </div>
                <!-- Sidebar Footer -->
                <div class="sidebar-footer">
                    <div class="sidebar-footer-content">
                        <ul class="d-flex">
                            <li><a href="#" data-toggle="tooltip" title="Profile settings"><i class="mdi mdi-settings"></i></a></li>
                            <li><a href="#" data-toggle="tooltip" title="Logout"><i class="mdi mdi-logout"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- ==================================== PAGE WRAPPER ===================================== -->
        <div class="page-wrapper">
            
            <!-- Header -->
            <header class="main-header" id="header">
                <nav class="navbar navbar-expand-lg navbar-light" id="navbar">
                    <button id="sidebar-toggler" class="sidebar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                    </button>
                    <span class="page-title">{{ $header_title ?? 'ড্যাশবোর্ড' }}</span>
                    <div class="navbar-right">
                        {{ $navbar_right ?? '' }}
                    </div>

                    @include('layouts.navigation')
                    
                </nav>
            </header>

            <!-- ==================================== CONTENT WRAPPER (MAIN CONTENT) ===================================== -->
            <div class="content-wrapper">
                <div class="content">
                    {{ $slot }} <!-- Default Slot: এইখানে প্রতিটি পেজের কন্টেন্ট লোড হবে -->
                </div>
            </div>

            <!-- Footer -->
            <footer class="footer mt-auto">
                <div class="copyright bg-white">
                    <p>
                        &copy; <span id="copy-year"></span> Copyright by {{ config('app.name') }}.
                    </p>
                </div>
                <script>
                    document.getElementById("copy-year").innerHTML = new Date().getFullYear();
                </script>
            </footer>
        </div>
    </div>

    @include('layouts.contact')

    <!-- CORE JAVASCRIPT FILES (যা সব পেজে লাগবে) -->
    {{-- <script src="{{ asset('assets/admin/plugins/jquery/jquery.min.js') }}"></script> --}}
    <script src="{{ asset('assets/admin') }}/plugins/jquery/jquery.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/simplebar/simplebar.min.js') }}"></script>
    <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>
    <script src="{{ asset('assets/admin/plugins/toaster/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/mono.js') }}"></script>
    <script src="{{ asset('assets/admin/js/custom.js') }}"></script>

    <!-- **JS PUSH LOCATION**: অন্যান্য পেজের অতিরিক্ত JS এখানে লোড হবে -->
    @stack('script')
    
</body>
</html>
