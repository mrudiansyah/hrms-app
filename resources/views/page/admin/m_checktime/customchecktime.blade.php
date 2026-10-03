@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   
	<div class="content-wrapper">
		<section class="content-header">
			<h1 onclick="">
				Manual Checktime
				<small>custom</small>
			</h1>
		</section>	
		<section class="content">
        <form role="form" action="" method="post">
        <meta name="csrf-token" content="{{ csrf_token() }}">
					{{ csrf_field() }}
          <div class="row">
            <div class="col-lg-4 col-sm-6 col-xs-12" id="tabel1" style="padding:10px;">
              <div class="box box-primary box-solid">
                <div class="box-header">
                  <i class="fa fa-clock-o"></i>
                  <h3 class="box-title">Form Checktime</h3>
                </div>
                <div class="box-body">
                  <div class="form-group">
                    <label>Working Category</label>
                    <select id="statuskerja" name="status_kerja" class="form-control">
                      <option value=""></option>
                      <option value="Outside">Outside (Customer Stay)</option>
                      <option value="WFH">WFH (Work from Home)</option>
                      <option value="WFO">WFO (Work from Office) / Reguler Working</option>
                    </select>
                    <!-- <input type="text" name="bagian" id="bagian" class="form-control"> -->
                  </div>
                  <div class="form-group">
                    <label>Actual Checktime</label>
                    <input type="datetime-local" name="actual_checktime" id="actualchecktime" class="form-control">
                  </div>
                  <div class="form-group">
                    <div class="raw radio">
                      <div class="col-xs-4">
                      <label>
                        <input type="radio" name="status_checktime" id="statusin" value="IN">Checkin
                      </label>
                      </div>
                      <div class="col-xs-8">
                      <label>
                        <input type="radio" name="status_checktime" id="statusout" value="OUT">Checkout
                      </label>
                      </div>
                    </div>
                  </div>
                  <br><br>
                  <div class="form-group">
                    <label>NIK Karyawan</label>
                    <input type="text" id="pin" name="NIK" class="form-control">
                    <input type="text" id="pin2">
                    <input type="text" id="pin3">
                    <input type="text" id="statuschecktime">
                  </div>
                  <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="employee_name" id="employeename" class="form-control" autocomplete="off">
                  </div>
                  <div class="form-group">
                    <label>Department</label>
                    <input type="text" name="department" id="department" class="form-control" autocomplete="off">
                  </div>
                 </div>
               <div class="box-footer">
                  <div class="pull-right" style="border:0px;">
                    <button type="button" class="btn btn-primary" id="kirim">Submit</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
		</section>
  	</div>
    <!-- /.Content -->
@endsection
@section('Scripts')
	<!-- page script Tabel-->
	<script>
	$(function () {
		$('#table1').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		'autoWidth'   : true
		})
		$('#table2').DataTable({
		'paging'      : true,
		'lengthChange': true,
		'searching'   : true,
		'ordering'    : true,
		'info'        : true,
		"pageLength"  : 25,
		'autoWidth'   : true
		})
	})
	</script>
	<!-- page script alert-->
<script>
	$("#pin").keyup(function(){
		
    $.ajaxSetup({
      type:"POST",
      url: "/EMS/Admin/Checktime/Ambil",
      cache: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var id = $("#pin").val();
    $.ajax({
      data:{id:id},
      success: function(respond){
				$("#pin2").val(respond);
				var str = $('#pin2').val();
				var res = str.split("#");
				$('#employeename').val(res[0]);
				$('#department').val(res[1]);
        $('#pin3').val(res[2]);
      }
    })
  });
  $(document).on('click', '#statusin', function() {
        $("#statuschecktime").val('IN');
  });
  $(document).on('click', '#statusout', function() {
    $("#statuschecktime").val('OUT');
  });
  $("#kirim").click(function(){
    $.ajaxSetup({
      type:"POST",
      url: "/EMS/Admin/Checktime/Simpan",
      cache: false,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    var checktime=$("#actualchecktime").val();
    var nik=$("#pin").val();
    var employeename=$("#employeename").val();
    var pin=$("#pin3").val();
    var status=$("#statuskerja").val();
    var statusct=$("#statuschecktime").val();
    if(checktime==''||nik==''||pin==''||status==''){
      alert('Data belum lengkap');
      window.location.href="/EMS/Admin/Checktime/Custom";
    }
    $.ajax({
      data:{PIN:pin,checktime:checktime,employee_name:employeename,NIK:nik,status_kerja:status,status_checktime:statusct},
      success: function(respond){
        window.location.href="/EMS/Admin/Checktime/Custom";
      }
    })
  });

</script>
@endsection
