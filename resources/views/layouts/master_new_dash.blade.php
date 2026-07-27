<!doctype html>
<html class="no-js" lang="">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- <title>@yield('title')</title> --}}
    <title>Audit Mitra</title>
    {{-- <meta name="description" content="Ela Admin - HTML5 Admin Template"> --}}
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

.kt-separator.kt-separator--border-dashed {
    border-bottom: 1px dashed #ebedf2;
}
.kt-separator {
    height: 0;
    margin: 20px 0;
    border-bottom: 1px solid #ebedf2;
}
.div1 {
    width: auto;
    height: auto;
    display: flex;
    overflow-x: auto;
  }
  
  .item {
    width: auto;
    flex-shrink: 0;
    height: auto;
    margin-right:5px;
  }
  .center{
    display: block;
  margin-left: auto;
  margin-right: auto;
  width: 50%;
  }
  .stat-widget-five .stat-heading {
    color: #99abb4;
    font-size: 13px;
}

.table thead th {
        font-size: 14px; /* Adjust as needed */
        font-weight: bold; /* Now fully bold */
        text-transform: uppercase;
        color: #333;
        /* background-color: #f1f1f1; */
    }
.productcontent{
    background-color: whitesmoke;
    border: 1px solid #dddddd;
    border-radius: 30px;
    padding: 10px 10px;
    margin-top: 10px;
}
.cursor{
    cursor: pointer;
}
.custom-rounded {
    border: 1px solid #ffffff;
    border-radius: 10px;
}
.vtext{
    margin: 0px 0px 12px 0px
}

.right-panel .navbar-brand img {
    max-width: 202px;
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
            <div class="navbar-header">
                
                <a class="navbar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
    @php
        $user = Auth::user();
        $client = \App\User::where('id', $user->client_id)->first();


        $logo = $client && $client->logo 
            ? asset('storage/app/public/' . $client->logo) 
            : url('public/images/app_logo.png');
    @endphp

    <img src="{{ $logo }}" 
         alt="Client Logo" 
         style="width: 140px; height: 50px; object-fit: cover; border-radius: 4px;">
</a>

                <!-- <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a> -->
            </div>
        </div>
        <div class="top-right">
            <div class="header-menu">
                <div class="header-left">
                    <!-- <button class="search-trigger"><i class="fa fa-search"></i></button>
                    <div class="form-inline">
                        <form class="search-form">
                            <input class="form-control mr-sm-2" type="text" placeholder="Search ..."
                                   aria-label="Search">
                            <button class="search-close" type="submit"><i class="fa fa-close"></i></button>
                        </form>
                    </div> -->

                </div>
                 @php
                    $user = Auth::user();
                @endphp
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
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>-->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>


<!--Chartist Chart-->
<script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

{{--<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>--}}
{{--<script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>--}}


<script src="{{asset('/public/assets/js/init/weather-init.js')}}"></script>
<script src="{{asset('/public/assets/js/lib/chosen/chosen.jquery.min.js')}}"></script>

<script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>
<script src="{{URL::asset('/public/assets/js/init/fullcalendar-init.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/flot-charts@0.8.3/jquery.flot.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.pie.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.time.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.stack.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.resize.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.crosshair.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flot.curvedlines@1.1.1/curvedLines.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.flot.tooltip@0.9.0/js/jquery.flot.tooltip.min.js"></script>





@yield('js')
</body>
</html>