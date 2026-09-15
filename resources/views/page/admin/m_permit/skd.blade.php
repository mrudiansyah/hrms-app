@extends('layouts/home')
@section('Contents')
  <?php if ($NIK!=='') {?>
    <section class="content">

       <form role="form" action="/SKD/Compress" method="post" enctype="multipart/form-data">
          <meta name="csrf-token" content="{{ csrf_token() }}">
					{{ csrf_field() }}
          <div class="row">
            <div class="col-lg-3 col-sm-6 col-xs-12" id="tabel1">
              <div class="box box-danger box-solid">
                <div class="box-header">
                  <i class="fa fa-photo"></i>
                  <h3 class="box-title">Form Upload SKD</h3>
                  <div class="pull-right"><a href="/SKD/Exit" style="color:#FFF;font-size:16px;padding:2px 10px;">Exit</a></div>
                </div>
                <div class="box-body">
                  <label style="font-size:20px;">{{$employee_name}} </label>&nbsp;<label style="font-size:20px;font-weight:normal;">(NIK: {{$NIK}})</label><br>
                  <div style="font-size:16px;">Department {{$department}}</div><br>

                  <div class="row" style="padding:15px;">
                    <div class="col-xs-6" style="padding:0px;padding-right:3px;">

                      <div class="form-group">
                        <label>Mulai</label>
                        <input type="date" id="startleave" name="start_leave" class="form-control waktu">
                        <input type="hidden" id="idkaryawan" name="id_employee" value="{{$id_employee}}">
                        <input type="hidden" id="leavecount" name="leave_count">
                      </div>

                    </div>
                    <div class="col-xs-6" style="padding:0px;padding-right:3px;">

                      <div class="form-group">
                        <label>Akhir</label>
                        <input type="date" name="finish_leave" id="finishleave" class="form-control waktu">								
                      </div>

                    </div>
                  </div>

                  <div class="form-group">
                    <label>Diagnosa</label>
                    
                    <select id="diagnosalist" class="selectpicker form-control" data-live-search="true">
                      <option value=""></option>
                      @foreach($tb_diagnosa as $diagnosa)
                        <option value="{{$diagnosa->diagnosa}}">{{$diagnosa->diagnosa}}</option>
                      @endforeach
                    </select>
                  </div>


                    <div class="form-group" id="kondisional">
                      <label>Diagnosa</label>
                      <input type="text" id="diagnosa" name="diagnosa" class="form-control">
                    </div>
                  <div class="form-group">
                    <label style="font-size:16px;">Upload SKD </label>
                    <input type="file" name="foto" id="exampleInputFile">
                    <?php if(session('size')!='')echo "<u style='color:red'>Upload Failed, max-size: 600KB</u>";?>
                    </div>
                </div>
                <div class="box-footer">
                  <div style="font-size:21px;font-weight:normal;">
                    <span id="jam">00</span>:<span id="menit">00</span>:<span id="detik">00</span>
                    <div class="pull-right" style="border:0px;">
                      <input type="submit" class="btn btn-danger" value="Submit" id="confirm">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
    </section>

    <script>
      window.setTimeout("waktu()", 1000);
    
      function waktu() {
        var waktu = new Date();
        var jam = ("0" + waktu.getHours()).slice(-2);
        var menit = ("0" + waktu.getMinutes()).slice(-2);
        var detik = ("0" + waktu.getSeconds()).slice(-2);
        setTimeout("waktu()", 1000);
        document.getElementById("jam").innerHTML = jam;
        document.getElementById("menit").innerHTML = menit;
        document.getElementById("detik").innerHTML = detik;
      }
    </script>
    
  <?php }else{?>
    <section class="content">
      <form role="form" action="/SKD/NIK/Check" method="post">
          <meta name="csrf-token" content="{{ csrf_token() }}">
          {{ csrf_field() }}
          <div style="width:330px;">
              <label style="font-size:16px;">Enter Your NIK </label>
              <div class="input-group">
                  <input type="text" placeholder="Nomor Induk Karyawan . . ." name="NIK" class="form-control" style="width:100%;" value="{{$NIK}}">
                  <span class="input-group-btn">
                      <input type="submit" class="btn btn-danger btn-flat" value="Check">
                  </span>
              </div>
          </div>
      </form>
    </section>
  <?php }?>

    @if ($message = Session::get('success'))
		<?php if($message=='Berhasil, SKD Asli tetap harus diserahkan ke HR...!'){?>
			<div class="alert alert-success alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 9999;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> Success Alert</h4>
				Berhasil, Terima kasih . . .
			</div>
		<?php }else{?>
			<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 9999;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> Failed Alert</h4>
				  <?php echo "Gagal ,".$message;?>
			</div>
		<?php }?>
    @endif



@endsection
@section('Scripts')
  <script>
    window.setTimeout(function() {
      $(".alert").fadeTo(500, 0).slideUp(500, function(){
      $(this).remove(); 
      });
    }, 5000);
		$( document ).ready(function() {
      var start=$('#startleave').val();
      var finish=$('#finishleave').val();
      if(start==''||finish==''){
        document.getElementById("confirm").disabled = true;
      }
      $('#kondisional').hide();
		});
    $(document).on('change', '#diagnosalist', function() {
      var isi=$('#diagnosalist').val();
      if(isi=='OTHER'){
        $('#kondisional').show();
        $('#diagnosa').val('');
      }else{
        $('#diagnosa').val(isi)
      }
    });
    $(document).on('change', '.waktu', function() {
      var start=$('#startleave').val();
      var finish=$('#finishleave').val();
      var idemployee=$('#idkaryawan').val();

      if(start==''||finish==''){
        document.getElementById("confirm").disabled = true;
      }else{

        const c = new Date(start);
        let day_start = c.getDay();
        const d = new Date(finish);
        let day = d.getDay();
        if(day_start==0||day==0){
          alert('Jangan pilih Minggu, Jadwal kerja Anda dimulai Hari Senin meskipun masuknya Minggu malam');
          $('#finishleave').val('');
        }else{

          if(finish<start){
            alert("Finish tidak bisa lebih kecil dari Start");
            document.getElementById("confirm").disabled = true;
            $('#finishleave').val('');
          }else{
            $(".tambahan").hide();
            document.getElementById("confirm").disabled = false;
          }
          $.ajaxSetup({
            type:"POST",
            url: "/SKD/Leave/Count",
            cache: false,
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
          });
          $.ajax({
            data:{start:start,finish:finish,idemployee:idemployee},
            success: function(respond){
              //alert(respond);
              $("#leavecount").val(respond);
            }
          })

        }
      }
    });
  </script>

@endsection
