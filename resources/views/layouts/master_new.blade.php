<!doctype html>

<!--[if lt IE 7]>

<html class="no-js lt-ie9 lt-ie8 lt-ie7" lang=""> <![endif]-->

<!--[if IE 7]>

<html class="no-js lt-ie9 lt-ie8" lang=""> <![endif]-->

<!--[if IE 8]>

<html class="no-js lt-ie9" lang=""> <![endif]-->

<!--[if gt IE 8]><!-->

<html class="no-js" lang=""> <!--<![endif]-->

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title')</title>

    {{-- <meta name="description" content="Ela Admin - HTML5 Admin Template"> --}}

    <meta name="viewport" content="width=device-width, initial-scale=1">



    <link rel="apple-touch-icon" href="{{URL::asset('favicon.ico')}}">

    <link rel="shortcut icon" href="{{URL::asset('favicon.ico')}}">



    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">

    <link rel="stylesheet"

        href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">

    <link rel="stylesheet" href="{{URL::asset('assets/css/cs-skin-elastic.css')}}">

    <link rel="stylesheet" href="{{URL::asset('assets/css/style.css')}}">

    <link rel="stylesheet" href="{{URL::asset('assets/css/lib/chosen/chosen.min.css')}}">

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->

    <link href="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/jqvmap@1.5.1/dist/jqvmap.min.css" rel="stylesheet">



    <link href="https://cdn.jsdelivr.net/npm/weathericons@2.1.0/css/weather-icons.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css" rel="stylesheet" />

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.css" rel="stylesheet" />

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

            margin-right: 5px;

        }

        .center {

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

            font-size: 12px;

        }

        .productcontent {

            background-color: whitesmoke;

            border: 1px solid #dddddd;

            border-radius: 30px;

            padding: 10px 10px;

            margin-top: 10px;

        }

        .cursor {

            cursor: pointer;

        }
    </style>

</head>



<body>





    <div>



        <div class="content" style="min-height:500px;bottom:0">

            @yield('content')

        </div>

    </div>





    <!-- Footer -->

    <footer class="site-footer" style="bottom:0">

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

    <script src="{{URL::asset('assets/js/main.js')}}"></script>

    <script src="https:////cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <link href="https:////cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    <!--  Chart js -->

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>



    <!--Chartist Chart-->

    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>



    {{--<script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>--}}

    {{--<script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>--}}

    {{--<script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>--}}







    <script src="{{URL::asset('assets/js/lib/chosen/chosen.jquery.min.js')}}"></script>



    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.js"></script>

    <script src="{{URL::asset('assets/js/init/fullcalendar-init.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/flot-charts@0.8.3/jquery.flot.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.pie.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.time.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.stack.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.resize.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.crosshair.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flot.curvedlines@1.1.1/curvedLines.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot.tooltip@0.9.0/js/jquery.flot.tooltip.min.js"></script>



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

        {{-- Success Messages --}}
        @if(session()->has('success'))
            @if(is_array(session()->get('success')))
                @foreach(session()->get('success') as $succ)
                    toastr.success("{{ $succ }}");
                @endforeach
            @else
                toastr.success("{{ session()->get('success') }}");
            @endif
        @endif

        {{-- Error Messages --}}
        @if(session()->has('error'))
            @if(is_array(session()->get('error')))
                @foreach(session()->get('error') as $error)
                    @if(is_array($error))
                        @foreach($error as $err)
                            toastr.error("{{ $err }}");
                        @endforeach
                    @else
                        toastr.error("{{ $error }}");
                    @endif
                @endforeach
            @else
                toastr.error("{{ session()->get('error') }}");
            @endif
        @endif
    });
</script>


    @yield('js')

</body>

</html>