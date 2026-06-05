<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ config('app.name', 'iClock') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/font-awesome/css/font-awesome.min.css') }}">
  <!-- Ionicons -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/Ionicons/css/ionicons.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet"
    href="{{ asset('/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/assets/dist/css/AdminLTE.min.css') }}">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="{{ asset('/assets/dist/css/skins/_all-skins.min.css') }}">

  <link rel="stylesheet" href="{{ asset('/assets/bower_components/bootstrap/dist/css/bootstrap-select.min.css') }}">
  <!-- daterange picker -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css') }}">
  <!-- bootstrap datepicker -->
  <link rel="stylesheet"
    href="{{ asset('/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') }}">
  <!-- jvectormap -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/jvectormap/jquery-jvectormap.css') }}">

  <!-- fullCalendar -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/fullcalendar/dist/fullcalendar.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css') }}"
    media="print">
  <style>
    .fc-time {
      display: none;
    }
  </style>

  <link rel="shortcut icon" href="{{ asset('public/gambar/apple-touch-icon.png') }}" type="image/x-icon">
  <link rel="icon" href="{{ asset('public/gambar/favicon.ico') }}" type="image/x-icon">

  <!-- Google Font -->
  <!--
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  -->

  <link rel="stylesheet"
    href="{{ asset('/assets/bower_components/datatables.net-bs/css/buttons.dataTables.min.css') }}">
  <!-- <link rel="stylesheet" href="{{ asset('/assets/bower_components/datatables.net-bs/css/datatables.min.css') }}"> -->
  <!-- <link rel="stylesheet" href="{{ asset('/assets/bower_components/datatables.net-bs/css/jquery.dataTables.min.css') }}"> -->
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/summernote/summernote.css') }}">
  <link rel="stylesheet" href="{{ asset('/assets/sweetalert/sweetalert.css') }}">
  <link rel="stylesheet" href="{{ asset('/assets/bower_components/select2/dist/css/select2.min.css') }}">

  <style>
    .abuabu {
      width: 400px;
      filter: gray;
      -webkit-filter: grayscale(1);
      /*-webkit-transition: all .8s ease-in-out;*/
    }
  </style>
  <style type="text/css">
    .to_top {
      display: inline-block;
      padding: 6px 10px;
      position: fixed;
      right: 10px;
      cursor: pointer;
      bottom: 10px;
    }

    #preloader {
      position: fixed;
      left: 0px;
      top: 0px;
      z-index: 0;
      width: 100%;
      height: 100%;
      overflow: visible;
      background: #ffffff none repeat scroll 0% 0%;
    }

    .socket {
      position: relative;
      top: 40%;
      width: auto;
      height: auto;
      margin: auto;
      display: block;
    }

    .socket img {
      display: block;
      margin: auto;
    }

    .container {
      text-align: center;
      padding-top: 100px;
    }
  </style>
</head>

<body class="hold-transition skin-blue fixed sidebar-mini">
  <div class="wrapper">
    <?php date_default_timezone_set("Asia/Jakarta");
$sekarang = date('Y-m-d');
$periode = date('Y-m');?>
    <header class="main-header">
      <!-- Logo -->
      <a href="" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>HR</b>MS</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><b>HRMS</b></span>
      </a>
      <!-- Header Navbar: style can be found in header.less -->
      <nav class="navbar navbar-static-top">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>

        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
            <li>
              <a style="padding:8px 8px 8px 0px;" title="Sign Out" class="dropdown-item" href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                <button class="btn btn-danger btn-md">
                  <i class="fa fa-power-off" style="color:white;"></i>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                  </form>
                </button>
              </a>
            </li>
            <li>
              <a style="padding:8px 8px 8px 0px;" title="ESS Portal" class="dropdown-item" href="/Profile">
                <button class="btn btn-success btn-md">
                  <i class="fa fa-user" style="color:white;"></i> ESS
                </button>
              </a>
            </li>
            <li>
              <a style="padding:8px 8px 8px 0px;" title="Sign Out" class="dropdown-item" href="/ChangePassword">
                <button class="btn btn-warning btn-md">
                  <i class="fa fa-key" style="color:white;"></i> Change Password
                </button>
              </a>
            </li>
            <li style="padding-top:8px;padding-right:8px;" title="Change Thema">
              <button data-toggle="control-sidebar" class="btn btn-default btn-md">
                <i class="fa fa-gears"></i>
              </button>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    @include('layouts/menu')
    <!-- Content Wrapper. Contains page content -->
    <div id="cetak">
      <style>
        #table1 {
          font-family: sans-serif;
        }

        #table1 th {
          border-top: 1px solid #999;
          border-bottom: 1px solid #999;
          background-color: #2F4F4F;
          color: white;
        }

        #table1,
        td {
          border-top: 1px solid #999;
          border-bottom: 1px solid #999;
        }

        @media print {
          .noprint {
            visibility: hidden;
          }
        }
      </style>

      @yield('Contents')
      <div id="preloader">
        <div class="socket">
          &nbsp;
        </div>
      </div>
      <span class="to_top btn btn-warning fa fa-angle-double-up"></span>
      @yield('Content2')
      @yield('Modals')
      <!-- /.content-wrapper -->

      <footer class="main-footer">
        <div class="pull-right hidden-xs" style="padding-right:30px;">
          <?php $MB = number_format(memory_get_peak_usage() / 1000000, '2');
echo "Usage: " . $MB . " MB \n";?>
        </div>
        <strong>Copyright &copy; 2020 <a href="https://adminlte.io">ICT-SAI</a></strong>

      </footer>

      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
          <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
          <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <!-- Home tab content -->
          <div class="tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class="control-sidebar-menu">
              <li>
                <a href="javascript:void(0)">
                  <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                    <p>Will be 23 on April 24th</p>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <i class="menu-icon fa fa-user bg-yellow"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                    <p>New phone +1(800)555-1234</p>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                    <p>nora@example.com</p>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <i class="menu-icon fa fa-file-code-o bg-green"></i>

                  <div class="menu-info">
                    <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                    <p>Execution time 5 seconds</p>
                  </div>
                </a>
              </li>
            </ul>
            <!-- /.control-sidebar-menu -->

            <h3 class="control-sidebar-heading">Tasks Progress</h3>
            <ul class="control-sidebar-menu">
              <li>
                <a href="javascript:void(0)">
                  <h4 class="control-sidebar-subheading">
                    Custom Template Design
                    <span class="label label-danger pull-right">70%</span>
                  </h4>

                  <div class="progress progress-xxs">
                    <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <h4 class="control-sidebar-subheading">
                    Update Resume
                    <span class="label label-success pull-right">95%</span>
                  </h4>

                  <div class="progress progress-xxs">
                    <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <h4 class="control-sidebar-subheading">
                    Laravel Integration
                    <span class="label label-warning pull-right">50%</span>
                  </h4>

                  <div class="progress progress-xxs">
                    <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                  </div>
                </a>
              </li>
              <li>
                <a href="javascript:void(0)">
                  <h4 class="control-sidebar-subheading">
                    Back End Framework
                    <span class="label label-primary pull-right">68%</span>
                  </h4>

                  <div class="progress progress-xxs">
                    <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                  </div>
                </a>
              </li>
            </ul>
            <!-- /.control-sidebar-menu -->

          </div>
          <!-- /.tab-pane -->
          <!-- Stats tab content -->
          <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
          <!-- /.tab-pane -->
          <!-- Settings tab content -->
          <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
              <h3 class="control-sidebar-heading">General Settings</h3>

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Report panel usage
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Some information about this general settings option
                </p>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Allow mail redirect
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Other sets of options are available
                </p>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Expose author name in posts
                  <input type="checkbox" class="pull-right" checked>
                </label>

                <p>
                  Allow the user to show his name in blog posts
                </p>
              </div>
              <!-- /.form-group -->

              <h3 class="control-sidebar-heading">Chat Settings</h3>

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Show me as online
                  <input type="checkbox" class="pull-right" checked>
                </label>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Turn off notifications
                  <input type="checkbox" class="pull-right">
                </label>
              </div>
              <!-- /.form-group -->

              <div class="form-group">
                <label class="control-sidebar-subheading">
                  Delete chat history
                  <a href="javascript:void(0)" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                </label>
              </div>
              <!-- /.form-group -->
            </form>
          </div>
          <!-- /.tab-pane -->
        </div>
      </aside>
      <!-- /.control-sidebar -->
      <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
      <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->


    <!-- jQuery 3 -->
    <script src="{{ asset('/assets/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('/assets/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('/assets/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('/assets/bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/assets/dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('/assets/dist/js/demo.js') }}"></script>

    <script src="{{ asset('/assets/bower_components/jquery/dist/jquerys.min.js') }}"></script>

    <script src="{{ asset('/assets/bower_components/jquery/dist/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/jquery/dist/bootstrap-select.min.js') }}"></script>

    <script>var jQuery_3_2_1 = $.noConflict(true);</script>

    <!-- date-range-picker -->
    <script src="{{ asset('/assets/bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script
      src="{{ asset('/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <script
      src="{{ asset('/assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <!-- ChartJS -->
    <script src="{{ asset('/assets/bower_components/chart.js/Chart.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/chart.js/utils.js') }}"></script>


    <script src="{{ asset('/assets/bower_components/datatables.net/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/datatables.net/js/dataTables.buttons.min.js') }}"></script>

    <!-- <script src="{{ asset('/assets/js/jquery.dataTables.min.js') }}"></script> -->
    <script src="{{ asset('/assets/js/jszip.min.js') }}"></script>
    <script src="{{ asset('/assets/js/pdfmake.min.js') }}"></script>
    <script src="{{ asset('/assets/js/vfs_fonts.js') }}"></script>
    <script src="{{ asset('/assets/js/buttons.html5.min.js') }}"></script>

    <!-- Dashboard-->

    <!-- jQuery UI 1.11.4 -->
    <script src="{{ asset('/assets/bower_components/jquery-ui/jquery-ui.min.js') }}"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button);
    </script>
    <!-- Sparkline -->
    <script src="{{ asset('/assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js') }}"></script>
    <!-- jvectormap -->
    <script src="{{ asset('/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <!-- jQuery Knob Chart -->
    <script src="{{ asset('/assets/bower_components/jquery-knob/dist/jquery.knob.min.js') }}"></script>
    <!-- datepicker -->
    <script
      src="{{ asset('/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') }}"></script>
    <!-- Bootstrap WYSIHTML5 -->
    <script src="{{ asset('/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="{{ asset('/assets/dist/js/pages/dashboard.js') }}"></script> -->

    <!-- fullCalendar -->
    <script src="{{ asset('/assets/bower_components/moment/moment.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/fullcalendar/dist/fullcalendar.min.js') }}"></script>

    <script src="{{ asset('/assets/bower_components/summernote/summernote.min.js') }}"></script>
    <script src="{{ asset('/assets/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('/assets/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <!-- End Dashboard-->

    <script type="text/javascript">
      //script preloader
      (function ($) {
        $(window).on('load', function () {
          $('#preloader').fadeOut('slow', function () {
            $(this).hide();
          });
        });
      })(jQuery);
      //slow bisa diganti dengan angka misal 2000 
    </script>
    <script type="text/javascript">
      $(function () {
        $('.to_top').hide().on('click', function () {
          $('body,html').animate({ scrollTop: 0 }, 800);
        });
        $(window).on('scroll', function () {
          if ($(this).scrollTop() > 50) {
            $('.to_top').show();
          } else {
            $('.to_top').hide();
          }
        });
      });
    </script>
    @yield('Scripts')

    <script type="text/javascript">
      function printDiv(elementId) {

        var a = document.getElementById('printing-css').value;
        var b = document.getElementById(elementId).innerHTML;
        window.frames["print_frame"].document.title = document.title;
        window.frames["print_frame"].document.body.innerHTML = '<style>' + a + '</style>' + b;
        window.frames["print_frame"].window.focus();
        window.frames["print_frame"].window.print();
      }
    </script>



    <textarea id="printing-css" style="display:none;">.no-print{display:none}</textarea>
    <iframe id="printing-frame" name="print_frame" src="about:blank" style="display:none;"></iframe>

</body>

</html>