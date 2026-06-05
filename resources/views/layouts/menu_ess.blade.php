<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('/public/assets/dist/img/user.jpg') }}" class="img-circle" alt="User Image"
                    onclick="location.href='/Profile';" style="cursor:pointer;">
            </div>
            <div class="pull-left info" onclick="location.href='/Profile';" style="cursor:pointer;">
                <p>{{ Auth::user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <?php if (request()->user()->hasRole('ess')) {?>
            <li class="<?php    if (isset($menu) && $menu == 'profile')
        echo ' active';?>"><a href="/Profile"><i class="fa fa-user"></i> <span>Profile</span></a></li>
            <?php }?>
            <?php if (request()->user()->hasRole('ess')) {?>
            <li class="treeview<?php    if (isset($menu) && $menu == 'slip_gaji')
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-calculator"></i> <span>Salary Slip</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="/SlipGaji"><i class="fa fa-circle-o"></i> <span>Slip Gaji</span></a></li>
                    <li><a href="/SlipOT"><i class="fa fa-circle-o"></i> <span>Slip Overtime</span></a></li>
                </ul>
            </li>
            @if(isset($ess_leave) && $ess_leave == 1)
                <li><a href="/Leave"><i class="fa fa-edit"></i> <span>Cuti Tahunan</span></a></li>
            @endif
            <?php }?>
            <?php if (request()->user()->hasRole('ess') && request()->user()->hasRole('training')) {?>
            <li><a href="/Training/Invitation/"><i class="fa fa-mortar-board"></i> <span>Training</span></a>
            </li>
            <?php }?>
            <?php if (request()->user()->hasRole('ess') && request()->user()->hasRole('training')) {?>
            <li><a href="/Documents/0"><i class="fa fa-book"></i> <span>Training Document</span></a></li>
            <?php }?>
            <li><a href="/Performance"><i class="fa fa-line-chart"></i> <span>Performance</span></a></li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>