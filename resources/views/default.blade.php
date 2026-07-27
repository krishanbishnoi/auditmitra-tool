<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo Config::get("Site.title"); ?></title>

    <!-- plugins:css -->
    <script src="{{ asset('all-cdn/jquery-3.5.1.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link href="{{ asset('css/developer.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/favicon.jpg') }}" />
    {{-- <link rel="stylesheet" href="{{ asset('old/css/bootstrap.min.css') }}" > --}}
    <!-- //bootstrap-css -->
    <!-- Custom CSS -->
    <!-- font CSS -->
    <link
        href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic'
        rel='stylesheet' type='text/css'>
    <!-- font-awesome icons -->
    <link rel="stylesheet" href="{{ asset('old/css/font.css') }}" type="text/css" />
    <!-- <link href="{{ asset('old/css/font-awesome.css') }}"   rel="stylesheet"> -->
    <!-- <link href="{{ asset('old/css/font-awesome.min.css') }}" rel="stylesheet"> -->

    <link rel="stylesheet" href="{{ asset('all-cdn/fontawesomeall.min.css') }}">



    <link href="{{ asset('old/css/notification/jquery.toastmessage.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('old/css/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" />


    <link rel="stylesheet" href="{{ asset('old/css/morris.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('old/css/monthly.css') }}">
    <!-- //calendar -->
    <!-- //font-awesome icons -->

    <link href="{{ URL::asset('old/css/bootstrap-datetimepicker.css') }}" rel="stylesheet" />

    <script src="{{ URL::asset('all-cdn/ckeditor/ckeditor.js') }}"></script>


</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="" href="{{ route('dashboard')}}"><img height="100%" width="100%"
                        src="{{ asset('images/airtel-payment-bank.jpg') }}" alt="logo" /></a>

            </div>
            <br>
            <div class="navbar-menu-wrapper d-flex align-items-stretch">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="mdi mdi-menu"></span>
                </button>
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            {{-- <div class="nav-profile-img">
                                <img src="{{ asset('images/faces/face1.jpg') }}" alt="image">
                            <span class="availability-status online"></span>
            </div> --}}
            <div class="nav-profile-text">
                <p class="mb-1 text-black">{{Auth::user()->first_name}} {{Auth::user()->last_name}}</p>
            </div>
            </a>
            <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="{{ URL('admin/myaccount')}}">
                    <i class="mdi  me-2 text-success"></i> Profile </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ URL('admin/change-password')}}">
                    <i class="mdi  me-2 text-primary"></i> Change Password </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ URL('admin/logout')}}">
                    <i class="mdi me-2 text-primary"></i> Signout </a>
            </div>



            </li>
            <!-- <li class="nav-item d-none d-lg-block full-screen-link">
                        <a class="nav-link">
                            <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-email-outline"></i>
                            <span class="count-symbol bg-warning"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="messageDropdown">
                            <h6 class="p-3 mb-0">Messages</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <img src="{{ asset('images/faces/face4.jpg') }}" alt="image" class="profile-pic">
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Mark send you a message
                                    </h6>
                                    <p class="text-gray mb-0"> 1 Minutes ago </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <img src="{{ asset('images/faces/face2.jpg') }}" alt="image" class="profile-pic">
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Cregh send you a
                                        message</h6>
                                    <p class="text-gray mb-0"> 15 Minutes ago </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <img src="{{ asset('images/faces/face3.jpg') }}" alt="image" class="profile-pic">
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Profile picture updated
                                    </h6>
                                    <p class="text-gray mb-0"> 18 Minutes ago </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <h6 class="p-3 mb-0 text-center">4 new messages</h6>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-bs-toggle="dropdown">
                            <i class="mdi mdi-bell-outline"></i>
                            <span class="count-symbol bg-danger"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="notificationDropdown">
                            <h6 class="p-3 mb-0">Notifications</h6>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-success">
                                        <i class="mdi mdi-calendar"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Event today</h6>
                                    <p class="text-gray ellipsis mb-0"> Just a reminder that you have an event today
                                    </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-warning">
                                        <i class="mdi mdi-settings"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Settings</h6>
                                    <p class="text-gray ellipsis mb-0"> Update dashboard </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class="mdi mdi-link-variant"></i>
                                    </div>
                                </div>
                                <div
                                    class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                                    <h6 class="preview-subject font-weight-normal mb-1">Launch Admin</h6>
                                    <p class="text-gray ellipsis mb-0"> New admin wow! </p>
                                </div>
                            </a>
                            <div class="dropdown-divider"></div>
                            <h6 class="p-3 mb-0 text-center">See all notifications</h6>
                        </div>
                    </li> -->

            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                data-toggle="offcanvas">
                <span class="mdi mdi-menu"></span>
            </button>
    </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->

        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <?php
                       $segment1	=	Request::segment(1);
                        $segment2	=	Request::segment(2);
                        $segment3	=	Request::segment(3);
                        $segment4	=	Request::segment(4);
                    ?>


                <li class="nav-item">
                    <a class="nav-link" href="{{ route('dashboard')}}">
                        <span class="menu-title">Dashboard</span>
                        <i class="mdi m             di-home menu-icon"></i>
                    </a>
                </li>


                <li class="nav-item <?php echo request()->segment(2) === 'trainers' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Trainers.index')}}">
                        <span class="m         enu-title">Trainers Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>
                @if ( Auth::user()->user_role_id == 1)
                <li class="nav-item <?php echo request()->segment(2) === 'training-managers' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Users.index')}}">
                        <span class="menu-title" >Manager Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>
                @endif
                <li class="nav-item <?php echo request()->segment(2) === 'users' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Trainees.index')}}">
                        <span class="menu-title">Users Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item <?php echo request()->segment(2) === 'tests' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Test.index')}}">
                        <span class="menu-title">Test Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item <?php echo request()->segment(2) === 'trainings' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Training.index')}}">
                        <span class="menu-title">Training Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>

                @if ( Auth::user()->user_role_id == 1)
                <li class="nav-item <?php echo request()->segment(2) === 'reports' ? 'active' : ''; ?>">
                    <a class="nav-link" href="{{ route('Reports.index')}}">
                                       <span class="menu-title">Reports Management</span>
                        <i class="mdi mdi-account menu-icon"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" href="#email" aria-expanded="false"
                        aria-controls="ui-basic">
                        <span class="menu-title">Email Template</span>
                        <i class="menu-arrow"></i>
                        <i class="mdi mdi-email menu-icon"></i>
                    </a>
                    <div class="collapse" id="email">
                        <ul class="nav flex-column sub-menu">
                            <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif>
                                <a class="nav-link" href="{{ route('EmailTemplate.index')}}">Email Template</a></li>
                            <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif>
                                <a class="nav-link" href="{{ route('EmailLogs.listEmail')}}">Email Logs</a></li>
                            {{-- <li class="nav-item" @if($segment1=='settings' && $segment3=='Site') class="active" @endif> <a class="nav-link"  href="{{ route('TeamMember.index')}}">Social
                            Setting</a>
                </li> --}}
            </ul>
    </div>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#Masters" aria-expanded="false" aria-controls="ui-basic">
            <span class="menu-title">Masters Management</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        </a>
        <div class="collapse" id="Masters">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item" @if($segment1=='lobs' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ route('Lob.index')}}">LOB Management</a></li>
                <li class="nav-item" @if($segment1=='designation' && $segment3=='Site' ) class="active" @endif> <a
                class="nav-link" href="{{ route('Designation.index')}}">Designation Management</a></li>
                <li class="nav-item" @if($segment1=='regions' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ route('Region.index')}}">Region Management</a></li>
                <li class="nav-item" @if($segment1=='circles' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ route('Circle.index')}}">Circle Management</a></li>
                <li class="nav-item" @if($segment1=='training-types' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ route('TrainingType.index')}}">TrainingType Management</a></li>
                <li class="nav-item" @if($segment1=='partners' && $segment3=='Site' ) class="active" @endif> <a
                class="nav-link" href="{{ route('Partner.index')}}">Partner Management</a></li>
                <li class="nav-item" @if($segment1=='domains' && $segment3=='Site' ) class="active" @endif> <a
                class="nav-link" href="{{ route('Domain.index')}}">Domain Management</a></li>

            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#cms" aria-expanded="false" aria-controls="ui-basic">
            <span class="menu-title">Page Management</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-book-open-page-variant menu-icon"></i>
        </a>
        <div class="collapse" id="cms">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ route('Cms.index')}}">Cms Page Management</a></li>

                {{-- <li class="nav-item" @if($segment1=='settings' && $segment3=='Site') class="active" @endif> <a class="nav-link"  href="{{ route('TeamMember.index')}}">Social
                Setting</a> --}}
    </li>


    </ul>
    </div>
    </li>

    <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="ui-basic">
            <span class="menu-title">Settings</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-settings
                            menu-icon"></i>
        </a>
        <div class="collapse" id="settings">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ URL('admin/settings/prefix/Site')}}">Site Setting</a></li>
                <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ URL('admin/settings/prefix/Reading')}}">Reading Setting</a></li>
                <!-- <li class="nav-item" @if($segment1=='settings' && $segment3=='Site') class="active" @endif> <a class="nav-link"  href="{{ URL('admin/settings/prefix/Social')}}">Social Setting</a></li> -->
                <li class="nav-item" @if($segment1=='settings' && $segment3=='Site' ) class="active" @endif> <a
                        class="nav-link" href="{{ URL('admin/settings/prefix/Contact')}}">Contact Setting</a></li>
            </ul>
        </div>
    </li>
@endif

    </ul>
    </nav>
    <!-- partial -->
    <div class="main-panel">

        @if(Session::has('error'))
        <script type="text/javascript">
        $(document).ready(function(e) {

            show_message("{{{ Session::get('error') }}}", 'error');
        });
        </script>
        @endif
        @if(Session::has('success'))
        <script type="text/javascript">
        $(document).ready(function(e) {
            show_message("{{{ Session::get('success') }}}", 'success');
        });
        </script>
        @endif
        @if(Session::has('flash_notice'))
        <script type="text/javascript">
        $(document).ready(function(e) {
            show_message("{{{ Session::get('flash_notice') }}}", 'success');
        });
        </script>
        @endif
        @yield('content')
        <script type="text/javascript">
        function show_message(message, message_type) {
            $().toastmessage('showToast', {
                text: message,
                sticky: false,
                position: 'top-right',
                type: message_type,
            });
        }

        $(function() {

            $(document).on('click', '.delete_any_item', function(e) {
                e.stopImmediatePropagation();
                url = $(this).attr('href');
                bootbox.confirm("Are you sure want to delete this ?",
                    function(result) {
                        if (result) {
                            window.location.replace(url);
                        }
                    });
                e.preventDefault();
            });

            /**
             * Function to change status
             *
             * @param null
             *
             * @return void
             */
            $(document).on('click', '.status_any_item', function(e) {
                e.stopImmediatePropagation();
                url = $(this).attr('href');
                bootbox.confirm("Are you sure want to change status ?",
                    function(result) {
                        if (result) {
                            window.location.replace(url);
                        }
                    });
                e.preventDefault();
            });

        });


        //  $(function() {
        //         $(this).bind("contextmenu", function(e) {
        //             e.preventDefault();
        //         });
        //     });
        </script>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        {{-- <footer class="footer">
                    <div class="container-fluid d-flex justify-content-between">
                        <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Copyright ©
                            Airtel 2021</span>
                    </div>
                </footer> --}}
        <!-- partial -->
    </div>
    <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="{{ URL::asset('vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ URL::asset('vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ URL::asset('js/jquery.cookie.js') }}" type="text/javascript"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ URL::asset('js/off-canvas.js') }}"></script>
    {{-- <script src="{{ URL::asset('js/hoverable-collapse.js') }}"></script> --}}
    <script src="{{ URL::asset('js/misc.') }}js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <!-- <script src="{{ URL::asset('js/dashboard.js') }}"></script> -->
    <script src="{{ URL::asset('js/todolist.js') }}"></script>

    <script src="{{ URL::asset('all-cdn/ckeditor/ckeditor.js') }}"></script>

    <script src="{{ URL::asset('old/js/jquery2.0.3.min.js') }}"></script>
    <script src="{{ URL::asset('old/js/formValidation.js') }}"></script>
    <script src="{{ URL::asset('old/js/bootbox.js') }}"></script>
    {{-- <script src="{{ URL::asset('old/js/framework/bootstrap.js') }}" ></script> --}}

    <script src="{{ URL::asset('old/js/raphael-min.js') }}"></script>
    <script src="{{ URL::asset('old/js/morris.js') }}"></script>
    <script src="{{ URL::asset('old/js/moment.js') }}"></script>
    <script src="{{ URL::asset('old/js/bootstrap-datetimepicker.js') }}"></script>

    <script src="{{ URL::asset('old/css/notification/jquery.toastmessage.js') }}"></script>
    <!-- End custom js for this page -->
</body>

</html>
<style>
.error-message {
    color: red;
}

.help-inline {
    color: red;
}

.pull-right {
    float: right !important;
}
</style>
