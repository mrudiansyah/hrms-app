<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo e(config('app.name', 'iClock')); ?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/font-awesome/css/font-awesome.min.css')); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/Ionicons/css/ionicons.min.css')); ?>">
    <!-- DataTables -->
    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css')); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/dist/css/AdminLTE.min.css')); ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/dist/css/skins/_all-skins.min.css')); ?>">

    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/bootstrap/dist/css/bootstrap-select.min.css')); ?>">
    <!-- daterange picker -->
    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/bootstrap-daterangepicker/daterangepicker.css')); ?>">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css')); ?>">
    <!-- jvectormap -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/jvectormap/jquery-jvectormap.css')); ?>">

    <!-- fullCalendar -->
    <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/fullcalendar/dist/fullcalendar.min.css')); ?>">
    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/fullcalendar/dist/fullcalendar.print.min.css')); ?>"
        media="print">
    <style>
        .fc-time {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="<?php echo e(asset('/assets/sweetalert/sweetalert.css')); ?>">

    <!-- <link rel="shortcut icon" href="<?php echo e(asset('public/gambar/ems.png')); ?>"> -->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    <link rel="stylesheet"
        href="<?php echo e(asset('/assets/bower_components/datatables.net-bs/css/buttons.dataTables.min.css')); ?>">
    <!-- <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/datatables.net-bs/css/datatables.min.css')); ?>"> -->
    <!-- <link rel="stylesheet" href="<?php echo e(asset('/assets/bower_components/datatables.net-bs/css/jquery.dataTables.min.css')); ?>"> -->

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
    <style>
        #tables th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #tables tbody tr:hover {
            cursor: pointer;
        }

        .table1 tr:hover {
            cursor: pointer;
        }

        #table2 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table2 tbody tr:hover {
            cursor: pointer;
        }

        #table3 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table3 tbody tr:hover {
            cursor: pointer;
        }

        #table4 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }

        #table4 tbody tr:hover {
            cursor: default;
        }

        #table5 th {
            border-top: 2px solid #999;
            border-bottom: 2px solid #999;
        }
    </style>
</head>

<body class="hold-transition skin-blue sidebar-collapse sidebar-mini">
    <div class="wrapper">
        <?php //date_default_timezone_set("Asia/Jakarta");$sekarang=date('Y-m-d');$periode=date('Y-m');?>
        <header class="main-header">
            <!-- Logo -->
            <a href="" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><b>E</b>SS</span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><b>ESS</b></span>
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
                            <a style="padding:8px 8px 8px 0px;" title="Sign Out" class="dropdown-item"
                                href="<?php echo e(route('logout')); ?>"
                                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                <button class="btn btn-danger btn-md">
                                    <i class="fa fa-power-off" style="color:white;"></i>
                                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
                                        <?php echo csrf_field(); ?>
                                    </form>
                                </button>
                            </a>
                        </li>
                        <li>
                            <a href="/Dashboard" style="padding:8px 8px 8px 0px;" title="ESS Portal"
                                class="dropdown-item">
                                <button class="btn btn-success btn-md">
                                    <i class="fa fa-dashboard" style="color:white;"></i> Dashboard
                                </button>
                            </a>
                        </li>
                        <li>
                            <a style="padding:8px 8px 8px 0px;" title="Sign Out" class="dropdown-item"
                                href="/ESS/ChangePassword2">
                                <button class="btn btn-warning btn-md">
                                    <i class="fa fa-key" style="color:ble;"></i> Change Password
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
        <?php echo $__env->make('layouts/menu_ess', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
            <?php echo $__env->yieldContent('Styles'); ?>
            <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
            <div class="content-wrapper">
                <section class="content-header">
                    <h1><?php echo e($juduls); ?></h1>
                    <ol class="breadcrumb">
                        <li>
                            <a href="#">
                                <i class="fa fa-calendar"></i>
                                <?php 
                date_default_timezone_set("Asia/Jakarta");
echo date('l, d M Y H:i');
            ?>
                            </a>
                        </li>
                    </ol>
                </section>
                <?php echo $__env->yieldContent('Contents'); ?>
            </div>
            <div id="preloader">
                <div class="socket">
                    &nbsp;
                </div>
            </div>
            <span class="to_top btn btn-warning fa fa-angle-double-up"></span>
            <?php echo $__env->yieldContent('Content2'); ?>
            <?php if($message = Session::get('info')): ?>
                <div class="alert alert-info alert-dismissible"
                    style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i> Information</h4>
                    <?php echo e($message); ?>

                </div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('Modals'); ?>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <div class="pull-right hidden-xs" style="padding-right:30px;">
                    <?php $MB = number_format(memory_get_peak_usage() / 1000000, '2');
echo "Usage: " . $MB . " MB \n";?>
                </div>
                <strong>Copyright &copy; 2020 <a href="https://adminlte.io">ICT-SAI</a>.</strong>

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
                                    <a href="javascript:void(0)" class="text-red pull-right"><i
                                            class="fa fa-trash-o"></i></a>
                                </label>
                            </div>
                            <!-- /.form-group -->
                        </form>
                    </div>
                    <!-- /.tab-pane -->
                </div>
            </aside>
            <div class="control-sidebar-bg"></div>
            <!-- End Control Sidebar -->

        </div>
        <!-- ./wrapper -->


        <!-- JavaScript -->
        <!-- jQuery 3 -->
        <script src="<?php echo e(asset('/assets/bower_components/jquery/dist/jquery.min.js')); ?>"></script>
        <!-- Bootstrap 3.3.7 -->
        <script src="<?php echo e(asset('/assets/bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
        <!-- DataTables -->
        <script
            src="<?php echo e(asset('/assets/bower_components/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
        <script
            src="<?php echo e(asset('/assets/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')); ?>"></script>
        <!-- SlimScroll -->
        <script
            src="<?php echo e(asset('/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js')); ?>"></script>
        <!-- FastClick -->
        <script src="<?php echo e(asset('/assets/bower_components/fastclick/lib/fastclick.js')); ?>"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo e(asset('/assets/dist/js/adminlte.min.js')); ?>"></script>
        <!-- AdminLTE for demo purposes -->
        <script src="<?php echo e(asset('/assets/dist/js/demo.js')); ?>"></script>

        <script src="<?php echo e(asset('/assets/bower_components/jquery/dist/jquerys.min.js')); ?>"></script>

        <script src="<?php echo e(asset('/assets/bower_components/jquery/dist/bootstrap.bundle.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/bower_components/jquery/dist/bootstrap-select.min.js')); ?>"></script>

        <script>var jQuery_3_2_1 = $.noConflict(true);</script>

        <!-- date-range-picker -->
        <script src="<?php echo e(asset('/assets/bower_components/moment/min/moment.min.js')); ?>"></script>
        <script
            src="<?php echo e(asset('/assets/bower_components/bootstrap-daterangepicker/daterangepicker.js')); ?>"></script>
        <script
            src="<?php echo e(asset('/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')); ?>"></script>
        <script
            src="<?php echo e(asset('/assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/plugins/timepicker/bootstrap-timepicker.min.js')); ?>"></script>

        <!-- ChartJS -->
        <script src="<?php echo e(asset('/assets/bower_components/chart.js/Chart.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/bower_components/chart.js/utils.js')); ?>"></script>


        <script src="<?php echo e(asset('/assets/bower_components/datatables.net/js/buttons.print.min.js')); ?>"></script>
        <script
            src="<?php echo e(asset('/assets/bower_components/datatables.net/js/dataTables.buttons.min.js')); ?>"></script>

        <!-- <script src="<?php echo e(asset('/assets/js/jquery.dataTables.min.js')); ?>"></script> -->
        <script src="<?php echo e(asset('/assets/js/jszip.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/js/pdfmake.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/js/vfs_fonts.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/js/buttons.html5.min.js')); ?>"></script>

        <!-- Dashboard-->

        <!-- jQuery UI 1.11.4 -->
        <script src="<?php echo e(asset('/assets/bower_components/jquery-ui/jquery-ui.min.js')); ?>"></script>
        <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
        <script>
            $.widget.bridge('uibutton', $.ui.button);
        </script>
        <!-- Sparkline -->
        <script
            src="<?php echo e(asset('/assets/bower_components/jquery-sparkline/dist/jquery.sparkline.min.js')); ?>"></script>
        <!-- jvectormap -->
        <script src="<?php echo e(asset('/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>
        <!-- jQuery Knob Chart -->
        <script src="<?php echo e(asset('/assets/bower_components/jquery-knob/dist/jquery.knob.min.js')); ?>"></script>
        <!-- datepicker -->
        <script
            src="<?php echo e(asset('/assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js')); ?>"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script
            src="<?php echo e(asset('/assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')); ?>"></script>
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <!-- <script src="<?php echo e(asset('/assets/dist/js/pages/dashboard.js')); ?>"></script> -->

        <!-- fullCalendar -->
        <script src="<?php echo e(asset('/assets/sweetalert/sweetalert.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/bower_components/moment/moment.js')); ?>"></script>
        <script src="<?php echo e(asset('/assets/bower_components/fullcalendar/dist/fullcalendar.min.js')); ?>"></script>

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
        <script>
            $(function () {
                $('#table2').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    "pageLength": 10,
                    'autoWidth': true
                })
            })
            $(function () {
                $('#table3').DataTable({
                    'paging': true,
                    'lengthChange': false,
                    'searching': true,
                    'ordering': false,
                    'info': false,
                    "pageLength": 50,
                    'autoWidth': false
                })
            })
            $(function () {
                $('#table4').DataTable({
                    'paging': false,
                    'lengthChange': false,
                    'searching': false,
                    'ordering': false,
                    'info': false,
                    "pageLength": 10,
                    'autoWidth': false
                })
            })
        </script>
        <script>
            $(document).ready(function () {
                var table = $('#tables').DataTable({
                    'paging': true,
                    'lengthChange': false,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    "pageLength": 50,
                    'autoWidth': false,
                    "lengthMenu": [[5, 10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    //"scrollX"     : true
                });

                new $.fn.dataTable.Buttons(table, {
                    //buttons: ['copy', 'excel', 'print']
                    buttons: [
                        { extend: 'copyHtml5', footer: true },
                        { extend: 'excelHtml5', footer: true },
                        { extend: 'print', footer: true }
                    ]
                });

                table.buttons(0, null).container().prependTo(
                    table.table().container()
                );
            });


        </script>
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
        <!-- End JavaScript -->
        <?php echo $__env->yieldContent('Scripts'); ?>

        <textarea id="printing-css" style="display:none;">.no-print{display:none}</textarea>
        <iframe id="printing-frame" name="print_frame" src="about:blank" style="display:none;"></iframe>

</body>

</html><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/layouts/home.blade.php ENDPATH**/ ?>