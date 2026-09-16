@extends('layouts/admin')
@section('Contents')
	<meta name="csrf-token" content="{{ csrf_token() }}">
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
	<div class="content-wrapper">
	<section class="content-header">
		<div class="box-header">
			<i class="fa fa-book"></i>
			<h3 class="box-title" id="judul" style="padding-bottom:25px;">e-Library</h3>
			<div class="pull-right">
				<a class="btn btn-app" href="/Training/DocumentDraft/0">
					<i class="fa fa-file-o"></i> Draft
				</a>
				<a class="btn btn-app" href="/Training/Document/0">
					<i class="fa fa-check-square-o"></i> Active
				</a>
				<a class="btn btn-app" href="/Training/DocumentArchieve/0">
					<i class="fa fa-trash"></i> InActive
				</a>
			</div>
		</div>
	</section>

	<!-- Main content -->
	<section class="content">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-xs-12">
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
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
								<th>Document Name</th>
								<th>Code Document</th>
								<th>Category</th>
								<th>Skill</th>
								<th>Nomor</th>
								<th>Revision</th>
								<th>Department</th>
								<th>Keywords</th>
								<th>Status</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php $no=0;?>
							@foreach($tb_training_document as $dt)
							<tr>
								<td><?php $no++;echo $no;?></td>
								<td>{{$dt->document_name}}</td>
								<td>{{$dt->code_document}}</td>
								<td>{{$dt->category}}</td>
								<td>{{$dt->skill}}</td>
								<td>{{$dt->nomor}}</td>
								<td>{{$dt->revision}}</td>
								<td>{{$dt->department}}</td>
								<td>{{$dt->keywords}}</td>
								<td>
									@if($dt->status == 1) Active
									@elseif($dt->status == 2) Inactive
									@else Draft
									@endif
								</td>
								<td>
									<div class="pull-right">
										<?php
											$panjang=strlen($dt->file_name);
											$mulai=$panjang-4;
											$extensi=substr($dt->file_name,$mulai,4);
											if($extensi=='.mp4'||$extensi=='.pdf'||$extensi=='.jpg'||$extensi=='.png'){?>
											<a href="/Training/Document/{{$dt->id}}" title="Preview" type="button" class="btn btn-primary btn-xs"><i class="fa fa-tv"></i></a>
										<?php }?>
										<a href="/Document/Download/{{$dt->id}}" title="Download" type="button" class="btn btn-info btn-xs"><i class="fa fa-download"></i></a>
										<button title="Keyword" type="button" class="keyword-form btn btn-primary btn-xs" data-iddocument="{{$dt->id}}" data-documentname="{{$dt->document_name}}"><i class="fa fa-key"></i></button>
										<button title="Edit" type="button" class="form btn btn-primary btn-xs" data-iddocument="{{$dt->id}}" data-documentname="{{$dt->document_name}}" data-training-name="{{$dt->training_name}}" data-category="{{$dt->category}}" data-skill="{{$dt->skill}}" data-nomor="{{$dt->nomor}}" data-revision="{{$dt->revision}}" data-code-document="{{$dt->code_document}}" data-department="{{$dt->department}}" data-information="{{$dt->information}}" data-status="{{$dt->status}}"><i class="fa fa-edit"></i></button>
										<button title="Delete" type="button" class="delete-modal btn btn-danger btn-xs" data-delid="{{$dt->id}}" data-delname="{{$dt->document_name}}"><i class="fa fa-trash"></i></button>
									</div>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<!-- /.box-body -->
			</div>
		</div>
		<div class="col-lg-12 col-md-12 col-xs-12">
			@if($id_doc>0)
			<div class="box box-primary" style="background:#FFF;">
				<div class="box-header">
					<i class="fa fa-tv"></i>
					<h3 class="box-title">{{$document_name}}</h3>
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
					@if($type=='mp4')
						<div class="embed-responsive embed-responsive-16by9">
							<iframe class="embed-responsive-item" src="{{ $document }}" frameborder="0" allowfullscreen></iframe>
						</div>
					@elseif($type=='pdf')
						<object data="{{ $document }}#toolbar=0" width="100%" height="800"></object>
					@else
			        	<img src="{{$document}}" style="width:800px;">
					@endif
				</div>
			</div>
			@endif
		</div>
	</div>
	<!-- /.row -->
	</section>
	<!-- /.content -->
    </div>

	<div class="modal fade" id="modal-form">
		<div class="modal-dialog box box-success" style="width:500px;">
			<div class="modal-content">
							<form id="document-form" action="/Document/Upload" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
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
								<label>Training Name</label>
								<input type="text" class="form-control" name="training_name" id="trainingname">
							</div>
							<div class="form-group">
								<input type="hidden" class="form-control" name="category" id="category" value="TRN" readonly>
								<label>Skill</label>
								<select class="form-control" name="skill" id="skill" required>
									<option value="">Select Skill</option>
									@foreach($tb_skill_type as $skill_type)
										<option value="{{$skill_type->skill_code}}">{{$skill_type->skill_code}}</option>
									@endforeach
								</select>
							</div>
							<div class="form-group">
								<label>Nomor</label>
								<select class="form-control" name="nomor" id="nomor">
									<option value="">New Nomor</option>
								</select>
							</div>
							<div class="form-group">
								<label>Revision</label>
								<input type="text" class="form-control" name="revision" id="revision" readonly placeholder="Automatic">
							</div>
							<div class="form-group">
								<label>Code Document</label>
								<input type="text" class="form-control" name="code_document" id="codedocument" readonly placeholder="Automatic">
							</div>
							<div class="form-group">
								<label>Department</label>
								<select class="form-control" name="department" id="department" required>
									<option value="">Select Department</option>
									@foreach($tb_department as $department)
										<option value="{{$department->dept_code}}">{{$department->dept_code}}</option>
									@endforeach
								</select>
							</div>
							@if(request()->user()->hasRole('training'))
							<div class="form-group" id="status-group" style="display:none;">
								<label>Status</label>
								<select class="form-control" name="status" id="status">
									<option value="0">Draft</option>
									<option value="1">Active</option>
									<option value="2">Inactive</option>
								</select>
							</div>
							@else
							<input type="hidden" name="status" value="0">
							@endif
							<div class="form-group">
								<label>Information</label>
								<textarea class="form-control" name="information" id="information" rows="3"></textarea>
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

	<div class="modal fade" id="modal-keyword">
		<div class="modal-dialog box box-primary" style="width:400px;">
			<div class="modal-content">
				<form id="keyword-form">
					{{ csrf_field() }}
					<div class="modal-header">
						<b>DOCUMENT KEYWORDS</b>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span></button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="keyword-document-id" name="id_training_document">
						<div class="form-group">
							<label>Document Name</label>
							<input type="text" class="form-control" id="keyword-document-name" readonly>
						</div>
						<div class="form-group">
							<label>Add New Keyword</label>
							<input type="text" class="form-control" id="new-keyword" name="keyword" maxlength="100">
						</div>
						<div class="form-group">
							<label>Remove Keywords </label>
							<div id="keyword-list">
								<span class="text-muted">Loading...</span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary pull-right">Save</button>
					</div>
				</form>
			</div>
		</div>
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


@endsection
@section('Scripts')
	<script>
		var trainingDocumentNumbers = @json($tb_training_document_numbers);

		function refreshDocumentNumbers() {
			var category = $('#category').val();
			var skill = $('#skill').val();
			var nomor = $('#nomor');

			nomor.empty().append('<option value="">New Nomor</option>');
			trainingDocumentNumbers
				.filter(function (documentNumber) {
					return documentNumber.category === category && documentNumber.skill === skill;
				})
				.forEach(function (documentNumber) {
					nomor.append($('<option>', {
						value: documentNumber.nomor,
						text: documentNumber.nomor + ' (New Revision)'
					}));
				});
		}

		$(document).on('change', '#skill', refreshDocumentNumbers);
	</script>
	<!-- page script Tabel-->
	<script>
		$(function () {
			$('#tables').DataTable({
				'paging': true,
				'lengthChange': true,
				'searching': true,
				'ordering': true,
				'info': true,
				'pageLength': 10,
				'autoWidth': false
			});
		});

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
		$(document).on('click', '.keyword-form', function() {
			var documentId = $(this).data('iddocument');
			$('#keyword-document-id').val(documentId);
			$('#keyword-document-name').val($(this).data('documentname'));
			$('#new-keyword').val('');
			$('#keyword-list').html('<span class="text-muted">Loading...</span>');
			$('#modal-keyword').modal('show');

			$.get('/Document/Keyword/' + documentId, function(response) {
				var keywordList = $('#keyword-list').empty();
				if (!response.keywords.length) {
					keywordList.html('<span class="text-muted">No keyword saved.</span>');
					return;
				}
				$.each(response.keywords, function(index, keyword) {
					$('<label class="checkbox-inline" style="display:block;margin:0 0 8px 0;"></label>')
						.append($('<input>', { type: 'checkbox', name: 'delete_ids[]', value: keyword.id }))
						.append(document.createTextNode(' ' + keyword.keyword))
						.appendTo(keywordList);
				});
			}).fail(function() {
				$('#keyword-list').html('<span class="text-danger">Failed to load keywords.</span>');
			});
		});

		$('#keyword-form').on('submit', function(event) {
			event.preventDefault();
			$.ajax({
				url: '/Document/Keyword/Save',
				type: 'POST',
				data: $(this).serialize(),
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				success: function(response) {
					$('#modal-keyword').modal('hide');
					window.location.reload();
				},
				error: function(xhr) {
					alert(xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to save keywords.');
				}
			});
		});

		// Form
			$(document).on('click', '.form', function() {
				var formButton = $(this);
				var docid=formButton.data('iddocument');
				$('#idComponent').val(docid);
				$('#documentname').val(formButton.data('documentname'));
				if(docid!=''){
					document.getElementById("trainingdoc").style.visibility = "hidden";
					$('#status-group').show();
					$('#trainingname').val(formButton.data('training-name'));
					$('#category').val(formButton.data('category'));
					$('#skill').val(formButton.data('skill')).prop('disabled', false);
					refreshDocumentNumbers();
					$('#skill').prop('disabled', true);
					$('#nomor').val(String(formButton.data('nomor'))).prop('disabled', true);
					$('#revision').val(formButton.data('revision'));
					$('#codedocument').val(formButton.data('code-document'));
					$('#department').val(formButton.data('department'));
					$('#information').val(formButton.data('information'));
					$('#status').val(formButton.data('status'));
				}else{
					document.getElementById("trainingdoc").style.visibility = "visible";
					$('#status-group').hide();
					$('#status').val('2');
					$('#trainingname').val('');
					$('#category').val('TRN');
					$('#skill').prop('disabled', false).val('');
					$('#nomor').prop('disabled', false);
					refreshDocumentNumbers();
					$('#revision').val('');
					$('#codedocument').val('');
					$('#department').val('');
					$('#information').val('');
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
							window.location.href = '/Training/Document/0';
						}else{
							alert(respond);
						}
					}
				})
			});
		// Delete End
	</script>
@endsection
