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
                        <div class="box-header">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">Approval</h3>
                            <div class="pull-right">
                                <a class="btn btn-app table2">
                                    <?php if (isset($qty_new) && $qty_new > 0) {
                                        echo "<span class='badge bg-red'>" . $qty_new . '</span>';
                                    } ?>
                                    <i class="fa fa-file-o"></i> New Form
                                </a>
                                <a class="btn btn-app table3">
                                    <i class="fa fa-check-square-o"></i> Complete
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <div id="tabel2">
                                <table id="table2" class="table table-hover" style="min-width:100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIK</th>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Plan</th>
                                            <th>Actual</th>
                                            <th>Hours</th>
                                            <th>Approval Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <?php $no = 0; ?>
                                    @foreach ($tb_assigment_new as $dt)
                                        <tbody>
                                            <tr>
                                                <td><?php $no++;
                                                echo $no; ?></td>
                                                <td>{{ $dt->NIK }}</td>
                                                <td>{{ $dt->employee_name }}</td>
                                                <td><?php echo date('d-M-Y', strtotime($dt->start_plan)); ?></td>
                                                <td><?php echo date('H:i', strtotime($dt->start_plan)) . ' ~ ' . date('H:i', strtotime($dt->finish_plan)); ?></td>
                                                <td><?php if ($dt->isCompleted == '1') {
                                                    echo date('H:i', strtotime($dt->start_act)) . ' ~ ' . date('H:i', strtotime($dt->finish_act));
                                                } ?></td>
                                                <td></td>
                                                <td>
                                                    <?php
                                                    if ($dt->assigned_status == '0') {
                                                        $status = 'assigned';
                                                        echo "<span class='badge bg-yellow'>1</span>";
                                                        echo "<span class='badge bg-yellow'>2</span>";
                                                        if ($dt->approved1 != '') {
                                                            echo "<span class='badge bg-yellow'>3</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>3</span>";
                                                        }
                                                        if ($dt->approved2 != '') {
                                                            echo "<span class='badge bg-yellow'>4</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>4</span>";
                                                        }
                                                        echo "<span class='badge bg-yellow'>5</span>";
                                                    } elseif ($dt->conducted_status == '0') {
                                                        $status = 'conducted';
                                                        echo "<span class='badge bg-green'>1</span>";
                                                        echo "<span class='badge bg-yellow'>2</span>";
                                                        if ($dt->approved1 != '') {
                                                            echo "<span class='badge bg-yellow'>3</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>3</span>";
                                                        }
                                                        if ($dt->approved2 != '') {
                                                            echo "<span class='badge bg-yellow'>4</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>4</span>";
                                                        }
                                                        echo "<span class='badge bg-yellow'>5</span>";
                                                    } elseif ($dt->approved1_status == '0' && $dt->approved1 != '') {
                                                        $status = 'approved1';
                                                        echo "<span class='badge bg-green'>1</span>";
                                                        echo "<span class='badge bg-green'>2</span>";
                                                        if ($dt->approved1 != '') {
                                                            echo "<span class='badge bg-yellow'>3</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>3</span>";
                                                        }
                                                        if ($dt->approved2 != '') {
                                                            echo "<span class='badge bg-yellow'>4</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>4</span>";
                                                        }
                                                        echo "<span class='badge bg-yellow'>5</span>";
                                                    } elseif ($dt->approved2_status == '0' && $dt->approved2 != '') {
                                                        $status = 'approved2';
                                                        echo "<span class='badge bg-green'>1</span>";
                                                        echo "<span class='badge bg-green'>2</span>";
                                                        echo "<span class='badge bg-green'>3</span>";
                                                        if ($dt->approved2 != '') {
                                                            echo "<span class='badge bg-yellow'>4</span>";
                                                        } else {
                                                            echo "<span class='badge bg-white'>4</span>";
                                                        }
                                                        echo "<span class='badge bg-yellow'>5</span>";
                                                    } elseif ($dt->legalized_status == '0') {
                                                        $status = 'legalized';
                                                        echo "<span class='badge bg-green'>1</span>";
                                                        echo "<span class='badge bg-green'>2</span>";
                                                        echo "<span class='badge bg-green'>3</span>";
                                                        echo "<span class='badge bg-green'>4</span>";
                                                        echo "<span class='badge bg-yellow'>5</span>";
                                                    } else {
                                                        $status = 'Completed';
                                                        echo "<span class='badge bg-green'>1</span>";
                                                        echo "<span class='badge bg-green'>2</span>";
                                                        echo "<span class='badge bg-green'>3</span>";
                                                        echo "<span class='badge bg-green'>4</span>";
                                                        echo "<span class='badge bg-green'>5</span>";
                                                    }
                                                    
                                                    ?>
                                                </td>
                                                <td>
                                                    <a title="Show" href='/Assigment/Show/{{ $dt->id }}'
                                                        target="_blank"><button type="button"
                                                            class="btn btn-primary btn-xs"><i
                                                                class="fa fa-print"></i></button></a>
                                                    <?php if($dt->hours_act<=4&&$dt->legalized_status=='1'){
                                                    $tgl_awal=date('Y-m-d',strtotime($dt->start_plan));
                                                    $jam_awal=date('H:i',strtotime($dt->start_plan));
                                                    $start_plan=$tgl_awal.'T'.$jam_awal;
                                                    $tgl_akhir=date('Y-m-d',strtotime($dt->finish_plan));
                                                    $jam_akhir=date('H:i',strtotime($dt->finish_plan));
                                                    $finish_plan=$tgl_akhir.'T'.$jam_akhir;
                                                    ?>
                                                    <button type="button" class="btn btn-warning btn-xs update-modal"
                                                        data-idform='{{ $dt->id }}'
                                                        data-startplan='{{ $start_plan }}'
                                                        data-finishplan='{{ $finish_plan }}'><i
                                                            class="fa fa-edit"></i></button>
                                                    <a title="Sign"
                                                        href='/Assigment/Realisations/Confirm/{{ $dt->id }}'><button
                                                            type="button" class="btn btn-success btn-xs"><i
                                                                class="fa fa-check"></i></button></a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                            <div id="tabel3">
                                <div class="row" style="padding-bottom:20px;">
                                    <div class="col-lg-2 col-md-3 col-xs-12">
                                        <label>Periode</label>
                                        <input type="month" class="form-control" id="periode" name="periode"
                                            value="{{ $periode }}">
                                    </div>
                                </div>
                                <table id="table3" class="table table-hover" style="min-width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">No</th>
                                            <th style="width:90px;">NIK</th>
                                            <th style="width:150px;">Name</th>
                                            <th style="width:120px;">Plan OT</th>
                                            <th style="width:120px;">Actual OT</th>
                                            <th>Reason/Target</th>
                                            <th style="width:120px;">Status</th>
                                        </tr>
                                    </thead>
                                    <?php $no = 0; ?>
                                    @foreach ($tb_assigment as $dt)
                                        <tbody>
                                            <tr>
                                                <td><?php $no++;
                                                echo $no; ?></td>
                                                <td>{{ $dt->NIK }}</td>
                                                <td>{{ $dt->employee_name }}</td>
                                                <td><?php echo date('d-M-Y H:i', strtotime($dt->start_plan)); ?></td>
                                                <td title="<?php echo date('d M Y H:i', strtotime($dt->start_act)).'-'.date('d M Y H:i', strtotime($dt->finish_act)); ?>"><?php echo date('H:i', strtotime($dt->start_act)).' - '.date('H:i', strtotime($dt->finish_act)); ?></td>
                                                <td>{{ $dt->jobs }}</td>
                                                <td>
                                                    <?php
                                                    if ($dt->assigned_status == '0') {
                                                        $status = 'assigned';
                                                    } elseif ($dt->conducted_status == '0') {
                                                        $status = 'conducted';
                                                    } elseif ($dt->approved1_status == '0' && $dt->approved1 != '') {
                                                        $status = 'approved1';
                                                    } elseif ($dt->approved2_status == '0' && $dt->approved2 != '') {
                                                        $status = 'approved2';
                                                    } elseif ($dt->legalized_status == '0') {
                                                        $status = 'legalized';
                                                    } else {
                                                        $status = 'Completed';
                                                    }
                                                    if ($dt->isDelete == '1') {
                                                        echo 'Cancel';
                                                    } elseif ($status != 'Completed') {
                                                        echo 'Need ' . $status;
                                                    } else {
                                                        echo $status;
                                                    }
                                                    ?>
                                                    <div class="pull-right">
                                                        <a title="Show" href='/Assigment/Show/{{ $dt->id }}'
                                                        target="_blank"><button type="button"
                                                            class="btn btn-primary btn-xs"><i
                                                                class="fa fa-print"></i></button></a>                                                        
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer">

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
                <form action="/Assigment/Realisation/Update" method="post">
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
                                <input type="hidden" name="hours_act" id="hoursact" class="form-control">
                                <input type="number" name="hours_acts" id="hoursacts" class="form-control" disabled>
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
            window.location.href = "/Assigment/Realisations/" + periode;
        });
    </script>
    <!-- page script Tabel-->
    <script>
        $(document).ready(function() {
            $("#tabel2").show();
            $("#tabel3").hide();
            $("#judul").text('New Assigment Form');
            $(document).on('click', '.table2', function() {
                $("#tabel2").show();
                $("#tabel3").hide();
                $("#judul").text('New Assigment Form');
            });
            $(document).on('click', '.table3', function() {
                $("#tabel3").show();
                $("#tabel2").hide();
                $("#judul").text('Completed Assigment Form');
            });
        });
    </script>
    <script>
        $(function() {
            $('#table2').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 15,
                'autoWidth': true,
                "scrollX": true
            })
        })
        $(function() {
            $('#table3').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
            })
        })
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
            $('#hoursact2').val(n);

            var hoursact = $("#hoursact").val();
            // if (hoursact <= 4) {
            // } else {
            document.getElementById("confirm").disabled = false;
            // }
        });
        $("#finishact").change(function() {
            var Awal = new Date($('#startact').val());
            var Akhir = new Date($('#finishact').val());

            var n = ((Akhir - Awal) / 60000 / 60);
            $('#hoursact').val(n);
            $('#hoursacts').val(n);

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
                    // if (hoursact <= 4) {
                    // } else {
                    document.getElementById("confirm").disabled = false;
                    // }
                }
            });
        });
    </script>
@endsection
