
<?php $__env->startSection('Contents'); ?>

  <section class="content">
    <div class="row">
      <form role="form" action="/Compress" method="post" enctype="multipart/form-data">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <?php echo e(csrf_field()); ?>

        <div class="col-lg-4 col-sm-6 col-xs-12">
          <div class="box box-primary box-solid">
            <div class="box-header">
              <i class="fa fa-clock-o"></i>
              <h3 class="box-title">Form Manual <?php echo ucfirst($status_checktime);?></h3>
            </div>
            <div class="box-body">
              <label style="font-size:20px;"><?php echo e($employee_name); ?> </label>&nbsp;<label style="font-size:20px;font-weight:normal;">(NIK: <?php echo e($NIK); ?>)</label><br>
              <div style="font-size:16px;">Department <?php echo e($department); ?></div><br>
              <div class="form-group">
                <label style="font-size:16px;">Status Checktime </label>
                <div class="radio">
                  <label>
                    <input type="radio" name="status_kerja" id="statustl" value="TL" checked>
                    TL (Tugas Luar) / Vendor Stay
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="status_kerja" id="statusdri" value="DRIVER">
                    Driver
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="status_kerja" id="statuswfo" value="WFO">
                    WFH (Actual at SAI / WFO)
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="status_kerja" id="statuswfh" value="WFH">
                    WFH (Real at Home)
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label style="font-size:16px;">Upload Photo </label>
                <input type="file" name="foto" id="exampleInputFile">
                <b style="font-weight:normal;">Upload your photo with time stamp</b> 
                <?php if(session('size')!='')echo "<u style='color:red'>Upload Failed, max-size: 600KB</u>";?>
                </div>
            </div>
            <div class="box-footer">
              <div style="font-size:21px;font-weight:normal;">
                <span id="jam">00</span>:<span id="menit">00</span>:<span id="detik">00</span>
                <div class="pull-right" style="border:0px;">
                  <input type="submit" class="btn btn-primary" value="Submit">
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>

  </section>
      

    <?php if($message = Session::get('success')): ?>
		<?php if($message=='Berhasil'){?>
			<div class="alert alert-success alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 9999;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> Success Alert</h4>
				Berhasil, Terima kasih . . .
			</div>
		<?php }?>
		<?php if($message!='Berhasil'){?>
			<div class="alert alert-danger alert-dismissible" style="position:absolute;width:350px;right:10px;top:60px;z-index: 9999;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-info"></i> Failed Alert</h4>
				  <?php echo "Gagal ,".$message;?>
			</div>
		<?php }?>
    <?php endif; ?>
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

	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page/admin/m_checktime/realchecktime.blade.php ENDPATH**/ ?>