<!doctype html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QDegree - People Progress Productivity</title>
    <meta name="description" content="QDegree - People Progress Productivity">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="{{URL::asset('/public/favicon.ico')}}">
    <link rel="shortcut icon" href="{{URL::asset('/public/favicon.ico')}}">

    <link href="{{URL::asset('/public/assets/css/normalize.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/bootstrap.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/font-awesome.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/themify-icons.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/pe-icon-7-stroke.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/flag-icon.min.css')}}" rel="stylesheet"/>
    <link href="{{URL::asset('/public/assets/css/cs-skin-elastic.css')}}" rel="stylesheet"/>
    <link rel="stylesheet" href="{{URL::asset('/public/assets/css/style.css')}}">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
</head>
<style>
body {
        font-family: -apple-system,BlinkMacSystemFont,segoe ui,Roboto,helvetica neue,Arial,sans-serif,apple color emoji,segoe ui emoji,segoe ui symbol,noto color emoji;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.5;
    /* color: #23282c; */
    }

.bg-dark{
    background-color: #ffffff !important;
}
.login-form{
    background: #fafafa;
    width: 460px;
    margin: 0 auto;
}
.login-button {
    background-color: #dd0886;
}
.login-form label a {
    color: #2e3992;
}
</style>
<body class="bg-dark">
@yield('content')

    <script src="{{URL::asset('/public/js/jquery.min.js')}}"></script>
    <script src="{{URL::asset('/public/js/popper.min.js')}}"></script>
    <script src="{{URL::asset('/public/js/bootstrap.min.js')}}"></script>
    <script src="{{URL::asset('/public/js/jquery.matchHeight.min.js')}}"></script>
    <script src="{{URL::asset('/public/assets/js/main.js')}}"></script>

</body>
</html>