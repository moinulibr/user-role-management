<x-admin-layout>
    {{-- 1. $slot-এ ডাটা পাস করার জন্য <x-slot> ব্যবহার --}}
    
    {{-- পেজের টাইটেল (header নামে Named Slot) --}}
    <x-slot name="page_title">
        আপনার অ্যাডমিন ড্যাশবোর্ড
    </x-slot>

    {{-- সাইডবারের কন্টেন্ট (sidebar নামে Named Slot) --}}
    <x-slot name="sidebar">
        <h2 class="text-white text-xl font-bold mb-6">Laragon Admin</h2>
        <nav class="space-y-2">
            <a href="#" class="block px-3 py-2 rounded-md text-white bg-gray-900">ড্যাশবোর্ড</a>
            <a href="{{-- {{ route('trucks.index') }} --}}" class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-700">ট্রাক্স ম্যানেজ করুন</a>
            <a href="#" class="block px-3 py-2 rounded-md text-gray-300 hover:bg-gray-700">সেটিংস</a>
        </nav>
    </x-slot>

    {{-- মূল কন্টেন্ট ($slot) --}}
    <!-- Top Statistics -->
        <div class="row">
        <div class="col-xl-3 col-sm-6">
            <div class="card card-default card-mini">
            <div class="card-header">
                <h2>$18,699</h2>
                <div class="dropdown">
                <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </div>
                <div class="sub-title">
                <span class="mr-1">Sales of this year</span> |
                <span class="mx-1">45%</span>
                <i class="mdi mdi-arrow-up-bold text-success"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                <div>
                    <div id="spline-area-1"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-default card-mini">
            <div class="card-header">
                <h2>$14,500</h2>
                <div class="dropdown">
                <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </div>
                <div class="sub-title">
                <span class="mr-1">Expense of this year</span> |
                <span class="mx-1">50%</span>
                <i class="mdi mdi-arrow-down-bold text-danger"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                <div>
                    <div id="spline-area-2"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-default card-mini">
            <div class="card-header">
                <h2>$4199</h2>
                <div class="dropdown">
                <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </div>
                <div class="sub-title">
                <span class="mr-1">Profit of this year</span> |
                <span class="mx-1">20%</span>
                <i class="mdi mdi-arrow-down-bold text-danger"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                <div>
                    <div id="spline-area-3"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card card-default card-mini">
            <div class="card-header">
                <h2>$20,199</h2>
                <div class="dropdown">
                <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </div>
                <div class="sub-title">
                <span class="mr-1">Revenue of this year</span> |
                <span class="mx-1">35%</span>
                <i class="mdi mdi-arrow-up-bold text-success"></i>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                <div>
                    <div id="spline-area-4"></div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>

        <div class="row">
        <div class="col-xl-8">

            <!-- Income and Express -->
            <div class="card card-default">
            <div class="card-header">
                <h2>Income And Expenses</h2>
                <div class="dropdown">
                <a class="dropdown-toggle icon-burger-mini" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false" data-display="static">
                </a>

                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="#">Action</a>
                    <a class="dropdown-item" href="#">Another action</a>
                    <a class="dropdown-item" href="#">Something else here</a>
                </div>
                </div>

            </div>
            <div class="card-body">
                <div class="chart-wrapper">
                <div id="mixed-chart-1"></div>
                </div>
            </div>

            </div>


        </div>
        <div class="col-xl-4">
            <!-- Current Users  -->
            <div class="card card-default">
            <div class="card-header">
                <h2>Current Users</h2>
                <span>Realtime</span>
            </div>
            <div class="card-body">
                <div id="barchartlg2"></div>
            </div>
            <div class="card-footer bg-white py-4">
                <a href="#" class="text-uppercase">Current Users Overview</a>
            </div>
            </div>
        </div>
        </div>

    @push('css')
        <!-- Plugins & Core Styles -->
        <link href="{{asset('assets/admin/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">

        <!-- Material Design Icons (MDI) - আইকনগুলির জন্য যেমন mdi-arrow-up-bold -->
        <link href="{{asset('assets/admin/plugins/material/css/materialdesignicons.min.css')}}" rel="stylesheet">

        <!-- Primary Theme CSS (Dashboard-এর বিশেষ স্টাইল) -->
        <link href="{{asset('assets/admin/css/style.css')}}" rel="stylesheet">
    @endpush
    
    @push('script')
        <!-- Core & Utility JS -->
        <script src="{{asset('assets/admin/plugins/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

        <!-- Charting Library (আপনার কোডে spline-area, mixed-chart থাকার জন্য এটি জরুরি) -->
        <script src="{{asset('assets/admin/plugins/apexcharts/apexcharts.min.js')}}"></script>

        <!-- Template Specific JS (যেমন অফক্যানভাস, ড্রপডাউন এবং অন্যান্য UI ফাংশনালিটির জন্য) -->
        <script src="{{ asset('assets/admin/js/chart.js') }}"></script>

        <!-- Chart Initialization JS (যেখানে চার্টের ডেটা ও অপশন ডিফাইন করা আছে) -->
        <!-- এই ফাইলে #spline-area-1, #mixed-chart-1 এ ডেটা লোড করার কোড থাকবে -->
        <script src="{{asset('assets/admin/js/chart-settings.js')}}"></script>
    @endpush
    
</x-admin-layout>