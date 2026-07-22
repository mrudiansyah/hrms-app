<aside class="main-sidebar">
	<!-- sidebar: style can be found in sidebar.less -->
	<section class="sidebar">
		<!-- Sidebar user panel -->
		<div class="user-panel">
			<div class="pull-left image">
				<img src="<?php echo e(asset('/assets/dist/img/user.jpg')); ?>" class="img-circle" alt="User Image">
			</div>
			<div class="pull-left info">
				<p><?php echo e(Auth::user()->name); ?></p>
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
					<i class="fa fa-users text-red"></i> <span>User Management</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<?php if(request()->user()->hasRole('add_user')): ?>
						<li><a href="/user-management"><i class="fa fa-user"></i> Users</a></li>
					<?php endif; ?>
					<?php if(request()->user()->hasRole('role')): ?>
						<li><a href="/role-management"><i class="fa fa-key"></i> Roles</a></li>
						<li><a href="/userrole-management"><i class="fa fa-cogs"></i> User Role</a></li>
						<li><a href="/Staff"><i class="fa fa-circle-o"></i> Staff Admin</a></li>
					<?php endif; ?>
				</ul>
			</li>
			<?php }?>

			<?php if (request()->user()->hasRole('admin_shift')||request()->user()->hasRole('admin_employee')||request()->user()->hasRole('admin_calendar')) {?>
				<li class="treeview<?php if(isset($menu)&&($menu=='shift'||$menu=='employee'||$menu=='leader'||$menu=='calendar'))echo ' active';?>">
				<a href="#">
				<i class="fa fa-database"></i> <span>Master Data</span>
				<span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
				</span>
				</a>
				<ul class="treeview-menu">
				<?php if (request()->user()->hasRole('admin_shift')) {?>
					<!-- <li class="treeview<?php if(isset($menu)&&$menu=='shift')echo ' active';?>">
					<a href="#">
						<i class="fa fa-clock-o"></i> <span>Shift</span>
						<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
					</a>
					<ul class="treeview-menu">
						<li><a href="/Admin/Shift"><i class="fa fa-circle-o"></i> Working Time</a></li>
						<?php if (request()->user()->hasRole('root')) {?>
						<li><a href="/Admin/Shift/SetUp"><i class="fa fa-circle-o"></i> Set-Up Shift</a></li>
						<?php }?>
					</ul>
					</li> -->
				<?php }if (request()->user()->hasRole('admin_employee')||request()->user()->hasRole('contract')||request()->user()->hasRole('staff')) {?>
					<li class="treeview<?php if(isset($menu)&&$menu=='employee')echo ' active';?>">
					<a href="#">
						<i class="fa fa-user"></i> <span>Employee</span>
						<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
					</a>
					<ul class="treeview-menu">
						<!-- <li><a href="/Admin/NewEmployee"><i class="fa fa-circle"></i> New Employee</a></li> -->
						<li><a href="/Admin/Employee"><i class="fa fa-circle"></i> Employee List</a></li>
						<li><a href="/Admin/Department/0/0"><i class="fa fa-circle"></i> Recap Database</a></li>

						<?php if (request()->user()->hasRole('contract')) {?>
						<li class="treeview<?php if(isset($menu)&&$menu=='employee'&&isset($submenu)&&$submenu=='contract')echo ' active';?>">
							<a href="#">
							<i class="fa fa-circle"></i> <span>Contract</span>
							<span class="pull-right-container">
							<i class="fa fa-angle-left pull-right"></i>
							</span>
							</a>
							<ul class="treeview-menu">
								<!-- <li class="treeview<?php if(isset($menu)&&$menu=='employee'&&isset($submenu)&&$submenu=='contract')echo ' active';?>">
									<a href="#">
									<i class="fa fa-circle-o"></i> <span> Agreement</span>
									<span class="pull-right-container">
									<i class="fa fa-angle-left pull-right"></i>
									</span>
									</a>
									<ul class="treeview-menu">
										<li><a href="/Agreement/Kontrak"><i class="fa fa-circle-o"></i>SAI Contract</a></li>
										<li><a href="/Agreement/Magang"><i class="fa fa-circle-o"></i>Magang</a></li>
									</ul>
								</li> -->
								<li><a href="/Status/Active"><i class="fa fa-circle-o"></i> Registered</a></li>
								<li><a href="/Status/Draft"><i class="fa fa-circle-o"></i> Non Active</a></li>
								<?php if (request()->user()->hasRole('ksk_hr')) {?>
									<li><a href="/Status/KSK/0"><i class="fa fa-circle-o"></i> KSK</a></li>
								<?php }?>
								<!-- <li><a href="/Status/Letter/0"><i class="fa fa-circle-o"></i> Letter</a></li> -->
								<?php if (request()->user()->hasRole('allowance')) {?>
									<!-- <li><a href="/Kompensasi/0"><i class="fa fa-circle-o"></i> Kompensasi</a></li> -->
								<?php }?>
							</ul>
						</li>
						<?php }?>

						<?php if (request()->user()->hasRole('staff')) {?>
						<!-- <li><a href="/Staff"><i class="fa fa-circle"></i> Staff Admin</a></li> -->
						<?php }?>

						<!-- <li><a href="/Department/Bagian/0"><i class="fa fa-circle"></i> Position</a></li> -->

					</ul>
					</li>
					<li><a href="/Leader"><i class="fa fa-user"></i> Direct Leader</a></li>
				<?php }if (request()->user()->hasRole('admin_calendar')) {?>
					<!-- <li class="<?php if(isset($menu)&&$menu=='calendar')echo ' active';?>">
					<a href="/Admin/Freeday">
						<i class="fa fa-calendar"></i> <span>Calendars</span>
					</a>
					</li> -->
				<?php }?>
				<?php if (request()->user()->hasRole('hr_access')) {?>
					<!-- <li class="<?php if(isset($menu)&&$menu=='kategori')echo ' active';?>">
					<a href="/Admin/OTCategory">
						<i class="fa fa-circle-o"></i> <span>OT Category</span>
					</a>
					</li> -->
				<?php }?>
				</ul>
			</li>
			<?php }?>
			<?php if(request()->user()->hasRole('hr_access')): ?>
				<li><a href="/Setup"><i class="fa fa-gear"></i> SetUp Utility</a></li>
			<?php endif; ?>


			<?php if (request()->user()->hasRole('admin_department')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'recruitment'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-user text-yellow"></i> <span>Personalia</span>
					<span class="pull-right-container">
						<i class="fa fa-angle-left pull-right"></i>
					</span>
				</a>
				<ul class="treeview-menu">
					<li class="treeview<?php if (isset($menu) && ($menu == 'recruitment'))
		echo ' active';?>">
						<a href="#">
							<i class="fa fa-user-plus"></i> <span>Recruitment</span>
							<span class="pull-right-container">
								<i class="fa fa-angle-left pull-right"></i>
							</span>
						</a>
						<ul class="treeview-menu">
							<li><a href="/FPPK"><i class="fa fa-circle"></i> FPPK</a></li>
						</ul>
					</li>
					<li><a href="<?php echo e(route('renewal.index')); ?>"><i class="fa fa-edit"></i> Renewal</a></li>
				</ul>
			</li>
			<?php }?>
			<?php if (request()->user()->hasRole('payroll')) {?>
			<li class="treeview<?php if (isset($menu) && ($menu == 'overtime' || $menu == 'overtime_summary' || $menu == 'overtime_tax' || $menu == 'capture_assignment' || $menu == 'summary_assignment'))
		echo ' active';?>">
				<a href="#">
					<i class="fa fa-money text-green"></i> <span>Payroll</span>
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
					<i class="fa fa-line-chart text-green"></i> <span>Improvement</span>
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
</aside><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/layouts/menu.blade.php ENDPATH**/ ?>