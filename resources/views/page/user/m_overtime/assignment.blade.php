@extends('layouts/admin')
@section('Contents')
    <!-- Contents -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 onclick="">
                Form Assigment
                <small>for working on the day off</small>
            </h1>
            <ol class="breadcrumb">
                <li>
                    <a href="#">
                        <i class="fa fa-calendar"></i>
                        <?php
                        date_default_timezone_set('Asia/Jakarta');
                        echo date('l, d M Y H:i');
                        ?>
                    </a>
                </li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-lg-12">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header with-border">
                            <h3 class="box-title">Assigment Periode</h3>
                            <div class="box-tools pull-right">
                                <input type="month" class="form-control" id="periode" name="periode"
                                    value="{{ $periode }}">
                            </div>
                        </div>
                        <div class="box-body">
                            <table id="tables" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>NIK</th>
                                        <th>Name</th>
                                        <th>Dept</th>
                                        <th>Position</th>
                                        <th style="width:180px;">Job</th>
                                        <th>Date</th>
                                        <th>Plan</th>
                                        <th>Actual</th>
                                        <th>Finger</th>
                                        <th>Hours</th>
                                        <th>Ammount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 0; ?>
                                    @foreach ($tb_assigment as $dt)
                                        <tr>
                                            <td><?php $no++;
                                            echo $no; ?></td>
                                            <td>{{ $dt->NIK }}</td>
                                            <td>{{ $dt->employee_name }}</td>
                                            <td>{{ $dt->dept_code }}</td>
                                            <td>{{ $dt->position_name }}</td>
                                            <td>
                                                <?php echo substr($dt->jobs,0,40);if(strlen($dt->jobs)>40){?>&nbsp;
                                                . . . <a title="Show" href='/Assigment/Show/{{ $dt->id }}'
                                                    target="_blank"> detail </a>
                                                <?php }?>
                                            </td>
                                            <td><?php echo date('d-M-Y', strtotime($dt->start_plan)); ?></td>
                                            <td><?php echo date('H:i', strtotime($dt->start_plan)) . ' ~ ' . date('H:i', strtotime($dt->finish_plan)); ?></td>
                                            <td>
                                                <?php if ($dt->isCompleted == '1') {
                                                    echo date('H:i', strtotime($dt->start_act)) . ' ~ ' . date('H:i', strtotime($dt->finish_act));
                                                } ?>
                                            </td>
                                            <td>
                                                &nbsp;
                                            </td>
                                            <td>{{ $dt->hours_act }}</td>
                                            <td>
                                                {{ $dt->assignment_amount }}
                                                <div class="pull-right">
                                                    <a title="Show" href='/Assigment/Show/{{ $dt->id }}'
                                                        target="_blank"><button type="button"
                                                            class="btn btn-primary btn-xs"><i
                                                                class="fa fa-print"></i></button></a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.Content -->

    <div class="modal fade" id="modal-update">
        <div class="modal-dialog box box-primary" style="width:400px;">
            <div class="modal-content">
                <form action="/Assigment/Verification/Update" method="post">
                    <input type="hidden" id="idform" name="idform">

                    {{ csrf_field() }}
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Overtime Confirmation</h4>
                    </div>
                    <div class="modal-body" style="height:400px;">
                        <div class="form-group">
                            <label>Actual/Realisation</label>
                            <select name="kondisi" id="signafter" class="form-control">
                                <option value="1">Come</option>
                                <option value="2">Not Come</option>
                            </select>
                        </div>
                        <div class="form-group come">
                            <div class="col-xs-6" style="padding:0px;padding-right:3px;">
                                <label>Plan Start</label>
                                <input type="datetime-local" name="start_plan" id="startplan" class="form-control" disabled>
                            </div>
                            <div class="col-xs-6" style="padding:0px;padding-right:3px;">
                                <label>Plan Finish</label>
                                <input type="datetime-local" name="finish_plan" id="finishplan" class="form-control"
                                    disabled>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <div class="form-group come">
                            <div class="col-xs-6" style="padding:0px;padding-right:3px;">
                                <label>Realisation Start</label>
                                <input type="datetime-local" name="start_act" id="startact" class="form-control">
                            </div>
                            <div class="col-xs-6" style="padding:0px;padding-right:3px;">
                                <label>Realisation Finish</label>
                                <input type="datetime-local" name="finish_act" id="finishact" class="form-control">
                            </div>
                        </div>
                        <div>&nbsp;</div>

                        <div class="form-group come">
                            <div class="col-xs-3" style="padding:0px;padding-left:3px;">
                                <label>Hours Actual</label>
                                <input type="number" name="hours_act" id="hoursact" class="form-control" disabled>
                                <div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer" style="text-align:left;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <input type="submit" id="confirm" class="btn btn-primary pull-right" value="Confirm">
                    </div>
            </div>

            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    @if ($message = Session::get('success'))
        <div class="alert alert-info alert-dismissible"
            style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-info"></i> Success Alert</h4>
            {{ $message }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible"
            style="position:absolute;width:350px;right:10px;top:60px;z-index: 1;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-warning"></i> Saving Failed Alert!</h4>
            @if ($errors->has('date_off'))
                - Date harus diisi<br>
            @endif
        </div>
    @endif

@endsection
@section('Scripts')
    <script>
        $('body').on("change", "#periode", function() {
            var periode = document.getElementById('periode').value;
            window.location.href = "/Overtimes/Assigment/" + periode;
        });
    </script>
    <!-- page script Tabel-->
    <script>
        $(document).ready(function() {
            var table = $('#tables').DataTable({
                'paging': true,
                'lengthChange': false,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
                "lengthMenu": [
                    [5, 10, 25, 50, 100, -1],
                    [10, 25, 50, 100, "All"]
                ],
            });

            new $.fn.dataTable.Buttons(table, {
                //buttons: ['copy', 'excel', 'print']
                buttons: [{
                        extend: 'copyHtml5',
                        footer: true
                    },
                    {
                        extend: 'excelHtml5',
                        footer: true
                    },
                    {
                        extend: 'print',
                        footer: true
                    }
                ]

            });

            table.buttons(0, null).container().prependTo(
                table.table().container()
            );
        });
    </script>
    <script type="text/javascript">
        window.onload = function() {
            var hoursact = $("#hoursact").val();
            if (hoursact == '') document.getElementById("confirm").disabled = true;
        }
    </script>
    <script>
        $("#startact").change(function() {
            var Awal = new Date($('#startact').val());
            var Akhir = new Date($('#finishact').val());

            var n = ((Akhir - Awal) / 60000 / 60);
            $('#hoursact').val(n);

            var hoursact = $("#hoursact").val();

            document.getElementById("confirm").disabled = false;
        });
        $("#finishact").change(function() {
            var Awal = new Date($('#startact').val());
            var Akhir = new Date($('#finishact').val());

            var n = ((Akhir - Awal) / 60000 / 60);
            $('#hoursact').val(n);

            var hoursact = $("#hoursact").val();
            // if (hoursact <= 4) {
            // } else {
            document.getElementById("confirm").disabled = false;
            // }
        });
    </script>
    <!-- Durasi Alert -->
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);
    </script>
    <script type="text/javascript">
        $(document).on('click', '.update-modal', function() {
            $('#idform').val($(this).data('idform'));
            $('#startplan').val($(this).data('startplan'));
            $('#finishplan').val($(this).data('finishplan'));
            $('#modal-update').modal('show');
            //alert($(this).data('startplan'));
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('change', '#signafter', function() {
                if ($(this).val() == '2') {
                    $(".come").hide();
                    document.getElementById("confirm").disabled = false;
                } else {
                    $(".come").show();
                    var hoursact = $("#hoursact").val();
                    document.getElementById("confirm").disabled = false;
                }
            });
        });
    </script>
@endsection
