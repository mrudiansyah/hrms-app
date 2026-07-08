
<?php $__env->startSection('Contents'); ?>
	<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
	<!-- Contents -->
   	<style>
		#tablesx th {
		border-top: 1px solid #999;
		border-bottom: 1px solid #999;
		background-color: #2F4F4F;
		color: white;
		}	
        .table1 tr:hover {
		  cursor:pointer;
        }
		#tables th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#tables tbody tr:hover{
			cursor:pointer;
		}
		#table2 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table2 tbody tr:hover{
			cursor:pointer;
		}
		#table3 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table3 tbody tr:hover{
			cursor:pointer;
		}
		#table4 th {
		border-top: 2px solid #999;
		border-bottom: 2px solid #999;
		}	
		#table4 tbody tr:hover{
			cursor:default;
		}
    </style>
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1 onclick="">
			Archive
		</h1>
	</section>

	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-lg-5 col-md-12 col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-list"></i>
					<h3 class="box-title">List Document</h3>
					<div class="box-tools pull-right">
						<button type="button" class="btn btn-success btn-xs form" data-iddocument="" data-documentname=""><i class="fa fa-plus"></i> &nbsp;Add New</button>
						<button type="button" class="btn btn-primary btn-xs" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<button type="button" class="btn btn-danger btn-xs" data-widget="remove"><i class="fa fa-times"></i></button>
					</div>
				</div>
				<div class="box-body" style="overflow-x: scroll;">
					<table id="tables" class="table table-hover">
						<thead>
							<tr>
								<th style="width:30px;">No</th>
								<th>File Name</th>
							</tr>
						</thead>
						<tbody>
							<?php $no=0;?>
							<?php $__currentLoopData = $tb_training_document; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td><?php echo e($dt->document_name); ?>

									<div class="pull-right">
										<?php
											$panjang=strlen($dt->file_name);
											$mulai=$panjang-4;
											$extensi=substr($dt->file_name,$mulai,4);
											if($extensi=='.mp4'||$extensi=='.pdf'||$extensi=='.jpg'||$extensi=='.png'){?>
											<a href="/Documents/<?php echo e($dt->id); ?>" title="Preview" type="button" class="btn btn-primary btn-xs"><i class="fa fa-tv"></i></a>
										<?php }?>
										<a href="/Document/Download/<?php echo e($dt->id); ?>" title="Download" type="button" class="btn btn-info btn-xs"><i class="fa fa-download"></i></a>
										<button title="Edit" type="button" class="form btn btn-primary btn-xs" data-iddocument="<?php echo e($dt->id); ?>" data-documentname="<?php echo e($dt->document_name); ?>"><i class="fa fa-edit"></i></button>
										<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="<?php echo e($dt->id); ?>" data-delname="<?php echo e($dt->document_name); ?>"><i class="fa fa-trash"></i></button>
									</div>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		<div class="col-lg-7 col-md-12 col-xs-12">
			<?php if($id_doc>0): ?>
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-tv"></i>
					<h3 class="box-title"><?php echo e($document_name); ?></h3>
					<div class="pull-right">
						&nbsp;
					</div>
				</div>
				<div class="box-body">
					<?php 
						$isi="storage/".$file_name;
						$document=asset($isi);
						$type=strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
						//echo $id_doc;
					?>
					<?php if($type=='mp4'): ?>
						<div class="embed-responsive embed-responsive-16by9">
							<iframe class="embed-responsive-item" src="<?php echo e($document); ?>" frameborder="0" allowfullscreen></iframe>
						</div>
					<?php elseif($type=='pdf'): ?>
						<object data="<?php echo e($document); ?>#toolbar=0" width="800" height="500"></object>
					<?php else: ?>
			        	<img src="<?php echo e($document); ?>" style="width:800px;">
					<?php endif; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<!-- /.row -->
	</section>
	<!-- /.content -->

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:350px;">
			<div class="modal-content">
					<form action="/Document/Upload" method="post" enctype="multipart/form-data">
					<?php echo e(csrf_field()); ?>

						<div class="modal-header">	
							<b>FORM UPLOAD DOCUMENTS</b>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span></button>
						</div>
						<div class="modal-body">
							<input type="hidden" id="idComponent" name="id_document" class="form-control">
							<div class="form-group">
								<label>Document Name</label>
								<input type="text" class="form-control" name="document_name" id="documentname">								
							</div>
							<div class="form-group">
								<input type="file" name="training_doc" id="trainingdoc">								
							</div>
							<i>File extension: pdf, jpg, png, mp4</i>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
							<input type="submit" class="btn btn-success pull-right" id="simpan" value="Submit">
						</div>
					</form>
			</div>

			<!-- /.modal-content -->
		</div>
	<!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="modal-delete">
		<div class="modal-dialog box box-danger" style="width:400px;">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Delete Confirmation</h4>
				</div>
				<div class="modal-body">
					Click Yes to Delete : <b id="delname1"></b> ?
					<input type="hidden" id="delid1">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger pull-left delete" data-dismiss="modal">Yes, Delete</button>
					<button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
				</div>
			</div>
			
			<!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	</div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('Scripts'); ?>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#table2').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
		$(function () {
			$('#table3').DataTable({
			'paging'      : true,
			'lengthChange': true,
			'searching'   : true,
			'ordering'    : true,
			'info'        : true,
			"pageLength"  : 10,
			'autoWidth'   : false,
			})
		})
	</script>
	<script>
		window.setTimeout(function() {
			$(".alert").fadeTo(500, 0).slideUp(500, function(){
			$(this).remove(); 
			});
		}, 5000);
	</script>
	<script type="text/javascript">
		// Form
			$(document).on('click', '.form', function() {
				var docid=$(this).data('iddocument');
				$('#idComponent').val($(this).data('iddocument'));
				$('#documentname').val($(this).data('documentname'));
				if(docid!=''){
					document.getElementById("trainingdoc").style.visibility = "hidden";
				}else{
					document.getElementById("trainingdoc").style.visibility = "visible";
				}
				$('#modal-form').modal('show');
			});

		// Form End
		// Delete Data
			$(document).on('click', '.delete-modal', function() {
				$('#delid1').val($(this).data('delid'));
				$('#delname1').text($(this).data('delname'));
				$('#modal-delete').modal('show');
			});
			$('.modal-footer').on('click', '.delete', function() {
				var x=$('#delid1').val();

				$.ajaxSetup({
					type:"POST",
					url: "/Delete/Document",
					cache: false,
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					}
				});
				$.ajax({
					data:{id:x},
					success: function(respond){
						if(respond=='Sukses'){
							window.location.href = '/Documents/0';
						}else{
							alert(respond);
						}
					}
				})
			});
		// Delete End
	</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/home', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/page_ess/training_document.blade.php ENDPATH**/ ?>