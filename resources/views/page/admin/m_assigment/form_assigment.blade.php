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
                <div class="col-xs-12 col-sm-12 col-lg-4">
                    <form action="/Assigment/Adds" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="sysid" id="syside">
                        <div class="box box-primary box-solid">
                            <div class="box-header with-border">
                                <label>Assigment Form</label>
                                <div class="box-tools pull-right">
                                    &nbsp;
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Employee</label>
                                    <select id="idemployee" name="id_employee" class="form-control selectpicker"
                                        data-live-search="true">
                                        <option value=""></option>
                                        @foreach ($tb_employee as $dt)
                                            <option value="{{ $dt->id }}">{{ $dt->employee_name }}</option>
                                        @endforeach

                                    </select>
                                    <div class="pull-right" style="color:#F00;padding-right:10px;" id="info">&nbsp;
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Plan Start</label>
                                    <input type="datetime-local" name="start_plan" class="form-control" id="startplan" <?php if($lock_backdate==1)echo "min='".$Jam."'";?>>
                                </div>
                                <div class="form-group">
                                    <label>Plan Finish</label>
                                    <input type="datetime-local" name="finish_plan" class="form-control" id="finishplan" <?php if($lock_backdate==1)echo "min='".$Jam."'";?>>
                                    <input type="hidden" id="hoursplan" name="hours_plan">
                                </div>

                                <div class="form-group" style="padding-top:15px;">
                                    <label>Reference Memo</label>
                                    <select name="reference" id="reference" class="form-control">
                                        <option></option>
                                        @foreach ($tb_ref as $dt3)
                                            <option value="{{ $dt3->memo_number }}">{{ $dt3->memo_number }} {{$dt3->to_dept}} {{$dt3->additional_note}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="padding-top:15px;">
                                    <label>Category</label>
                                    <select name="reasoncategory" id="reasoncategory" class="form-control">
                                        <option></option>
                                        @foreach ($category_list as $dt2)
                                            <option value="{{ $dt2->category }}">{{ $dt2->category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="padding-top:15px;">
                                    <label>Reason OT</label>
                                    <select name="reason_ot" id="reasonot" class="form-control">
                                    <!-- <select name="reason_ot" id="reasonot" class="form-control selectpicker" data-live-search="true"> -->
                                        <option></option>
                                        <!-- @foreach ($tb_reason_ot as $dt2)
                                            <option value="{{ $dt2->reason_ot }}">{{ $dt2->reason_ot }}</option>
                                        @endforeach -->
                                    </select>
                                </div>
 
                                <div class="form-group umum">
                                    <label>Detail Reason/Target</label>
                                    <textarea name="jobs" id="jobs" class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Assigned By</label>
                                    <select class="form-control" name="assigned" id="assigned">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Approved #1</label>
                                    <select class="form-control" name="approved1" id="approved1">
                                        <option value=""></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Approved #2</label>
                                    <select class="form-control" name="approved2" id="approved2">
                                        <option value=""></option>
                                    </select>
                                </div>

                            </div>
                            <div class="box-footer">
                                <div class="form-group pull-right">

                                    <input type="submit" class="btn btn-primary addmp" id="addmp" value="Submit">
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                    </form>
                </div>
                <div class="col-xs-12 col-sm-12 col-lg-8">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-header">
                            <i class="fa fa-calendar"></i>
                            <h3 class="box-title">Data Tables</h3>
                        </div>
                        <div class="box-body">
                            <table id="table2" class="table table-hover" style="min-width:100%;">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">No</th>
                                        <th style="width:90px;">NIK</th>
                                        <th style="width:150px;">Name</th>
                                        <th style="width:90px;">Plan OT</th>
                                        <th>Reason/Target</th>
                                        <th style="width:80px;">Action</th>
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
                                            <td><?php echo date('d-M-Y', strtotime($dt->start_plan)); ?></td>
                                            <td>{{ $dt->jobs }}</td>
                                            <td>
                                                <a title="Show" href='/Assigment/Show/{{ $dt->id }}'
                                                    target="_blank"><button type="button" class="btn btn-primary btn-xs"><i
                                                            class="fa fa-print"></i></button></a>
                                                <button title="Delete" type="button"
                                                    class="delete-modal btn btn-danger btn-xs"
                                                    data-delid="{{ $dt->id }}"
                                                    data-delname="{{ $dt->employee_name }}"><i
                                                        class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                @endforeach
                            </table>

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

    <div class="modal fade" id="modal-delete">
        <div class="modal-dialog box box-danger" style="width:400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <div class="modal-body">
                    Click Yes to Delete : <b id="delname"></b> ?
                    <input type="hidden" id="delid">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes,
                        Delete</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
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
            @else
                {{$errors}}
            @endif
        </div>
    @endif

@endsection
@section('Scripts')
    <!-- page script Tabel-->
    <script>
        $(function() {
            $('#table2').DataTable({
                'paging': false,
                'lengthChange': false,
                'searching': false,
                'ordering': false,
                'info': false,
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
            var hoursplan = $("#hoursplan").val();
            var idemployee = $("#idemployee").val();
            var jobs = $("#jobs").val();
            var assigned = $("#assigned").val();
            if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
                document.getElementById("addmp").disabled = true;
            } else {
                document.getElementById("addmp").disabled = false;
            }
        }
        $(function() {
            $("#idemployee").change(function() {
                $.ajaxSetup({
                    type: "POST",
                    url: "/Assigment/Select",
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var idemployee = $("#idemployee").val();
                $.ajax({
                    data: {
                        id_employee: idemployee
                    },
                    success: function(respond) {
                        $("#assigned").html(respond);

                        $('#approved1')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""></option>')
                            .val();
                        $('#approved2')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""></option>')
                            .val();
                        //alert(valdep);
                    }
                })

                var hoursplan = $("#hoursplan").val();
                var idemployee = $("#idemployee").val();
                var jobs = $("#jobs").val();
                var assigned = $("#assigned").val();
                if (hoursplan <= 4 || idemployee == '' || jobs == '' || assigned == '') {
                    document.getElementById("addmp").disabled = true;
                } else {
                    document.getElementById("addmp").disabled = false;
                }

            });
            $("#assigned").change(function() {
                $.ajaxSetup({
                    type: "POST",
                    url: "/Assigment/Select",
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var idemployee = $("#assigned").val();
                $.ajax({
                    data: {
                        id_employee: idemployee
                    },
                    success: function(respond) {
                        //alert(respond);
                        $("#approved1").html(respond);
                        $('#approved2')
                            .find('option')
                            .remove()
                            .end()
                            .append('<option value=""></option>')
                            .val();
                    }
                })

                updateButton();

            });
            $("#approved1").change(function() {
                $.ajaxSetup({
                    type: "POST",
                    url: "/Assigment/Select",
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var idemployee = $("#approved1").val();
                $.ajax({
                    data: {
                        id_employee: idemployee
                    },
                    success: function(respond) {
                        $("#approved2").html(respond);
                    }
                })

                updateButton();
            });
        })
    </script>
    <script>
        $("#startplan").change(function() {
            var Akhir = new Date($('#finishplan').val());
            if(Akhir!="Invalid Date"){
                updateButton();
            }
        });
        $("#finishplan").change(function() {
            updateButton();
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
        // Delete Data
        $(document).on('click', '.delete-modal', function() {
            $('#delid').val($(this).data('delid'));
            $('#delname').text($(this).data('delname'));
            $('#modal-delete').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            var x = $('#delid').val();
            window.location.href = '/Assigment/Delete/' + x;
        });
    </script>
    <script>
        function appendLeadingZeroes(n) {
            if (n <= 9) {
                return "0" + n;
            }
            return n
        }

        function updateButton() {
            var Awal = new Date($('#startplan').val());
            var Akhir = new Date($('#finishplan').val());
            var Hari = Akhir.getDay();
            var Bulan_0 = Akhir.getMonth() + 1;
            var Bulan = appendLeadingZeroes(Bulan_0);
            var Tanggal_0 = Akhir.getDate();
            var Tanggal = appendLeadingZeroes(Tanggal_0);
            var Tgl = Akhir.getFullYear() + '-' + Bulan + '-' + Tanggal;

            var idemployee = $("#idemployee").val();
            var jobs = $("#jobs").val();
            var assigned = $("#assigned").val();
            var n = ((Akhir - Awal) / 60000 / 60);
            console.log(n);
            $('#hoursplan').val(n);
            var hoursplan=$("#hoursplan").val();
            //alert(hoursplan);
            if (hoursplan<1||hoursplan=='NaN') {
                $("#addmp").prop("disabled", true);
                alert('Jam belum sesuai, pilih dengan benar');
            } else {
                $("#addmp").prop("disabled", false);
            }
        }
        window.onload = function() {

            // ===== HARDCODE untuk dropdown reasoncategory =====
            var reference = $("#reference").val();
            var select = $("#reasoncategory");
            select.empty(); // Kosongkan dropdown
            
            // Tambahkan option default
            select.append('<option value=""></option>');
            
            // Hardcode data kategori
            if(reference==''){
                var categories = [
                    { id: 'Unplanned', name: 'Unplanned' },
                    { id: 'Vendor Stay', name: 'Vendor Stay' }
                ];
            }else{
                var categories = [
                    { id: 'Planned', name: 'Planned' }
                ];
            }
            
            // Loop dan tambahkan ke dropdown
            $.each(categories, function(index, category) {
                select.append(
                    $('<option></option>')
                        .val(category.id)
                        .text(category.name)
                );
            });
            // ===== END HARDCODE =====


			var category=$("#reasoncategory").val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/SelectReason",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
            $.ajax({
                data:{category:category},
                success: function(respond){
                    $("#reasonot").html(respond);
                }
            })


        }
        $("#reference").change(function() {
            // ===== HARDCODE untuk dropdown reasoncategory =====
            var reference = $("#reference").val();
            var select = $("#reasoncategory");
            select.empty(); // Kosongkan dropdown
            
            // Tambahkan option default
            select.append('<option value=""></option>');
            
            // Hardcode data kategori
            if(reference==''){
                var categories = [
                    { id: 'Unplanned', name: 'Unplanned' },
                    { id: 'Vendor Stay', name: 'Vendor Stay' }
                ];
            }else{
                var categories = [
                    { id: 'Planned', name: 'Planned' }
                ];
            }
            
            // Loop dan tambahkan ke dropdown
            $.each(categories, function(index, category) {
                select.append(
                    $('<option></option>')
                        .val(category.id)
                        .text(category.name)
                );
            });
            // ===== END HARDCODE =====

        });
        $("#reasoncategory").change(function() {
			var category=$("#reasoncategory").val();
			$.ajaxSetup({
				type:"POST",
				url: "/Admin/Overtime/SelectReason",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
            $.ajax({
                data:{category:category},
                success: function(respond){
                    $("#reasonot").html(respond);
                }
            })
        });
    </script>
@endsection
