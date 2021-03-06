<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Encrypted CSRF token for Laravel, in order for Ajax requests to work --}}
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>
      {{ isset($title) ? $title.' :: '.config('backpack.base.project_name').' Admin' : config('backpack.base.project_name').' Admin' }}
    </title>

    @yield('before_styles')

    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/dist/css/skins/_all-skins.min.css">

    <link rel="stylesheet" href="{{ asset('vendor/adminlte/') }}/plugins/pace/pace.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/backpack/pnotify/pnotify.custom.min.css') }}">

    <!-- BackPack Base CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/backpack/backpack.base.css') }}">

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    @yield('after_styles')

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<!-- add this class to close sidebar:  sidebar-collapse  -->
<body class="hold-transition {{ config('backpack.base.skin') }} sidebar-mini sidebar-collapse">
    <!-- Site wrapper -->
    <div class="wrapper">

    @if (Auth::check())
      <header class="main-header">
        <!-- Logo -->
        <a href="{{ url('') }}" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini">{!! config('backpack.base.logo_mini') !!}</span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg">{!! config('backpack.base.logo_lg') !!}</span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">{{ trans('backpack::base.toggle_navigation') }}</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          @include('backpack::inc.menu')
        </nav>
      </header>
    @endif

      @include('backpack::inc.sidebar')

      <div class=" {{ Auth::check() ? 'content-wrapper' : 'content-wrapper-logout'  }}">

         @yield('header')

        <section class="content">
          @yield('content')
        </section>

      </div>


    @if (Auth::check())
      <footer class="main-footer">
        @if (config('backpack.base.show_powered_by'))
            <div class="pull-right hidden-xs">
              {{ trans('backpack::base.powered_by') }} <a target="_blank" href="http://laravelbackpack.com">Laravel BackPack</a>
            </div>
        @endif
        {{ trans('backpack::base.handcrafted_by') }} <a target="_blank" href="{{ config('backpack.base.developer_link') }}">{{ config('backpack.base.developer_name') }}</a>.
      </footer>
    @endif

    </div>

    @yield('before_scripts')

    <!-- jQuery 2.2.0 -->
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <script src="{{ asset('vendor/adminlte') }}/bootstrap/js/bootstrap.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/pace/pace.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/plugins/fastclick/fastclick.js"></script>
    <script src="{{ asset('vendor/adminlte') }}/dist/js/app.min.js"></script>

    <!-- page script -->
    <script type="text/javascript">
        // To make Pace works on Ajax calls
        $(document).ajaxStart(function() { Pace.restart(); });

        // Ajax calls should always have the CSRF token attached to them, otherwise they won't work
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        // Set active state on menu element
        var current_url = "{{ url(Route::current()->getUri()) }}";
        $("ul.sidebar-menu li a").each(function() {
          if ($(this).attr('href').startsWith(current_url) || current_url.startsWith($(this).attr('href')))
          {
            $(this).parents('li').addClass('active');
          }
        });
    </script>

    <script>
        $( function() {
            $( ".datepicker" ).datepicker({
                dateFormat: "yy-mm-dd"
            });
        } );
    </script>

    <script>

        function make_request_to_get_total_info( url ){
            $.ajax({
                type: "POST",
                url: url,
                data: {},
                success: function(data){
                    var panel = $("#totals-panel");
                    panel.html("");
                    $.each( data, function( key, value ) {
                        panel.append("<p style='float: left; padding-right: 10px; margin-right: 10px;'>" + value.label + ": " + value.value + "</p>");
                    });
                }
            });
        }

        function make_server_side_ajax_export( url ){

            $("#export-button-download").hide();

            var button = $("#export-button");
            var text = button.text();

            button.html("");
            button.html("Loading...");

            $.ajax({
                type: "POST",
                url: url,
                data: {},
                success: function(data){

                    button.html("");
                    button.html(text);

                    if( data.error.length > 0 ){
                        alert(data.error);
                    }else{

                        $("#export-button-download").attr("href", data.download);
                        $("#export-button-download").fadeIn();

                        //window.location.href = data.download;
                    }
                }
            });
        }

        function show_debug_query( query ){
            var debug = $("#custom-debug");
            debug.html( query );
        }

    </script>

    @include('backpack::inc.alerts')

    @yield('after_scripts')

    <!-- JavaScripts -->
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
</body>
</html>
