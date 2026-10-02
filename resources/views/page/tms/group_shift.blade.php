@extends('layouts/admin')
<!-- <link rel="stylesheet" href="{{ asset('/public/assets/bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}"> -->
    
@section('Contents')
    <style>
        .colorpicker-alpha.colorpicker-visible,
        .colorpicker-hue.colorpicker-visible,
        .colorpicker-saturation.colorpicker-visible,
        .colorpicker-selectors.colorpicker-visible,
        .colorpicker.colorpicker-visible {
            z-index: 9999 !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="content-wrapper">
        <section class="content-header">
            <h1 onclick="">
                {{ $juduls }}
                <small><?php if (isset($subjudul)) {
                    echo $subjudul;
                } else {
                    echo 'TMS';
                } ?></small>
                <div class="pull-right">
                    &nbsp;
                </div>
            </h1>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary" style="background:#FFF;">
                        <div class="box-body">
                            <div class="col-xs-12">
                                <div class="box-header">
                                    <!--
                                    <button class="btn btn-primary btn-sm" id="btnAddWT" onclick="ShowWorkTime(0)"><i class="fa fa-plus-circle"></i> Add Work Time</button>
                                    -->
                                    <div class="pull-right" id="work_time">
                                        @foreach($tb_work_time as $dt)
                                            @if($dt->isactive==1)
                                            <button class="btn btn-default btn-xs formtime" style="background:{{$dt->background}}; color:{{$dt->color}}"
                                            data-checkin="{{$dt->check_in}}"
                                            data-checkout="{{$dt->check_out}}"
                                            data-id="{{$dt->id}}"
                                            data-isactive="{{$dt->isactive}}"
                                            data-isoma_start="{{$dt->isoma_start}}"
                                            data-isoma_finish="{{$dt->isoma_finish}}"
                                            >
                                                <?php echo $dt->id.' '.substr($dt->check_in,0,5).' - '.substr($dt->check_out,0,5) ?>
                                            </button>
                                            @endif
                                        @endforeach
                                        <?php
                                        //echo $work_time;
                                        ?>
                                    </div>
                                </div>
                                <div class="box-body">

                                    <div class="col-lg-3 col-sm-6 col-xs-12">
                                        <div class="box box-info box-solid" style="border:#CCC;">
                                            <div class="box-header with-border" style="height:48px;">
                                                <i class="fa fa-circle"></i>
                                                <h3 class="box-title">Groups</h3>
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool" id="btnAddGroup"
                                                        onclick="AddGroup(0)"><i class="fa fa-plus-circle"></i> Add
                                                        Group</button>
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                            class="fa fa-minus"></i></button>

                                                </div>
                                            </div>
                                            <div class="box-body" style="padding-left:10px;">
                                                <table id="table5" class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Group</th>
                                                            <th>Cycle</th>
                                                            <!-- <th>Action</th> -->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($tb_group as $dt)
                                                            <tr>
                                                        <td>{{$dt->group_code}}</td>
                                                        <td>{{$dt->cycle_day}} Days</td>
                                                            </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12">
                                        <div class="box box-warning box-solid" style="border:#CCC;">
                                            <div class="box-header with-border" style="height:48px;">
                                                <i class="fa fa-circle-o"></i>
                                                <h3 class="box-title">Sub Group</h3>
                                                <div class="box-tools pull-right">
                                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                                            class="fa fa-minus"></i></button>
                                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i
                                                            class="fa fa-times"></i></button>
                                                </div>
                                            </div>
                                            <div class="box-body">
                                                <table id="table4" class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:150px;">Group</th>
                                                            <th><i class="fa fa-calendar">&nbsp;Cycle Start</i></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($tb_group_shift as $dt)
                                                            <tr>
                                                                <td>{{ $dt->shift_code }}</td>
                                                                <td>{{ $dt->start_implement }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-sm-12 col-xs-12">

                                        <div class="box box-primary box-solid" style="border:#CCC;">
                                            <div class="box-header">
                                                <i class="fa fa-clock-o"></i>
                                                <h3 class="box-title">Group Schedule</h3>
                                                <div class="box-tools pull-right">
                                                    <div class="form-group">
                                                        <div class="pull-right" style="padding:2px;">
                                                            <select name="group" class="form-control showcycle" id="showcycle">
                                                                <option>&nbsp;</option>
                                                                @foreach ($tb_group as $dt)
                                                                    <option value="{{ $dt->id }}">{{ $dt->group_code }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="box-body" style="overflow-x:scroll;">

                                                <table id="table4" class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:100px;text-align:center;">Cycle Days</th>
                                                            <th style="width:100px;text-align:center;">ID Time</th>
                                                            <th><i class="fa fa-clock-o">&nbsp;Checkin</i></th>
                                                            <th><i class="fa fa-clock-o">&nbsp;Checkout</i></th>
                                                            <th>Break</th>
                                                            <th style="width:50px;">Advance</th>
                                                            <th style="width:50px;">Cross</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="tbodycycle"></tbody>
                                                </table>
                                            </div>
                                            <!-- /.box-body -->
                                        </div>

                                    </div>

                                </div>
                                <!-- /.box-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="modalGroup">
            <div class="modal-dialog box box-primary" style="width:250px;">
                <form>
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="idworkgroup" id="idworkgroup">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Add Group</h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_group" id="id_group" value="0">
                            <div class="form-group">
                                <label for="group_code">Group Code</label>
                                <input type="text" name="group_code" id="group_code" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="cycle_change">Cycle Change</label>
                                <input type="number" name="cycle_change" id="cycle_change" class="form-control"
                                    min="1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary pull-left" id="saveGroup"
                                onclick="SaveGroup()">Simpan</button>
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="modalWT">
            <div class="modal-dialog">
                
                <input type="hidden" name="idworkgroup" id="idworkgroup">
                <div class="modal-content">
                    <form role="form" action="/TMS/TimeListSave" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Work Time</h4>
                    </div>
                    <div class="modal-body">
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    {{ csrf_field() }}
                        <input type="hidden" name="id_time" id="id_time">
                        <input type="hidden" name="id_group" id="id_group" value="0">
                        <div class="form-group col-md-6">
                            <label for="check_in">Check in</label>
                            <input type="time" name="check_in" id="check_in" class="form-control" step="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="check_out">Check out</label>
                            <input type="time" name="check_out" id="check_out" class="form-control" step="1">
                        </div>
                        <div class="form-group col-md-12">
                            <hr>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="isoma_start">Start Isoma</label>
                            <input type="time" name="isoma_start" id="isoma_start" class="form-control" step="1">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="isoma_finish">End Isoma</label>
                            <input type="time" name="isoma_finish" id="isoma_finish" class="form-control" step="1">
                        </div>
                        <!-- <div class="form-group colors">
                            <label for="isoma_finish">Legend Color</label>
                            <input type="text" class="form-control warna  colorpicker-element" id="warna">
                        </div> -->
                        <!-- <div class="form-group">
                            <label>
                                <input type="checkbox" class="form-check-input" nama="isActiveTime" id="isActiveTime" value="1"
                                    checked=false>
                                Non Aktif
                            </label>
                        </div> -->
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary pull-left" id="updateTimes_"
                            >Simpan</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="modal-edit">
            <div class="modal-dialog box box-primary" style="width:250px;">
                <form>
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="idworkgroup" id="idworkgroup">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="cycledays"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Working Time</label>
                                <select name="group" class="form-control" id="idworktime">
                                    <option value="0"></option>
                                    @foreach ($tb_work_time as $dt)
                                        <option value="{{ $dt->id }}"><?php echo date('H:i', strtotime($dt->check_in)) . ' ~ ' . date('H:i', strtotime($dt->check_out)) . ' (' . date('H:i', strtotime($dt->isoma_start)) . '~' . date('H:i', strtotime($dt->isoma_finish)) . ')'; ?></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary savecycle pull-left">Simpan</button>
                            <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </form>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </div>
@endsection
<!-- {{-- <script src="../../plugins/iCheck/icheck.min.js"></script> --}}
<script
    src="{{ asset('/public/assets/bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}">
</script> -->

@section('Scripts')
    <script>
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function() {
                $(this).remove();
            });
        }, 5000);
        $(document).on('change', '.showcycle', function() {
            $.ajaxSetup({
                type: "POST",
                url: "{{ $site }}/TMS/Group/Choose",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var valgroup = $(this).val();
            $.ajax({
                data: {
                    groupid: valgroup
                },
                success: function(respond) {
                    $("#tbodycycle").html(respond);
                }
            })
        });
        $(document).on('click', '.editcycle', function() {
            $('#id').val($(this).data('cycleid'));
            $('#idworkgroup').val($(this).data('idworkgroup'));
            $('#cycledays').text('Edit Cycle Days : ' + $(this).data('cycledays'));
            $('#idworktime').val($(this).data('idworktime'));
            $('#modal-edit').modal('show');
        });
        $(document).on('click', '.savecycle', function() {
            $.ajaxSetup({
                type: "POST",
                url: "{{ $site }}/TMS/Group/Save",
                cache: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var id = $('#id').val();
            var idworkgroup = $('#idworkgroup').val();
            var idworktime = $('#idworktime').val();
            $.ajax({
                data: {
                    id: id,
                    idworktime: idworktime,
                    idworkgroup: idworkgroup
                },
                success: function(respond) {
                    $("#tbodycycle").html(respond);
                }
            })
            $('#modal-edit').modal('hide');
        });

        function emptyStr(str) {
            return !str || !/[^\s]+/.test(str);
        }

        function AddGroup(id) {
            if (id == 0) {
                $("#modalGroup").modal('show');
                $("#group_code").val("");
                $("#cycle_change").val("");
                $("#id_group").val(id);
            } else {
                var token = $('meta[name="csrf-token"]').attr('content');
                var data = {
                    _token: token,
                    id: id
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('TMS.GetDataGroupId') }}",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        $("#modalGroup").modal('show');
                        $("#group_code").val(data.group_code);
                        $("#cycle_change").val(data.cycle_day);
                        $("#id_group").val(data.id);

                    }
                });
            }
        }

        function SaveGroup() {
            var id = $("#id_group").val();
            var group_code = $("#group_code").val();
            var cycle_change = $("#cycle_change").val();
            var token = $('meta[name="csrf-token"]').attr('content');

            if (emptyStr(group_code)) {
                // swal("Info","Please fill group code","info")
                swal({
                    type: "info",
                    html: true,
                    title: "Info",
                    text: "<b>Please fill group code</b>"
                });
            } else if (emptyStr(cycle_change) || cycle_change == 0) {
                // swal("Info","Please fill cycle change","info")
                swal({
                    type: "info",
                    html: true,
                    title: "Info",
                    text: "<b>Please fill cycle change</b>"
                });
            } else {
                var data = {
                    _token: token,
                    id: id,
                    group_code: group_code,
                    cycle_change: cycle_change
                }
                $.ajax({
                    type: "post",
                    url: "{{ route('TMS.SaveGroup') }}",
                    data: data,
                    dataType: "json",
                    success: function(data) {
                        if (data.process_status == 1) {
                            $("#modalGroup").modal('hide');
                            swal({
                                type: "success",
                                title: "Success",
                                text: data.msg_process
                            });
                            ReloadGroup();
                        } else {
                            swal({
                                type: "error",
                                title: "Info",
                                text: data.msg_process
                            });
                        }
                    }
                });
            }
        }

        function ReloadGroup() {
            var tblGroup = $("#table5").DataTable({
                searching: false,
                processing: true,
                paging: false,
                retrieve: true,
                autoWidth: false,
                responsive: true,
                ordering: false,
                ajax: {
                    type: 'POST',
                    url: "{{ route('TMS.GetDataGroup') }}",
                    data: function(d) {
                        d._token = document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content');
                    },
                    cache: false,
                    dataType: 'json',
                    error: function(xhr) {
                        var doc = $.parseHTML(xhr.responseText);
                        var titleNode = doc.filter(function(node) {
                            return node.localName === "title";
                        });
                        var msg = titleNode[0].textContent;
                        swal("Error", "Error :" + msg, "error");
                    }
                },
                columns: [{
                        data: 'group_code',
                        className: 'text-left'
                    },

                    {
                        data: 'cycle_day',
                        className: 'text-left'
                    },
                    {
                        data: 'action'
                    }
                ],

            });
            tblGroup.ajax.reload();
        }

        function DeleteGroup(id) {
            swal({
                title: 'Are you sure for delete this data?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                closeOnConfirm: false,
            }, function(isConfirm) {
                //window.onkeydown = previousWindowKeyDown;
                if (isConfirm) {
                    var data = {
                        _token: document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content'),
                        id: id
                    }
                    $.ajax({
                        type: "POST",
                        url: "{{ route('TMS.RemoveGroupId') }}",
                        data: data,
                        dataType: "json",
                        success: function(data) {
                            if (data.process_status == 1) {
                                $("#modalGroup").modal('hide');
                                swal({
                                    type: "success",
                                    title: "Success",
                                    text: data.msg_process
                                });
                                ReloadGroup();
                            } else {
                                swal({
                                    type: "error",
                                    title: "Info",
                                    text: data.msg_process
                                });
                            }
                        },
                        error: function(xhr) {
                            var doc = $.parseHTML(xhr.responseText);
                            var titleNode = doc.filter(function(node) {
                                return node.localName === "title";
                            });
                            var msg = titleNode[0].textContent;
                            swal("Error", "Error :" + msg, "error");
                        }
                    });
                }
            });
        }

        $(document).on("click", ".formtime", function() {
            $("#check_in").val($(this).data('checkin'));
            $("#check_out").val($(this).data('checkout'));
            $("#id_time").val($(this).data('id'));
            $("#isActiveTime").val($(this).data('isactive'));
            $("#isoma_start").val($(this).data('isoma_start'));
            $("#isoma_finish").val($(this).data('isoma_finish'));
            //$("#warna").val(data.warna);
            var isactive = $(this).data('isactive');
            if (isactive == 1) {
                $("#isActiveTime").prop("checked", false);
                $("#isActiveTime").val($(this).data('isactive'));
            } else {
                $("#isActiveTime").prop("checked", true);
                $("#isActiveTime").val($(this).data('isactive'));

            }
            $("#modalWT").modal('show');
            // console.log(id);
            // var data = {
            //     _token: document.querySelector('meta[name="csrf-token"]')
            //         .getAttribute('content'),
            //     id_work_time: id,
            // };
            // $.ajax({
            //     type: "POST",
            //     url: "{{ route('TMS.ShowWorkTime') }}",
            //     data: data,
            //     dataType: "json",
            //     success: function(data) {
            //         $("#modalWT").modal('show');
            //         $("#check_in").val(data.check_in);
            //         $("#check_out").val(data.check_out);
            //         $("#id_time").val(data.id);
            //         $("#isActiveTime").val(data.isactive);
            //         $("#isoma_start").val(data.isoma_start);
            //         $("#isoma_finish").val(data.isoma_finish);
            //         $("#warna").val(data.warna);
            //         var isactive = data.isactive;
            //         if (isactive == 1) {
            //             $("#isActiveTime").prop("checked", false);
            //             $("#isActiveTime").val(data.isactive);
            //         } else {
            //             $("#isActiveTime").prop("checked", true);
            //             $("#isActiveTime").val(data.isactive);

            //         }
            //     }
            // });
            // $("#modalWT").modal('show');
        });
        $(document).on("click", "#isActiveTime", function() {
            if (this.checked) {
                $(this).attr("value", "0");
            } else {
                $(this).attr("value", "1");
            }
        });
		$('.modal-footer').on('click', '#updateTime', function() {
            var check_in = $("#check_in").val();
            var check_out = $("#check_out").val();
            var id_time = $("#id_time").val();
            var isActive = $("#isActiveTime").val();
            var isoma_start = $("#isoma_start").val();
            var isoma_finish = $("#isoma_finish").val();

            var datas = {
                _token: document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                id_time: id_time,
                check_in: check_in,
                check_out: check_out,
                isActive: isActive,
                isoma_start: isoma_start,
                isoma_finish: isoma_finish
            }
            
			$.ajaxSetup({
				type:"POST",
				url: "/TMS/UpdateTime",
				cache: false,
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});

			$.ajax({
				data:datas,
				success: function(respond){
					// if(respond=='Sukses'){
					// 	location.reload();
					// }else{
					// 	alert(respond);
					// }
					location.reload();
				}
			})
		});

        function SaveTime() {
            var check_in = $("#check_in").val();
            var check_out = $("#check_out").val();
            var id_time = $("#id_time").val();
            var isActive = $("#isActiveTime").val();
            var isoma_start = $("#isoma_start").val();
            var isoma_finish = $("#isoma_finish").val();
            //var warna = $("#warna").val();

            if (emptyStr(check_in)) {
                swal({
                    type: "info",
                    html: true,
                    title: "Info",
                    text: "<b>Please fill Check in Time</b>"
                });
            } else if (emptyStr(check_out)) {
                swal({
                    type: "info",
                    html: true,
                    title: "Info",
                    text: "<b>Please fill Check Out Time</b>"
                });
            } else {
                swal({
                    title: 'Are you sure for Add this data?',
                    html: true,
                    text: "<p>Check in : <b>" + check_in + "</b> </p> <p>Check Out : <b>" + check_out + "</b></p>",
                    type: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    closeOnConfirm: false,
                }, function(isConfirm) {
                    if (isConfirm) {
                        var data = {
                            _token: document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            id_time: id_time,
                            check_in: check_in,
                            check_out: check_out,
                            isActive: isActive,
                            isoma_start: isoma_start,
                            isoma_finish: isoma_finish
                            //background: warna
                        }
                        $.ajax({
                            type: "POST",
                            url: "{{ route('TMS.SaveTime') }}",
                            data: data,
                            dataType: "json",
                            success: function(data) {
                                if (data.process_status == 1) {
                                    $("#modalWT").modal('hide');
                                    swal({
                                        type: "success",
                                        title: "Success",
                                        text: data.msg_process
                                    });
                                    GetDataWorkTime()

                                    // ReloadGroup();
                                } else {
                                    swal({
                                        type: "error",
                                        title: "Info",
                                        text: data.msg_process
                                    });
                                }
                            }
                        });
                    }
                });

            }
        }


        function GetDataWorkTime() {
            $.ajax({
                type: "POST",
                url: "{{ route('TMS.GetDataWorkTime') }}",
                data: {
                    _token: document.querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                success: function(data) {
                    console.log(data);
                    $("#work_time").html(data)
                }
            });
        }
    </script>
@endsection
