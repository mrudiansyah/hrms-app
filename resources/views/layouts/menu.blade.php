<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="{{ asset('/public/assets/dist/img/user.jpg') }}" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p>{{ Auth::user()->name }}</p>
				<a href="#"><i class="fa fa-circle text-success"></i> Online</a>
			</div>
		</div>

		<!-- sidebar menu: : style can be found in sidebar.less -->
		<?php $sekarang = date('Y-m-d H:i:s');?>
		<ul class="sidebar-menu" data-widget="tree">
			<?php if (request()->user()->hasRole('candidate')) {?>
			<li><a href="/candidate"><i class="fa fa-user"></i> Candidate</a></li>
			<?php }?>
			<?php if (request()->user()->hasRole('add_user') || request()->user()->hasRole('role') || request()->user()->hasRole('user_role')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'user_management'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-users text-aqua"></i> <span>User Management</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					@if(request()->user()->hasRole('add_user'))
						<li><a href="/user-management"><i class="fa fa-user"></i> Users</a></li>
					@endif
					@if(request()->user()->hasRole('role'))
						<li><a href="/role-management"><i class="fa fa-key"></i> Roles</a></li>
						<li><a href="/userrole-management"><i class="fa fa-cogs"></i> User Role</a></li>
					@endif
				</ul>
			</li>
			<?php }?>
			<?php if (request()->user()->hasRole('payroll')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'overtime' || $menu == 'overtime_summary' || $menu == 'overtime_tax' || $menu == 'capture_assignment' || $menu == 'summary_assignment'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-money text-aqua"></i> <span>Payroll</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li class="treeview<?php if (isset($menu) && ($menu == 'overtime_summary' || $menu == 'overtime_tax'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-clock-o text-orange"></i> <span>Overtime</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/payroll/summary_overtime/0/0"><i class="fa fa-circle"></i> Capture</a></li>
							<li><a href="/payroll/tax_overtime/0/0"><i class="fa fa-circle"></i> Summary</a></li>
						</ul>
					</li>
					<li class="treeview<?php if (isset($menu) && ($menu == 'capture_assignment' || $menu == 'summary_assignment'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-clock-o text-orange"></i> <span>Assigment</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/payroll/capture_assignment/0/0"><i class="fa fa-circle"></i> Capture</a></li>
							<li><a href="/payroll/summary_assignment/0/0"><i class="fa fa-circle"></i> Summary</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<?php }?>
			<?php if (request()->user()->hasRole('manifest')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'manifest' || $menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area' || $menu == 'outside' || $menu == 'scurity'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-clock-o text-aqua"></i> <span>Manifest</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li class="treeview<?php if (isset($menu) && ($menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-gear text-orange"></i> <span>Setup Area</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/master_ap"><i class="fa fa-users"></i> Assembly Point</a></li>
							<li><a href="/master_area"><i class="fa fa-map-marker"></i> Data Area</a></li>
							<li><a href="/working_area"><i class="fa fa-street-view"></i> Employee Area</a></li>
						</ul>
					</li>
					<li><a href="/outside"><i class="fa fa-car text-orange"></i> Outside Assigment</a></li>
					<li class="treeview<?php if (isset($menu) && ($menu == 'scurity'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-edit text-orange"></i> <span>Scurity Check</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/scurity/assigment"><i class="fa fa-file-text-o"></i> Tugas Luar</a></li>
							<li><a href="/scurity/permit"><i class="fa fa-file-text"></i> Form Ijin</a></li>
						</ul>
					</li>
					<li><a href="/manifest"><i class="fa fa-list-alt text-orange"></i> Verify Manifest</a></li>
				</ul>
			</li>
			<?php }?>
			<?php if (request()->user()->hasRole('improvement')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'improvement' || $menu == 'qcc' || $menu == 'gqcc'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-line-chart text-aqua"></i> <span>Improvement</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li class="treeview<?php if (isset($menu) && ($menu == 'qcc'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-gear text-orange"></i> <span>QCC</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/qcc_team"><i class="fa fa-users"></i> Teams</a></li>
							<li><a href="/qcc_schedule"><i class="fa fa-map-marker"></i> Schedule</a></li>
							<li><a href="/qcc_activity"><i class="fa fa-street-view"></i> Activity</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<?php }?>

		</ul>
	</section>
	<!-- /.sidebar -->
</aside>