@extends('layouts/admin')
@section('Contents')
   <!-- Contents -->
   <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
		#tables th {
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
            background-color: #d3d8d8ff;
            color: black;
		}	
        /* Modal and Toggle Styles */
        .glass-modal .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .glass-modal .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.05);
            border-radius: 15px 15px 0 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
            margin-bottom: 0;
        }
        .toggle-switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        input:checked + .toggle-slider {
            background-color: #28a745;
        }
        input:focus + .toggle-slider {
            box-shadow: 0 0 1px #28a745;
        }
        input:checked + .toggle-slider:before {
            -webkit-transform: translateX(24px);
            -ms-transform: translateX(24px);
            transform: translateX(24px);
        }
        .toggle-slider.round {
            border-radius: 34px;
        }
        .toggle-slider.round:before {
            border-radius: 50%;
        }
        
        .role-loader {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #666;
        }
    </style>
	<div class="content-wrapper">
		<section class="content-header">
			<h1>
				User Roles
				<small>Manage User and Role Assignments</small>
			</h1>
			<ol class="breadcrumb">
				<li>
				<a href="#">
					<i class="fa fa-calendar"></i> 
					<?php 
						date_default_timezone_set("Asia/Jakarta");
						echo date('l, d M Y H:i');
					?>
				</a>
				</li>
			</ol>
		</section>

		<section class="content">
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-primary" style="background:#FFF;">
					<div class="box-header">
						<i class="fa fa-users"></i>
						<h3 class="box-title">System Users</h3>
					</div>
					<div class="box-body">
						<table id="tables" class="table table-hover">
							<thead>
								<tr>
									<th style="width:50px;">ID</th>
									<th>NIK</th>
									<th>Name</th>
									<th>Email</th>
									<th>Registered</th>
									<th>Expired Date</th>
									<th>Status</th>
                                    <th style="width:150px; text-align:center;">Action</th>
								</tr>
							</thead>
							<tbody>
								@foreach($tb_user as $dt)
								<tr>
									<td>{{$dt->id}}</td>
									<td>{{$dt->nik}}</td>
									<td>{{$dt->name}}</td>
									<td>{{$dt->email}}</td>
									<td>{{$dt->created_at}}</td>
									<td>{{$dt->expired_date}}</td>
									<td>
										@if($dt->email_verified_at == null)
											<span class="label label-danger">Unverified</span>
										@else
											<span class="label label-success">Verified</span>
										@endif
									</td>
                                    <td align="center">
                                        <button class="btn btn-info btn-xs btn-manage-roles" data-uid="{{$dt->id}}" data-uname="{{$dt->name}}">
                                            <i class="fa fa-cogs"></i> Manage Roles
                                        </button>
                                    </td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		</section>
  	</div>

    <!-- Role Management Modal -->
    <div class="modal fade glass-modal" id="modal-roles" tabindex="-1" role="dialog" aria-labelledby="modalRolesLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="modalRolesLabel"><i class="fa fa-user"></i> <span id="modal-user-name">User</span></h4>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto; padding: 10px 20px;">
                    <input type="hidden" id="active-user-id">
                    <input type="text" id="role-search" class="form-control" placeholder="🔍 Search roles..." style="margin-bottom: 15px; border-radius: 8px;">
                    <div id="role-switches-container">
                        <div class="role-loader"><i class="fa fa-spin fa-spinner"></i> Loading...</div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; background: #fafafa; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('Scripts')
	<script>
		$(document).ready(function() {
			var table = $('#tables').DataTable({
				'paging'      : true,
				'lengthChange': false,
				'searching'   : true,
				'ordering'    : true,
				'info'        : true,
				"pageLength"  : 10,
				'autoWidth'   : false,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
			});
		});

        var baseUrl = "{{ url('/') }}";
        
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Open Modal and Load Roles
		$(document).on('click', '.btn-manage-roles', function() {
			var uid = $(this).data('uid');
			var uname = $(this).data('uname');
            
            $('#active-user-id').val(uid);
            $('#modal-user-name').text(uname);
            $('#role-switches-container').html('<div class="role-loader"><i class="fa fa-spin fa-spinner"></i> Loading roles...</div>');
            
            $('#modal-roles').modal('show');

			$.ajax({
                type: "POST",
				url: baseUrl + "/userrole-management/selectUser",
				data: { user_id: uid },
				success: function(respond) {
					$("#role-switches-container").html(respond);
				},
                error: function() {
                    $("#role-switches-container").html('<div class="alert alert-danger">Failed to load roles.</div>');
                }
			});
		});

        // Toggle Switch Action (Add/Remove Role)
        $(document).on('change', '.role-switch', function() {
            var isChecked = $(this).is(':checked');
            var roleId = $(this).data('roleid');
            var userId = $('#active-user-id').val();
            
            var targetUrl = isChecked 
                ? baseUrl + "/userrole-management/addRole" 
                : baseUrl + "/userrole-management/removeRole";

            // Optional: Provide instant visual feedback / show mini notification
            $.ajax({
                type: "POST",
                url: targetUrl,
                data: { user_id: userId, role_id: roleId },
                success: function(respond) {
                    console.log(respond.status);
                    // Could add toast notification here if desired
                },
                error: function() {
                    alert('An error occurred while saving the role.');
                    // Revert the switch if failed
                    $(".role-switch[data-roleid='" + roleId + "']").prop('checked', !isChecked);
                }
            });
        });

        // Search Roles functionality
        $(document).on('keyup', '#role-search', function() {
            var value = $(this).val().toLowerCase();
            $("#role-switches-container .role-toggle-item").filter(function() {
                var text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(value) > -1);
            });
        });
	</script>
@endsection
