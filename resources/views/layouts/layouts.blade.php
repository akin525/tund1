<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,minimal-ui">
    <title>PLANETF | Dashboard</title>
    <meta content="Admin Dashboard" name="description">
    <meta content="5Star Company" name="author">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="shortcut icon" href="img/mcd_logo.png">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/icons.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/style.css" rel="stylesheet" type="text/css">
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <!-- DataTables -->
    <link href="/assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css">
    <!-- Responsive datatable examples -->
    <link href="/assets/plugins/datatables/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css">
    @yield('after-style')
</head>
<body class="fixed-left">
<!-- Loader -->
<div id="preloader">
    <div id="status">
{{--        <lottie-player src="/assets/cheapprogress.json" background="transparent"  speed="0.5"  style="width: 150px; height: 150px;" loop autoplay></lottie-player>--}}
        <div class="spinner"></div>
    </div>
</div>
<!-- Begin page -->
<div id="wrapper">
    <!-- ========== Left Sidebar Start ========== -->
    <div class="left side-menu">
        <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect"><i class="ion-close"></i></button><!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center bg-logo">
                <a href="#" class="logo"><i class="mdi mdi-bowling text-success"></i> PLANETF</a><!-- <a href="index.html" class="logo"><img src="assets/images/logo.png" height="24" alt="logo"></a> -->
            </div>
        </div>
        <div class="sidebar-user">
            @if(\Illuminate\Support\Facades\Auth::user()->photo)
                <img src="https://mcd.5starcompany.com.ng/app/avatar/{{\Illuminate\Support\Facades\Auth::user()->photo}}" alt="user" class="rounded-circle img-thumbnail mb-1">
            @else
                <img src="/img/mcd_logo.png" alt="user" class="rounded-circle img-thumbnail mb-1">
            @endif

            <h6 class="">{{\Illuminate\Support\Facades\Auth::user()->full_name}}</h6>
            <p class="online-icon text-dark"><i class="mdi mdi-record text-success"></i>online</p>
            <ul class="list-unstyled list-inline mb-0 mt-2">
                <li class="list-inline-item"><a href="/profile/{{\Illuminate\Support\Facades\Auth::user()->user_name}}" class="" data-toggle="tooltip" data-placement="top" title="Profile"><i class="dripicons-user text-purple"></i></a></li>
                <li class="list-inline-item"><a href="{{route('allsettings')}}" class="" data-toggle="tooltip" data-placement="top" title="Settings"><i class="dripicons-gear text-dark"></i></a></li>
                <li class="list-inline-item"><a href="/logout" class="" data-toggle="tooltip" data-placement="top" title="Log out"><i class="dripicons-power text-danger"></i></a></li>
            </ul>
        </div>
        <div class="sidebar-inner slimscrollleft">
            <div id="sidebar-menu">
                <ul>
                    <li class="menu-title">Main</li>
                    <li><a href="/home" class="waves-effect"><i class="dripicons-device-desktop"></i> <span>Dashboard</span></a></li>
                    <li><a href="{{route('addgnews')}}" class="waves-effect"><i class="dripicons-bell"></i> <span>Announcement</span></a></li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-blog"></i><span> Transactions </span>
                            {{--                            <span class="badge badge-pill badge-info float-right">8</span>--}}
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                        </a>
                        <ul class="list-unstyled">
                            <li><a href="/transaction">Transaction History</a></li>
                            <li><a href="{{route('trans_pending')}}">Pending Transactions</a></li>
{{--                            <li><a href="/generalmarket">General Market</a></li>--}}
                            <li><a href="{{route('findtransaction')}}">Find Transaction</a></li>

                            {{--                                <li><a href="/cryptorequest">Crypto Request</a></li>--}}
                            <li><a href="/addtransaction">Add Airtime Transaction</a></li>
                            <li><a href="/adddatatransaction">Add Data Transaction</a></li>
                            <li><a href="/airtime2cash">Airtime Converter</a></li>
                            <li><a href="/reversal">Reverse Transaction</a></li>
                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i
                                class="dripicons-wallet"></i><span> Wallet </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            {{--                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>--}}
                        </a>
                        <ul class="list-unstyled">
                            @if(\Illuminate\Support\Facades\Auth::user()->status == "superadmin")
                                <li><a href="{{route('addfund')}}">Credit/Debit User</a></li>
                            @endif
                            <li><a href="/wallet">Wallet</a></li>
{{--                            <li><a href="{{route('withdrawal_list')}}">Withdrawal</a></li>--}}
                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-user-group"></i> <span>Users </span>
                            <span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="/users">Users</a></li>
                            <li><a href="/finduser">Search User(s)</a></li>
{{--                            <li><a href="/agentpayment">Agent Payment</a></li>--}}
                            <li><a href="/loginattempts">Login Attempts</a></li>
{{--                            <li><a href="/agents">Agents</a></li>--}}
                            <li><a href="/resellers">Resellers</a></li>
{{--                            <li><a href="/pending_request">Pending Request</a></li>--}}
{{--                            <li><a href="/gmblocked">GM Blocked</a></li>--}}
                            <li><a href="/dormantusers">Dormant Users</a></li>

{{--                            @if(\Illuminate\Support\Facades\Auth::user()->status == "superadmin")--}}
{{--                                <li><a href="/referral_upgrade">Referral Upgrade</a></li>--}}
{{--                            @endif--}}
                        </ul>
                    </li>
                    @if(\Illuminate\Support\Facades\Auth::user()->status == "superadmin")
                        <li><a href="/role" class="waves-effect"><i class="dripicons-user"></i> <span>Assigned Role
{{--                                <span class="badge badge-pill badge-primary float-right">7</span>--}}
                            </span></a></li>
                        <li><a href="{{route('paymentgateway')}}" class="waves-effect"><i class="dripicons-card"></i> <span>Payment Gateway
                            </span></a></li>

                        <li><a href="{{route('sliders.index')}}" class="waves-effect"><i class="dripicons-bookmark"></i> <span>Slider
{{--                                <span class="badge badge-pill badge-primary float-right">7</span>--}}
                            </span></a></li>
                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-gear"></i><span> Services Control</span><span
                                    class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                <li><a href="{{route('airtimecontrol')}}">Airtime Control</a></li>
                                <li><a href="{{route('dataplans')}}">Data Plans</a></li>
                                <li><a href="{{route('tvcontrol')}}">TV Plans</a></li>
                                <li><a href="{{route('electricitycontrol')}}">Electricity Control</a></li>
                            </ul>
                        </li>

                        <li class="has_sub">
                            <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-gear"></i><span> Reseller Control</span><span
                                    class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                            <ul class="list-unstyled">
                                <li><a href="{{route('reseller.airtimecontrol')}}">Airtime Control</a></li>
                                <li><a href="{{route('reseller.dataplans')}}">Data Plans</a></li>
                                <li><a href="{{route('reseller.tvcontrol')}}">TV Plans</a></li>
                            </ul>
                        </li>
                    @endif
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-card"></i><span> Reports </span><span
                                class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>
                        <ul class="list-unstyled">
                            <li><a href="{{route('report_yearly')}}">Yearly Report</a></li>
                            <li><a href="{{route('report_monthly')}}">Monthly Report</a></li>
                            <li><a href="{{route('report_daily')}}">Daily Report</a></li>
                        </ul>

                    {{--                    </li>--}}
                    {{--                    <li class="menu-title">Extra</li>--}}
                    {{--                    <li class="has_sub">--}}
                    {{--                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-map"></i><span> Maps </span><span class="badge badge-pill badge-danger float-right">2</span></a>--}}
                    {{--                        <ul class="list-unstyled">--}}
                    {{--                            <li><a href="maps-google.html">Google Map</a></li>--}}
                    {{--                            <li><a href="maps-vector.html">Vector Map</a></li>--}}
                    {{--                        </ul>--}}
                    {{--                    </li>--}}
{{--                    <li class="has_sub">--}}
{{--                        <a href="javascript:void(0);" class="waves-effect"><i class="dripicons-copy"></i><span> Pages </span><span class="float-right"><i class="mdi mdi-chevron-right"></i></span></a>--}}
{{--                        <ul class="list-unstyled">--}}
                    {{--                            <li><a href="pages-login.html">Login</a></li>--}}
                    {{--                            <li><a href="pages-register.html">Register</a></li>--}}
                    {{--                            <li><a href="pages-recoverpw.html">Recover Password</a></li>--}}
                    {{--                            <li><a href="pages-lock-screen.html">Lock Screen</a></li>--}}
                    {{--                            <li><a href="pages-blank.html">Blank Page</a></li>--}}
                    {{--                            <li><a href="pages-404.html">Error 404</a></li>--}}
                    {{--                            <li><a href="pages-500.html">Error 500</a></li>--}}
                    {{--                        </ul>--}}
                    {{--                    </li>--}}

{{--                    <li><a href="{{route('virtual-accounts')}}" class="waves-effect"><i class="dripicons-archive"></i>--}}
{{--                            <span>Virtual Accounts--}}
{{--                                <span class="badge badge-pill badge-primary float-right">7</span>--}}
{{--                            </span></a></li>--}}

                    <li><a href="{{route('faqs.index')}}" class="waves-effect"><i class="dripicons-archive"></i>
                            <span>FAQs</span></a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>
        <!-- end sidebarinner -->
    </div>
    <!-- Left Sidebar End --><!-- Start right Content here -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <!-- Top Bar Start -->
            <div class="topbar">
                <nav class="navbar-custom">
                    <ul class="list-inline float-right mb-0">
                        <!-- language-->
{{--                        <li class="list-inline-item dropdown notification-list hide-phone">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect text-white" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">English <img src="assets/images/flags/us_flag.jpg" class="ml-2" height="16" alt=""></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right language-switch"><a class="dropdown-item" href="#"><img src="assets/images/flags/italy_flag.jpg" alt="" height="16"><span>Italian </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/french_flag.jpg" alt="" height="16"><span>French </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/spain_flag.jpg" alt="" height="16"><span>Spanish </span></a><a class="dropdown-item" href="#"><img src="assets/images/flags/russia_flag.jpg" alt="" height="16"><span>Russian</span></a></div>--}}
{{--                        </li>--}}
{{--                        <li class="list-inline-item dropdown notification-list">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="dripicons-mail noti-icon"></i> <span class="badge badge-danger noti-icon-badge">5</span></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">--}}
{{--                                <!-- item-->--}}
{{--                                <div class="dropdown-item noti-title">--}}
{{--                                    <h5><span class="badge badge-danger float-right">745</span>Messages</h5>--}}
{{--                                </div>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-2.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Charles M. Jones</b><small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-3.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Thomas J. Mimms</b><small class="text-muted">You have 87 unread messages</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon"><img src="assets/images/users/avatar-4.jpg" alt="user-img" class="img-fluid rounded-circle"></div>--}}
{{--                                    <p class="notify-details"><b>Luis M. Konrad</b><small class="text-muted">It is a long established fact that a reader will</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- All--> <a href="javascript:void(0);" class="dropdown-item notify-item border-top">View All</a>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--                        <li class="list-inline-item dropdown notification-list">--}}
{{--                            <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false"><i class="dripicons-bell noti-icon"></i> <span class="badge badge-success noti-icon-badge">2</span></a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">--}}
{{--                                <!-- item-->--}}
{{--                                <div class="dropdown-item noti-title">--}}
{{--                                    <h5><span class="badge badge-danger float-right">87</span>Notification</h5>--}}
{{--                                </div>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-primary"><i class="mdi mdi-cart-outline"></i></div>--}}
{{--                                    <p class="notify-details"><b>Your order is placed</b><small class="text-muted">Dummy text of the printing and typesetting industry.</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-success"><i class="mdi mdi-message"></i></div>--}}
{{--                                    <p class="notify-details"><b>New Message received</b><small class="text-muted">You have 87 unread messages</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- item-->--}}
{{--                                <a href="javascript:void(0);" class="dropdown-item notify-item">--}}
{{--                                    <div class="notify-icon bg-warning"><i class="mdi mdi-glass-cocktail"></i></div>--}}
{{--                                    <p class="notify-details"><b>Your item is shipped</b><small class="text-muted">It is a long established fact that a reader will</small></p>--}}
{{--                                </a>--}}
{{--                                <!-- All--> <a href="javascript:void(0);" class="dropdown-item notify-item border-top">View All</a>--}}
{{--                            </div>--}}
{{--                        </li>--}}
                        <li class="list-inline-item dropdown notification-list">
                            <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                @if(\Illuminate\Support\Facades\Auth::user()->photo)
                                    <img src="https://mcd.5starcompany.com.ng/app/avatar/{{\Illuminate\Support\Facades\Auth::user()->photo}}" alt="user" class="rounded-circle">
                                @else
                                    <img src="img/mcd_logo.png" alt="user" class="rounded-circle">
                                @endif
                            </a>
                            <div class="dropdown-menu dropdown-menu-right profile-dropdown">
                                <!-- item-->
                                <div class="dropdown-item noti-title">
                                    <h5>Welcome</h5>
                                </div>
                                <a class="dropdown-item" href="/profile/{{\Illuminate\Support\Facades\Auth::user()->user_name}}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                <a class="dropdown-item" href="{{route('allsettings')}}"><span class="badge badge-success float-right">5</span><i class="mdi mdi-settings m-r-5 text-muted"></i> Settings</a>
{{--                                <a class="dropdown-item" href="#"><i class="mdi mdi-lock-open-outline m-r-5 text-muted"></i> Lock screen</a>--}}
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="/logout"><i class="mdi mdi-logout m-r-5 text-muted"></i> Logout</a>
                            </div>
                        </li>
                    </ul>
                    <ul class="list-inline menu-left mb-0">
                        <li class="float-left"><button class="button-menu-mobile open-left waves-light waves-effect"><i class="mdi mdi-menu"></i></button></li>
                        <li class="hide-phone app-search">
                            <form role="search" class=""><input type="text" placeholder="Search..." class="form-control"> <a href="#"><i class="fas fa-search"></i></a></form>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </nav>
            </div>
            <!-- Top Bar End -->
            <div class="page-content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="page-title-box">
                                <div class="btn-group float-right">
                                    <ol class="breadcrumb hide-phone p-0 m-0">
                                        @if (trim($__env->yieldContent('parentPageTitle')))
                                            <li class="breadcrumb-item"><a href="#">@yield('parentPageTitle')</a></li>
                                        @endif
                                        @if (trim($__env->yieldContent('title')))
                                            <li class="breadcrumb-item active">@yield('title')</li>
                                        @endif
                                    </ol>
                                </div>
                                @if (trim($__env->yieldContent('title')))
                                    <h4 class="page-title">@yield('title')</h4>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- end page title end breadcrumb -->
                    @yield('content')
                </div>
                <!-- Page content Wrapper -->
            </div>
            <!-- container -->
            </div>
            <!-- content -->
            <footer class="footer">
                © 2020 PLANETF
            </footer>
        </div>
        <!-- End Right content here -->
    </div>
    <!-- END wrapper --><!-- jQuery  --><script src="/assets/js/jquery.min.js"></script><script src="/assets/js/popper.min.js"></script><script src="/assets/js/bootstrap.min.js"></script><script src="/assets/js/modernizr.min.js"></script><script src="/assets/js/detect.js"></script><script src="/assets/js/fastclick.js"></script><script src="/assets/js/jquery.slimscroll.js"></script><script src="/assets/js/jquery.blockUI.js"></script><script src="/assets/js/waves.js"></script><script src="/assets/js/jquery.nicescroll.js"></script><script src="/assets/js/jquery.scrollTo.min.js"></script><script src="/assets/plugins/chart.js/chart.min.js"></script><script src="/assets/pages/dashboard.js"></script><!-- App js --><script src="/assets/js/app.js"></script>
<script src="/assets/js/jquery.scrollTo.min.js"></script><!-- Required datatable js --><script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script><script src="/assets/plugins/datatables/dataTables.bootstrap4.min.js"></script><!-- Buttons examples --><script src="/assets/plugins/datatables/dataTables.buttons.min.js"></script><script src="/assets/plugins/datatables/buttons.bootstrap4.min.js"></script><script src="/assets/plugins/datatables/jszip.min.js"></script><script src="/assets/plugins/datatables/pdfmake.min.js"></script><script src="/assets/plugins/datatables/vfs_fonts.js"></script><script src="/assets/plugins/datatables/buttons.html5.min.js"></script><script src="/assets/plugins/datatables/buttons.print.min.js"></script><script src="/assets/plugins/datatables/buttons.colVis.min.js"></script><!-- Responsive examples --><script src="/assets/plugins/datatables/dataTables.responsive.min.js"></script><script src="/assets/plugins/datatables/responsive.bootstrap4.min.js"></script><!-- Datatable init js --><script src="/assets/pages/datatables.init.js"></script>
@yield('before-scripts')
</body>
</html>
