<!doctype html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9" lang=""> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang=""> <!--<![endif]-->
<!-- <head> -->
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Audit Mitr</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="{{URL::asset('/public/fav_icon.png')}}">
    <link rel="shortcut icon" href="{{URL::asset('/public/fav_icon.png')}}">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="{{URL::asset('/public/assets/css/cs-skin-elastic.css')}}">
    <link rel="stylesheet" href="{{URL::asset('/public/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{URL::asset('/public/assets/css/lib/chosen/chosen.min.css')}}">
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/weathericons@2.1.0/css/weather-icons.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" rel="stylesheet"/>

    <!-- for sidebar icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@yield('css')
<style>
        #weatherWidget .currentDesc {
            color: #ffffff !important;
        }

        .traffic-chart {
            min-height: 335px;
        }

        #flotPie1 {
            height: 150px;
        }

        #flotPie1 td {
            padding: 3px;
        }

        #flotPie1 table {
            top: 20px !important;
            right: -10px !important;
        }

        .chart-container {
            display: table;
            min-width: 270px;
            text-align: left;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        #flotLine5 {
            height: 105px;
        }

        #flotBarChart {
            height: 150px;
        }

        #cellPaiChart {
            height: 160px;
        }
        
        .kt-separator.kt-separator--space-lg {
        margin: 2.5rem 0;
    }

.right-panel .navbar-brand img {
    max-width: 202px;
}
/* When sidebar is collapsed */
.left-panel.open-menu .menu-item-has-children.show .sub-menu,
.left-panel:not(:visible) .menu-item-has-children.show .sub-menu {
    position: absolute;
    left: 100%;
    top: 0;
    min-width: 200px;
    z-index: 1000;
    display: block !important;
    box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
}

/* Hide submenu by default when collapsed */
.left-panel.open-menu .sub-menu,
.left-panel:not(:visible) .sub-menu {
    display: none;
}

/* Keep the parent menu visible when submenu is shown */
.left-panel.open-menu .menu-item-has-children.show > a,
.left-panel:not(:visible) .menu-item-has-children.show > a {
    position: relative;
    z-index: 1001;
}

</style>


</head>

<body>
@include('chat.help-chat-widget')
@include('layouts.sidebar')
<div id="right-panel" class="right-panel">
    <!-- Header-->
    <header id="header" class="header">
        <div class="top-left">
    <div class="navbar-header d-flex justify-content-between align-items-center" style="width: 100%;height:50px;">
        
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
            @php
                $user = Auth::user();
                $client = \App\User::where('id', $user->client_id)->first();

                $logo = $client && $client->logo
                    ? asset('storage/app/public/' . $client->logo)
                    : url('public/images/app_logo.png');
            @endphp

            <img src="{{ $logo }}"
                 alt="Logo"
                 style="margin-top:6px;width: 140px; height: 50px; object-fit: cover; border-radius: 4px;">
        </a>

        <!-- Menu Toggle on Right -->
        <a id="menuToggle" class="menutoggle" style="margin-right:90px;cursor:pointer;">
            <i class="fa fa-bars"></i>
        </a>
    </div>
</div>

        <div class="top-right">
            <div class="header-menu">
                <div class="header-left">
                   
                </div>
                  @php
                        $user = Auth::user();
                        $client = \App\User::find($user->client_id);
                        $isLegalRoute = request()->is('legal/*');
                    @endphp


                    {{-- User has BOTH audits --}}
                    @if ($client->is_legal == 1 && $client->is_compliance == 1)
                        <div class="vtext float-right mt-2 p-2 custom-rounded">
                            {{-- Currently on Compliance --}}
                            @if (!$isLegalRoute)
                                <a href="{{ route('legal.dashboard') }}">Legal Audit</a>
                            @else
                                {{-- Currently on Legal --}}
                                <a href="{{ route('dashboard') }}">Compliance Audit</a>
                            @endif
                        </div>

                    @endif
                <div class="vtext float-right mt-2 p-2 bg-ligh custom-rounded"> 
                    @if ($user)
                        <h5>Welcome, <span class="text-danger">{{ $user->name }} !</span></h5>
                    @else
                        <h5>Hello, Guest !</h5>
                    @endif
                </div>

                <div class="user-area dropdown float-right">
                    <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true"
                       aria-expanded="false">
                        <img class="user-avatar rounded-circle" src="{{ URL::asset('/public/images/avatar/' . (Auth::user()->avatar ? Auth::user()->avatar : 'default-image.jpg')) }}" alt="User Avatar">
                    </a>

                    <div class="user-menu dropdown-menu">
                        <a class="nav-link" href="{{ route('profile') }}"><i class="fa fa- user"></i>My Profile</a>
                        <a class="nav-link" href="{{ route('change-password') }}"><i class="fa fa- update"></i>Change Password</a>

                        <!-- <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications <span
                                    class="count">13</span></a> -->

                        <!-- <a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a> -->
                       {{-- <div class="dropdown-menu dropdown-menu-right " aria-labelledby="navbarDropdown">--}}
                            <a class="dropdown-item nav-link" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"><i class="fa fa-power -off"></i>
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <!-- <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a> -->
                        {{--</div>--}}
                        {{--<a class="nav-link" href="#"><i class="fa fa-power -off"></i>Logout</a>--}}
                    </div>
                </div>

            </div>
        </div>
    </header>
    <!-- /#header -->
    <div class="content" style="min-height:500px;bottom:0">
        @yield('content')
    </div>
</div>


<!-- Footer -->
    <footer class="site-footer"  style="bottom:0">
        <div class="footer-inner bg-white">
            <div class="row">
                {{-- <div class="col-sm-6">
                    Copyright &copy; 2019 Araways
                </div>
                <div class="col-sm-6 text-right">
                    Designed by <a href="#">Araways</a>
                </div> --}}
            </div>
        </div>
    </footer>
    <!-- /.site-footer -->
</div>

<!-- /#right-panel -->


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
<script src="{{URL::asset('/public/assets/js/main.js')}}"></script>
<script src="https:////cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https:////cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<!--  Chart js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

<!--Chartist Chart-->
<script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

{{--<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>--}}



<script src="{{asset('/public/assets/js/lib/chosen/chosen.jquery.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script src="{{URL::asset('/public/assets/js/init/fullcalendar-init.js')}}"></script>

<!--Local Stuff-->

<script>
    var url = "{!! url('') !!}";
    jQuery(document).ready(function () {

        toastr.options = {
            "toastClass": "animated fadeInDown",


            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        @if(session()->has('success'))
        @if(is_array(session()->get('success')))
            @foreach(session()->get('success') as $succ)
            toastr.success("{{$succ}}");
        @endforeach
        @else
        toastr.success("{{session()->get('success')}}");
        @endif

        @endif

        @if(session()->has('error'))
        @if(is_array(session()->get('error')))
            @foreach(session()->get('error') as $error)
        @if(is_array($error))
            @foreach($error as $err)
toastr.error("{{$err}}");
        @endforeach
        @endif

        @endforeach
        @endif
        @endif
    });

   
</script>


@yield('js')
</body>
</html>