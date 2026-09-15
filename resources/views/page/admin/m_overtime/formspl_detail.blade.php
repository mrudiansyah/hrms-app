@extends('layouts/admin')
@section('Contents')
    <!-- Contents -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        #tables th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #2F4F4F;
            color: white;
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

        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 34px;
            user-select: none;
            -webkit-user-select: none;
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1 onclick="">
                Form SPL
                <small>Surat Perintah Lembur</small>
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
                    @foreach ($tb_overtime as $dt)
                        <?php
                        $idSPL = $dt->id;
                        $Tgl = date_create($dt->ot_date);
                        $MinBottom = date_format($Tgl, 'Y-m-d H:i');
                        //$MinStart=date('Y-m-d',strtotime('-1',strtotime($Tgl)));
                        $date = date_add($Tgl, date_interval_create_from_date_string('-1 days'));
                        $MinStart = date_format($date, 'Y-m-d');
                        $date = date_add($Tgl, date_interval_create_from_date_string('+2 days'));
                        $MaxFinish = date_format($date, 'Y-m-d');

                        //echo $MinStart.'~'.$MaxFinish;
                        ?>
                        {{ csrf_field() }}
                        <input type="hidden" name="sysid" id="syside">
                        <input type="hidden" name="deptid" id="deptid" value="{{ $dept_id }}">
                        <div class="box box-primary box-solid">
                            <div class="box-header with-border">
                                <label>SPL: {{ $dt->id_overtime }}</label>
                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-default btn-xs"
                                        onclick="window.location.href='/Admin/Overtime'"><i
                                            class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="form-group">
                                    <label>Overtime Date</label>
                                    <input id="idot" type="hidden" value="{{ $dt->id }}" name="idot" class="form-control">
                                    <input id="idovertime" type="hidden" value="{{ $dt->id_overtime }}" name="id_overtime" class="form-control">
                                    <input id="dateon" type="hidden" value="{{ $dt->ot_date }}" name="date_on" class="form-control">
                                    <input id="dateonshow" type="text" value="{{ $dt->ot_date }}" class="form-control" disabled>
                                    <input type="hidden" id="iddraftovertime">
                                    <input id="iddept" type="hidden" name="iddept">
                                    <input id="nmdept" type="hidden" name="nmdept">
                                </div>
                                <div class="form-group">
                                    <label>Employee</label>
                                    <select id="idemployee" name="id_employee" class="form-control selectpicker" data-live-search="true">
                                        <option value=""></option>
                                        @foreach ($tb_employee as $dt2)
                                            <option value="{{ $dt2->id }}"> {{ $dt2->employee_name }} 
                                                ({{ $dt2->NIK }} - {{ $dt2->dept_code }})
                                            </option>
                                        @endforeach

                                    </select>
                                    <div class="pull-right" style="color:#F00;padding-right:10px;" id="info">&nbsp;
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>PIC/Subtitusi</label>
                                    <select name="pic" id="pic" class="form-control">
                                        <option></option>
                                        <option value="PIC">PIC</option>
                                        <option value="Subtitusi">Subtitusi</option>
                                    </select>
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

                                <div class="form-group khusus" style="padding-top:15px;">
                                    <label>Reason OT</label>
                                    <select name="reason_ot" id="reasonot" class="form-control">
                                    <!-- <select name="reason_ot" id="reasonot" class="form-control selectpicker" data-live-search="true"> -->
                                        <option></option>
                                        <!-- @foreach ($tb_reason_ot as $dt2)
                                            <option value="{{ $dt2->reason_ot }}">{{ $dt2->reason_ot }}</option>
                                        @endforeach -->
                                    </select>
                                </div>
                                <div class="form-group khusus">
                                    <label>Job OT</label>
                                    <select name="job_ot" id="jobot" class="form-control">
                                        <option></option>
                                        @foreach ($tb_job_ot as $dt2)
                                            <option value="{{ $dt2->job_ot }}">{{ $dt2->job_ot }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group khusus">
                                    <label>Customer</label>
                                    <select name="customer" id="customer" class="form-control">
                                        <option></option>
                                        @foreach ($tb_customer as $dt2)
                                            <option value="{{ $dt2->customer_code }}">{{ $dt2->customer_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- <div class="form-group khusus line">
                                    <label>Line</label>
                                    <select name="line" id="line" class="form-control" style="width: 100%">
                                    </select>
                                </div> -->
                                <div class="form-group umum">
                                    <label>Detail Reason/Target</label>
                                    <textarea name="reason" id="reason" class="form-control" rows="3" placeholder="Enter ..."></textarea>
                                </div>

                                <div class="form-group">
                                    <?php
                                        $date_top = date_add($Tgl, date_interval_create_from_date_string('+1 days'));
                                        $MaxTop = date_format($date_top, 'Y-m-d H:i');
                                    ?>
                                    <label>Plan Start</label>
                                    <input type="datetime-local" name="start_plan" class="form-control" id="startplan" min="{{$MinBottom}}" max="{{$MaxTop}}">
                                </div>
                                <div class="form-group">
                                    <label>Plan Finish</label>
                                    <input type="datetime-local" name="finish_plan" class="form-control" id="finishplan" min="{{$MinBottom}}" max="{{$MaxTop}}">
                                </div>
                                <div class="form-group">
                                    <div class="col-xs-5" style="padding:0px;padding-right:3px;">
                                        <label>Break Hours</label>
                                        <input type="hidden" name="otisoma" id="isoma">
                                        <select name="otisoma2" id="isoma2" class="form-control" disabled>
                                            <option value="0">0 Minute</option>
                                            <option value="30">30 Minutes</option>
                                            <option value="45">45 Minutes</option>
                                            <option value="90">90 Minutes</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-4" style="padding:0px;padding-left:3px;">
                                        <label>Plan Hours</label>
                                        <input type="number" id="hoursdraft" class="form-control" step="0.25"
                                            disabled>
                                        <div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
                                    </div>
                                    <div class="col-xs-3" style="padding:0px;padding-left:3px;">
                                        <label>Fix Hours</label>
                                        <input type="number" name="hours_plan" id="hoursplan" class="form-control"
                                            step="0.50" disabled>
                                        <div style="color:#F00;padding-right:10px;" id="info2">&nbsp;</div>
                                    </div>

                                </div>

                                <div class="form-group" style="padding-top:80px;">
                                    <div class="col-xs-7" style="padding:0px;padding-right:3px;">
                                        <label>Type</label>
                                        <input type="hidden" name="ot_category" id="otcategory">
                                        <select name="ot_category2" id="otcategory2" class="form-control" disabled>
                                            <option value=""></option>
                                            <option value="1">Lembur Awal/Ahhir</option>
                                            <option value="2">Lembur Hari Libur</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-5" style="padding:0px;padding-left:3px;">
                                        <label>Convertion</label>
                                        <input type="number" step="0.5" id="hoursconvertion"
                                            name="hours_convertion" class="form-control" disabled>
                                    </div>
                                </div>


                            </div>
                            <div class="box-footer">
                                <div class="form-group pull-right">
                                    <button type="button" class="btn btn-primary addmp" id="addmp">Submit</button>
                                </div>

                            </div>
                            <!-- /.box-body -->
                        </div>
                    @endforeach
                </div>
                <div class="col-xs-12 col-sm-12 col-lg-8">
                    <div class="row">
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
                                            <th style="width:70px;">Plan OT</th>
                                            <th>Line</th>
                                            <th>Reason/Target</th>
                                            <th style="width:50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="otdetail"></tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <div class="row" style="padding:20px;">
                                    <form action="/Admin/Overtime/Note" method="post">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="id_ot" value="{{ $idSPL }}">
                                        <div class="form-group">
                                            <label>Notes:</label>
                                            @foreach ($tb_pesan as $row)
                                                <div class="row" style="padding:5px 20px;">
                                                    <u style="color:#CCC;">{{ $row->penulis }} {{ $row->waktu }} :</u>
                                                    {{ $row->pesan }}
                                                </div>
                                            @endforeach
                                            <div class="row" style="padding:5px 20px;">
                                                <textarea name="pesan" class="form-control" rows="3" placeholder="Message ..."></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group pull-right">
                                            <input type="submit" class="btn btn-primary" value="Save Note">
                                        </div>
                                    </form>
                                </div>
                                <div class="form-group">
                                    <a href="/Admin/Overtime/Confirm/{{ $idSPL }}"><button type="button"
                                            class="btn btn-warning confirm-btn">Confirm</button></a>
                                    <a href="/Admin/Overtime/Preview/{{ $idSPL }}" target="_blank"><button
                                            type="button" class="btn btn-info preview-btn">Preview</button></a>
                                </div>
                            </div>
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
            @endif
        </div>
    @endif

@endsection
@section('Scripts')
    <!-- page script Tabel-->
    <script>
        $(function() {
            $('#table2').DataTable({
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': true,
                'info': true,
                "pageLength": 10,
                'autoWidth': false,
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
                "scrollX": true
            })
        })
    </script>
    <script>
        window.onload = function() {
            $.ajaxSetup({
                type: "POST",
                url: "/Admin/Overtime/Konten",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var id_overtime = $("#idovertime").val();
            $.ajax({
                data: {
                    id_overtime: id_overtime
                },
                success: function(respond) {
                    $("#otdetail").html(respond);
                    //alert('masuk');
                }
            })

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

        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        }
        $(document).on('click', '.addmp', function() {
            $.ajaxSetup({
                type: "POST",
                url: "/Admin/Overtime/Detail/Save",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var idot = $("#idot").val();
            var id_overtime = $("#idovertime").val();
            var id_employee = $("#idemployee").val();
            var ot_category = $("#otcategory").val();
            var date_on = $("#dateon").val();
            var reason_ot = $("#reasonot").val();
            var job_ot = $("#jobot").val();
            var customer = $("#customer").val();
            var reason = $("#reason").val();
            var start_plan = $("#startplan").val();
            var finish_plan = $("#finishplan").val();
            var otisoma = $("#isoma").val();
            var hours_plan = $("#hoursplan").val();
            var hours_convertion = $("#hoursconvertion").val();
            var iddraftovertime = $("#iddraftovertime").val();
            var line = $("#line").val();
            var dept_id = $("#deptid").val();
            var iddept = $("#iddept").val();
            var nmdept = $("#nmdept").val();
            var pic = $("#pic").val();
            var reference = $("#reference").val();
            //alert(hours_convertion);

            if (reason_ot == '') {
                alert('Category OT belum dipilih');
                return (false);
            }
            if (id_employee == "" || ot_category == "" || date_on == "" || reason == "" || start_plan == "" ||
                finish_plan == "" || otisoma == "" || hours_plan == "" || hours_convertion == "") {
                alert("Data belum lengkap, periksa & silahkan coba lagi");
                return (false);
            }
            
            $.ajax({
                data: {
                    idot: idot,
                    id_overtime: id_overtime,
                    id_employee: id_employee,
                    ot_category: ot_category,
                    date_on: date_on,
                    reason_ot: reason_ot,
                    job_ot: job_ot,
                    customer: customer,
                    reason: reason,
                    start_plan: start_plan,
                    finish_plan: finish_plan,
                    otisoma: otisoma,
                    hours_plan: hours_plan,
                    hours_convertion: hours_convertion,
                    iddraftovertime: iddraftovertime,
                    id_line_machine: 0,
                    deptid: dept_id,
                    iddept:iddept,
                    nmdept:nmdept,
                    pic:pic,
                    reference:reference,
                },
                success: function(respond) {
                    let str = respond;
                    let result = str.slice(0, 4);

                    if(result=='Note'){
                        alert(respond);
                    }else{
                        //alert('Employee telah ditambahkan');
                        document.getElementById("idemployee").focus();
                        $('body,html').animate({
                            scrollTop: 0
                        }, 800);
                        $("#otdetail").html(respond);
                        var iddraftovertime = 'tr' + $("#iddraftovertime").val();
                        var element = document.getElementById(iddraftovertime);
                        //$("#iddraftovertime").val('');
                    }
                }
            })
        });
        $(document).on('click', '#memo', function() {
            $("#iddraftovertime").val($(this).data('iddraftovertime'))
            $("#reasonot").val($(this).data('reasonot'))
            $("#customer").val($(this).data('customer'))
            $("#reason").val($(this).data('reason'))
            $("#startplan").val($(this).data('startplan'))
            $("#finishplan").val($(this).data('finishplan'))
            updateJam();
            $('body,html').animate({
                scrollTop: 0
            }, 800);
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
    <!-- Set Up Hours/Convertion -->
    <script>
        function updateJam() {
            var Awal = new Date($('#startplan').val());
            var Akhir = new Date($('#finishplan').val());

            var Thn = Akhir.getFullYear();
            var Bln = Akhir.getMonth() + 1;
            var Tgl = Akhir.getDate();
            var Hari = Akhir.getDay();
            var Tgla = Awal.getDate();
            var Blna = Awal.getMonth() + 1;
            var Thna = Awal.getFullYear();

            var Break1 = new Date(Thn + '-' + Bln + '-' + Tgl + ' 02:00:00');
            if (Hari == 5) {
                var Break2 = new Date(Thn + '-' + Bln + '-' + Tgl + ' 11:30:00');
            } else {
                var Break2 = new Date(Thn + '-' + Bln + '-' + Tgl + ' 12:00:00');
            }
            var Break3 = new Date(Thna + '-' + Blna + '-' + Tgla + ' 18:00:00');
            //alert (Awal+' '+Break3+' '+Akhir);
            var malamSenin = new Date(Thna + '-' + Blna + '-' + Tgla + ' 20:00:00');
            //Setting Isoma
            if (Awal < Break1 && Akhir > Break1) {
                $('#isoma').val('45');
                $('#isoma2').val('45');
            } else if (Awal < Break2 && Akhir > Break2) {
                if (Hari == 5) {
                    $('#isoma').val('90');
                    $('#isoma2').val('90');
                } else {
                    // $('#isoma').val('45');
                    // $('#isoma2').val('45');
                    $('#isoma').val('30');
                    $('#isoma2').val('30');
                }
                //else{$('#isoma').val('30');$('#isoma2').val('30');}
            } else if (Awal < Break3 && Akhir > Break3) {
                $('#isoma').val('30');
                $('#isoma2').val('30');
            } else {
                $('#isoma').val('0');
                $('#isoma2').val('0');
            }

            var n = ((Akhir - Awal) / 60000 / 60) - ($('#isoma').val() / 60);
            var sisa = n % 1;
            //Setting Category
            //if(Hari>0 && Hari<6){
            if (Hari > 0 && Hari < 6 && n < 6) {
                $('#otcategory').val(1);
                $('#otcategory2').val(1);
            } else {
                if (Awal >= malamSenin && Hari == 0) {
                    $('#otcategory').val(1);
                    $('#otcategory2').val(1);
                } else {
                    $('#otcategory').val(2);
                    $('#otcategory2').val(2);
                }
            }

            if (sisa < 0.5) {
                var add = 0;
            } else if (sisa < 0.75) {
                var add = 0.5;
            } else {
                var add = 1;
            }
            var fix = n - sisa + add;
            if (n < 0.75) {
                fix = 0;
            }

            $('#hoursdraft').val(n);
            $('#hoursplan').val(fix);

            var category = $('#otcategory').val();
            var plan = $('#hoursplan').val();
            var conv = 0;
            if (category == '1' && plan >= 1) {
                conv = conv + (1.5) + ((plan - 1) * 2);
            }
            if (category == '2') {
                conv = conv + (plan * 2);
                if (plan >= 9) {
                    conv = conv + 1;
                }
                if (plan >= 10) {
                    conv = conv + ((plan - 9) * 2);
                }
            }
            if (category == '3') {
                conv = conv + (plan * 2);
                if (plan >= 8) {
                    conv = conv + 1;
                }
                if (plan >= 9) {
                    conv = conv + ((plan - 8) * 2);
                }
            }
            if (category == '4') {
                conv = conv + (plan * 2);
                if (plan >= 6) {
                    conv = conv + 1;
                }
                if (plan >= 7) {
                    conv = conv + ((plan - 6) * 2);
                }
            }
            $('#hoursconvertion').val(conv);
            //Update bangkok
            if (plan <= 0 || plan > 22) {
                document.getElementById("addmp").disabled = true;
            } else {
                document.getElementById("addmp").disabled = false;
            }
            if (category == 1 && plan > 3) {
                $('#info2').text("Limit Harian 3 Jam");
            } else {
                $('#info2').text("");
            }

            var dateon = new Date($('#dateon').val());
            var dateonTgl = dateon.getDate();
            if (dateonTgl != Tgla) {
                alert('Informasi, Tanggal SPL dengan Plan Start tidak sama');
            }

        }
        $("#startplan").change(function() {
            updateJam();
        });
        $("#finishplan").change(function() {
            updateJam();
        });
        $("#isoma").change(function() {
            var now = new Date($('#startplan').val());
            var bitDate = new Date($('#finishplan').val());
            var n = ((bitDate - now) / 60000 / 60) - ($('#isoma').val() / 60);
            $('#hoursplan').val(n);

            var category = $('#otcategory').val();
            var plan = $('#hoursplan').val();
            var conv = 0;
            if (category == '1' && plan >= 1) {
                conv = conv + (1.5) + ((plan - 1) * 2);
            }
            if (category == '2') {
                conv = conv + (plan * 2);
                if (plan >= 8) {
                    conv = conv + 1;
                }
                if (plan >= 9) {
                    conv = conv + ((plan - 8) * 2);
                }
            }
            if (category == '3') {
                conv = conv + (plan * 2);
                if (plan >= 8) {
                    conv = conv + 1;
                }
                if (plan >= 9) {
                    conv = conv + ((plan - 8) * 2);
                }
            }
            if (category == '4') {
                conv = conv + (plan * 2);
                if (plan >= 6) {
                    conv = conv + 1;
                }
                if (plan >= 7) {
                    conv = conv + ((plan - 6) * 2);
                }
            }
            $('#hoursconvertion').val(conv);
            if (plan < 0) {
                document.getElementById("addmp").disabled = true;
            } else {
                document.getElementById("addmp").disabled = false;
            }
            if (category == 1 && plan > 3) {
                $('#info2').text("Limit Harian 3 Jam");
            } else {
                $('#info2').text("");
            }
        });
        $("#otcategory").change(function() {
            var category = $('#otcategory').val();
            var plan = $('#hoursplan').val();
            var conv = 0;
            if (category == '1' && plan >= 1) {
                conv = conv + (1.5) + ((plan - 1) * 2);
            }
            if (category == '2') {
                conv = conv + (plan * 2);
                if (plan >= 8) {
                    conv = conv + 1;
                }
                if (plan >= 9) {
                    conv = conv + ((plan - 8) * 2);
                }
            }
            if (category == '3') {
                conv = conv + (plan * 2);
                if (plan >= 8) {
                    conv = conv + 1;
                }
                if (plan >= 9) {
                    conv = conv + ((plan - 8) * 2);
                }
            }
            if (category == '4') {
                conv = conv + (plan * 2);
                if (plan >= 6) {
                    conv = conv + 1;
                }
                if (plan >= 7) {
                    conv = conv + ((plan - 6) * 2);
                }
            }
            $('#hoursconvertion').val(conv);
            if (plan < 0) {
                document.getElementById("addmp").disabled = true;
            } else {
                document.getElementById("addmp").disabled = false;
            }
            if (category == 1 && plan > 3) {
                $('#info2').text("Limit Harian 3 Jam");
                //document.getElementById("addmp").disabled = true;
            } else {
                $('#info2').text("");
                //document.getElementById("addmp").disabled = true;
            }
            //alert(conv);
        });
    </script>
    <!-- Delete Detail  -->
    <script type="text/javascript">
        // Delete Data
        $(document).on('click', '.delete-modal', function() {
            $('#delid').val($(this).data('delid'));
            $('#delname').text($(this).data('delname'));
            $('#modal-delete').modal('show');
        });
        $('.modal-footer').on('click', '.delete', function() {
            var x = $('#delid').val();
            window.location.href = '/Admin/Overtime/Detail/Delete/' + x;
        });
    </script>
    <!-- Sign Before -->
    <script type="text/javascript">
        // Confirm
        $(document).on('click', '.confirm-modal', function() {
            var x = $(this).data('before');
            window.location.href = '/Admin/Overtime/Detail/Before/' + x;
        });
    </script>
    <!-- Job OT -->
    <script>
        $("#reasonot").change(function() {
            $('#reason').val($('#jobot').val() + ' ' + $('#customer').val() + ' ' + $('#reasonot').val());
        });
        // $("#reasonot").change(function() {
        //     var reason = $(this).val();
        //     if (reason == "Others") {
        //         $(".line").prop('hidden', true);
        //     } else {
        //         $(".line").prop('hidden', false);
        //     }
        // })
        $("#jobot").change(function() {
            $('#reason').val($('#jobot').val() + ' ' + $('#customer').val() + ' ' + $('#reasonot').val());
        });
        $("#customer").change(function() {
            $('#reason').val($('#jobot').val() + ' ' + $('#customer').val() + ' ' + $('#reasonot').val());
        });
    </script>
    <script>
        $(document).ready(function() {
            $(".sembunyi").hide();
            var dept_id = $("#deptid").val();
            //if(dept_id!='7'||dept_id=='11'){
            if (dept_id > 0) {
                //$(".umum").hide();
                $(".khusus").show();
            } else {
                $(".umum").show();
                $(".khusus").hide();
            }
            //#region line Select
            $("#line").select2({
                placeholder: "Choose Line",
                multiple: false,
                allowClear: true,
                minimumInputLength: 0,
                ajax: {
                    url: "{{ route('Admin.Overtime.GetListLine') }}",
                    type: "post",
                    dataType: 'json',
                    quietMillis: 250,
                    data: function(params) {
                        return {
                            _token: document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content'),
                            searchTerm: params.term,
                            page: params.page || 1,
                            dept_id: dept_id
                        };
                    },
                    results: function(data, page) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                formatResult: function(element) {
                    return element.text + ' (' + element.id + ')';
                },
                formatSelection: function(element) {
                    return element.text + ' (' + element.id + ')';
                },
                escapeMarkup: function(m) {
                    return m;
                }
            });
        });
        //#endregion
    </script>
    <!-- Quota info -->
    <script>
        $(document).on('change', '#idemployee', function() {
            $.ajaxSetup({
                type: "POST",
                url: "/Admin/Overtime/Detail/Quota",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var id_employee = $("#idemployee").val();
            var tgl_ot = $("#dateon").val();
            var dept_id = $("#deptid").val();

            $.ajax({
                data: {
                    id_employee: id_employee,
                    dateon: tgl_ot,
                    dept_id:dept_id,
                },
                success: function(respond) {
                    const teks = respond;
                    const hasil = teks.split("#");
                    const selectElement = document.getElementById("pic");
                    $("#iddept").val(hasil[0]);
                    $("#nmdept").val(hasil[1]);
                    selectElement.value = hasil[2];
                    if(hasil[4]>0){
                        //$("#info").html(respond);
                        var x='0';
                    }else{
                        $("#info").html('Check Quota...!');
                    }
                }
            })
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
    </script>
@endsection
