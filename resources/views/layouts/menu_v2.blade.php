<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset('/assets/dist/img/user.jpg') }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Auth::user()->name }}</p>
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <?php $sekarang = date('Y-m-d H:i:s');?>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <!-- DASHBOARD -->
            @if(request()->user()->hasRole('dashboard'))
                    <li class="<?php    if (isset($menu) && $menu == 'dashboard')
                echo 'active'; ?>">
                        <a href="/Dashboard"><i class="fa fa-dashboard text-light-blue"></i> <span>Dashboard</span></a>
                    </li>
            @endif

            <!-- USER MANAGEMENT & ACCESS -->
            @if (request()->user()->hasRole('add_user') || request()->user()->hasRole('role') || request()->user()->hasRole('user_role') || request()->user()->hasRole('hr_access'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'user_management' || $menu == 'setup' || $menu == 'whatsapp_wuzapi'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-gears text-red"></i> <span>Configuration</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(request()->user()->hasRole('add_user') || request()->user()->hasRole('role'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'user_management'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-gears"></i> <span>User Management</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                @if(request()->user()->hasRole('add_user'))
                                                    <li><a href="/user-management"><i class="fa fa-user text-red"></i> Users List</a></li>
                                                @endif
                                                @if(request()->user()->hasRole('role'))
                                                    <li><a href="/role-management"><i class="fa fa-key text-red"></i> Roles Management</a></li>
                                                    <li><a href="/userrole-management"><i class="fa fa-cogs text-red"></i> User Role
                                                            Assignment</a>
                                                    </li>
                                                    <li><a href="/Staff"><i class="fa fa-user-md text-red"></i> Staff Admin</a></li>
                                                @endif
                                            </ul>
                                        </li>
                            @endif
                            @if(request()->user()->hasRole('hr_access'))
                                <li><a href="/Setup"><i class="fa fa-gear"></i> <span>Setup Utility</span></a></li>
                            @endif
                            @if(request()->user()->hasRole('root'))
                                <li><a href="/whatsapp"><i class="fa fa-whatsapp"></i> <span>Whatsapp Test</span></a></li>
                            @endif
                        </ul>
                    </li>
            @endif

            <!-- MANIFEST & AREA -->
            @if (request()->user()->hasRole('manifest'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'manifest' || $menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area' || $menu == 'outside' || $menu == 'scurity'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-street-view text-red"></i> <span>Manifest</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview<?php    if (isset($menu) && ($menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area'))
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-gear"></i> <span>Setup Area</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/master_ap"><i class="fa fa-circle-o"></i> Assembly Point</a></li>
                                    <li><a href="/master_area"><i class="fa fa-circle-o"></i> Data Area</a></li>
                                    <li><a href="/working_area"><i class="fa fa-circle-o"></i> Employee Area</a></li>
                                </ul>
                            </li>
                            <li><a href="/outside"><i class="fa fa-car"></i> Outside Assignment</a></li>
                            <li class="treeview<?php    if (isset($menu) && ($menu == 'scurity'))
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-shield"></i> <span>Security Check</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/scurity/assigment"><i class="fa fa-circle-o"></i> Tugas Luar</a></li>
                                    <li><a href="/scurity/permit"><i class="fa fa-circle-o"></i> Form Ijin</a></li>
                                </ul>
                            </li>
                            <li><a href="/manifest"><i class="fa fa-list-alt"></i> Verify Manifest</a></li>
                        </ul>
                    </li>
            @endif

            <!-- EMPLOYEE MANAGEMENT -->
            @if(request()->user()->hasRole('admin_shift') || request()->user()->hasRole('admin_employee') || request()->user()->hasRole('admin_calendar'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'shift' || $menu == 'employee' || $menu == 'leader' || $menu == 'calendar'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-id-card-o text-aqua"></i> <span>Employee Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (request()->user()->hasRole('admin_employee') || request()->user()->hasRole('contract') || request()->user()->hasRole('staff'))
                                <li><a href="/Admin/Employee"><i class="fa fa-users"></i> Employee List</a></li>
                                <li><a href="/Admin/Department/0/0"><i class="fa fa-pie-chart"></i> Employee Recap</a></li>

                                @if (request()->user()->hasRole('contract'))
                                        <li class="treeview<?php            if (isset($menu) && $menu == 'employee' && isset($submenu) && $submenu == 'contract')
                                    echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-file-text-o"></i> <span>Contract</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/Status/Active"><i class="fa fa-circle-o"></i> Registered Active</a></li>
                                                <li><a href="/Status/Draft"><i class="fa fa-circle-o"></i> Non Active</a></li>
                                                @if (request()->user()->hasRole('ksk_hr'))
                                                    <li><a href="/Status/KSK/0"><i class="fa fa-circle-o"></i> KSK Management</a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </li>
                                @endif
                                <li><a href="/Leader"><i class="fa fa-user"></i> Direct Leader</a></li>
                                <li><a href="{{ route('structure.index') }}"><i class="fa fa-sitemap"></i> Organization
                                        Structure</a></li>
                                <!-- <li class="treeview<?php        //if (isset($menu) && ($menu == 'recruitment')) echo ' active';?>">
                                                                    <a href="#">
                                                                        <i class="fa fa-user-plus text-yellow"></i> <span>Recruitment</span>
                                                                        <span class="pull-right-container">
                                                                            <i class="fa fa-angle-left pull-right"></i>
                                                                        </span>
                                                                    </a>
                                                                    <ul class="treeview-menu">
                                                                        <li><a href="/FPPK"><i class="fa fa-file-text-o"></i> FPPK Application</a></li>
                                                                    </ul>
                                                                </li>
                                                                <li><a href="{{ route('renewal.index') }}"><i class="fa fa-refresh text-green"></i> Contract Renewal</a></li>
                                                                @if (request()->user()->hasRole('candidate'))
                                                                    <li><a href="/candidate"><i class="fa fa-user-circle text-aqua"></i> <span>Candidate Portal</span></a></li>
                                                                @endif -->
                                <?php        $hcmis = 0;?>
                                @if($hcmis == 1)
                                    <li><a href="/hcmis"><i class="fa fa-server"></i> HCMIS Integration Data</a></li>
                                @endif
                            @endif
                        </ul>
                    </li>
            @endif

            <!-- DEVELOPMENT & TRAINING -->
            @if (request()->user()->hasRole('training') || request()->user()->hasRole('competence') || request()->user()->hasRole('hr_access') || request()->user()->hasRole('performance') || request()->user()->hasRole('leader'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'training' || $menu == 'training_activity' || $menu == 'training_tools' || $menu == 'training_actual' || $menu == 'competence' || $menu == 'master' || $menu == 'aktual' || $menu == 'performanceAll' || $menu == 'performanceHR'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-graduation-cap text-green"></i> <span>Development</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (request()->user()->hasRole('training'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'training' || $menu == 'training_activity' || $menu == 'training_tools' || $menu == 'training_actual'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-sliders"></i> <span>Training Management</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/TrainingGraph/Periode/0"><i class="fa fa-bar-chart text-green"></i> Training
                                                        Overview</a></li>
                                                <li><a href="/Training/Overview/0"><i class="fa fa-calendar-check-o text-green"></i> Annual
                                                        Schedule</a></li>
                                                <li class="treeview<?php        if (isset($menu) && $menu == 'training_tools' || $menu == 'training')
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-list-alt text-green"></i> <span>Master List</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Training/List/0/0"><i class="fa fa-circle-o"></i> Training Courses</a>
                                                        </li>
                                                        <li><a href="/Training/Examination"><i class="fa fa-circle-o"></i> Question Bank</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="treeview<?php        if (isset($menu) && ($menu == 'training_activity' || $menu == 'training_actual'))
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-tasks text-green"></i> <span>Training Activity</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Training/Periode/0"><i class="fa fa-circle-o"></i> Plan Schedule</a>
                                                        </li>
                                                        <li class="treeview<?php        if (isset($menu) && $menu == 'training_actual')
                                echo ' active';?>">
                                                            <a href="#">
                                                                <i class="fa fa-circle-o"></i> <span>Actual Activity</span>
                                                                <span class="pull-right-container">
                                                                    <i class="fa fa-angle-left pull-right"></i>
                                                                </span>
                                                            </a>
                                                            <ul class="treeview-menu">
                                                                <li><a href="/Training/Actuals/0/0"><i class="fa fa-group"></i> Group
                                                                        Class</a></li>
                                                                <li><a href="/Training/Personal"><i class="fa fa-user"></i> Employee
                                                                        History</a></li>
                                                            </ul>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                            @endif
                            @if(request()->user()->hasRole('hr_access') || request()->user()->hasRole('performance') || request()->user()->hasRole('info_employee'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'performanceHR' || $menu == 'performanceHR'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-line-chart"></i> <span>Performance</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/Performance/0"><i class="fa fa-line-chart text-green"></i> Employee
                                                        Performance</a></li>
                                                @if(request()->user()->hasRole('performance') || request()->user()->hasRole('info_employee'))
                                                    <li><a href="/Performance/0/0/0"><i class="fa fa-edit text-green"></i> Performance Rank</a>
                                                    </li>
                                                @endif
                                                @if(request()->user()->hasRole('hr_access'))
                                                    <li><a href="/Performances/0/0/0"><i class="fa fa-area-chart text-green"></i> Performance
                                                            Summary</a></li>
                                                @endif
                                            </ul>
                                        </li>
                            @endif
                            @if(request()->user()->hasRole('competence') || request()->user()->hasRole('hr_access'))
                                <li
                                    class="treeview<?php if (isset($menu) && ($menu == 'SkillMatric' || $menu == 'SkillMatric1' || $menu == 'SkillMatric2')) echo ' active';?>">
                                    <a href="#">
                                        <i class="fa fa-bar-chart"></i> <span>Skill Competence</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <?php if (request()->user()->hasRole('competence')) {?>
                                        <li
                                            class="treeview<?php            if (isset($menu) && ($menu == 'SkillMatric' || $menu == 'SkillMatric0')) echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-cog"></i> <span>Set Up Skill</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/SkillMatric/Type"><i class="fa fa-circle-o"></i> Skill Type</a></li>
                                                <li><a href="/SkillMatric/Group"><i class="fa fa-circle-o"></i> Skill Group</a></li>
                                                <li><a href="/SkillMatric"><i class="fa fa-circle-o"></i> Competence List</a></li>
                                                <li><a href="/SkillMatric/Group/0"><i class="fa fa-circle-o"></i> Competence
                                                        Group</a></li>
                                            </ul>
                                        </li>
                                        <?php }?>
                                        <li class="treeview<?phpif (isset($menu) && ($menu == 'SkillMatric1'))echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-edit"></i> <span>Update Competence</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/SkillMatric/MainJob"><i class="fa fa-circle-o"></i> Employee List</a>
                                                </li>
                                                <li><a href="/SkillMatric/CompetenceEmployee/0"><i class="fa fa-circle-o"></i>
                                                        Competence Employee</a></li>
                                                <li><a href="/SkillMatric/EmployeeCompetence/0/0"><i class="fa fa-circle-o"></i>
                                                        Employee Competence</a></li>
                                            </ul>
                                        </li>
                                        <li class="treeview<?php if (isset($menu) && ($menu == 'SkillMatric2'))echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-bar-chart"></i> <span>Report Competence</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/SkillMatric/Groups"><i class="fa fa-circle-o"></i> Group
                                                        Competence</a></li>
                                                <li><a href="/SkillMatric/Employees/0"><i class="fa fa-circle-o"></i> Employee
                                                        Matric</a></li>
                                                <li><a href="/SkillMatric/FelxibilityChart/0/0"><i class="fa fa-circle-o"></i>
                                                        Flexibility Chart</a></li>
                                                <li><a href="/SkillMatric/LineEmployee/0/0"><i class="fa fa-circle-o"></i> Matric Mc
                                                        Line</a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            <!-- IMPROVEMENT -->
                            @if (request()->user()->hasRole('improvement'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'improvement' || $menu == 'qcc' || $menu == 'gqcc'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-star"></i> <span>Improvement & QCC</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li class="treeview<?php        if (isset($menu) && ($menu == 'qcc'))
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-gears text-green"></i> <span>QCC</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/qcc_team"><i class="fa fa-users"></i> Teams</a></li>
                                                        <li><a href="/qcc_schedule"><i class="fa fa-calendar"></i> Schedule</a></li>
                                                        <li><a href="/qcc_activity"><i class="fa fa-tasks"></i> Activity</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                            @endif

                        </ul>
                    </li>
            @endif

            <!-- TIME MANAGEMENT SHEET (TMS) -->
            @if(request()->user()->hasRole('tms') || request()->user()->hasRole('admin_department') || request()->user()->hasRole('admin_calendar') || request()->user()->hasRole('permit') || request()->user()->hasRole('permit_approve') || request()->user()->hasRole('permit_personalia') || request()->user()->hasRole('permit_scurity'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'tms' || $menu == 'calendar' || $menu == 'tms_check'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-clock-o text-purple"></i> <span>Attendance</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (request()->user()->hasRole('admin_calendar') || request()->user()->hasRole('tms'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'calendar'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-calendar"></i> <span>Calendar & Shift</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                @if (request()->user()->hasRole('admin_calendar'))
                                                                <li class="<?php            if (isset($menu) && $menu == 'calendar')
                                                    echo ' active';?>">
                                                                    <a href="/Admin/Freeday"><i class="fa fa-calendar-o text-purple"></i> Calendars</a>
                                                                </li>
                                                @endif
                                                @if (request()->user()->hasRole('tms'))
                                                    <li><a href="/TMS/Group"><i class="fa fa-clock-o text-purple"></i> Shift List</a></li>
                                                @endif
                                            </ul>
                                        </li>
                            @endif
                            @if (request()->user()->hasRole('tms') || request()->user()->hasRole('admin_department'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'tms' || $menu == 'tms_check'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-sliders"></i> <span>Shift Management</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/TMS/Draft/0/0"><i class="fa fa-file-text-o text-purple"></i> Draft Group</a>
                                                </li>
                                                <li><a href="/TMS/Plan/0/0/100/0"><i class="fa fa-calendar-check-o text-purple"></i> Working
                                                        Schedule</a></li>
                                                <li><a href="/DailyPresence/0/0/100"><i class="fa fa-edit text-purple"></i> Daily
                                                        Presence</a></li>
                                                @if (request()->user()->hasRole('hr_access'))
                                                                <li class="treeview<?php            if (isset($menu) && ($menu == 'tms_check'))
                                                    echo ' active';?>">
                                                                    <a href="#">
                                                                        <i class="fa fa-check-circle-o text-purple"></i> <span>Check Absen</span>
                                                                        <span class="pull-right-container">
                                                                            <i class="fa fa-angle-left pull-right"></i>
                                                                        </span>
                                                                    </a>
                                                                    <ul class="treeview-menu">
                                                                        <li><a href="/MonthlyCheck/0"><i class="fa fa-circle-o"></i> Work Schedule</a></li>
                                                                        <li><a href="/AbsensiRateDetail/0"><i class="fa fa-circle-o"></i> Absency Rate</a>
                                                                        </li>
                                                                        <li><a href="/FailedFinger/0/0"><i class="fa fa-circle-o"></i> Failed Finger</a>
                                                                        </li>
                                                                    </ul>
                                                                </li>
                                                @endif
                                                <li><a href="/AbsensiRate/0/0"><i class="fa fa-line-chart text-purple"></i> Absensi Rate</a>
                                                </li>
                                            </ul>
                                        </li>
                            @endif
                            <!-- E-WORKFLOWS & APPROVALS -->
                            @if (request()->user()->hasRole('permit') || request()->user()->hasRole('permit_approve') || request()->user()->hasRole('permit_personalia') || request()->user()->hasRole('permit_scurity'))
                                        <li class="treeview<?php        if (isset($menu) && $menu == 'permit')
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-ticket"></i> <span>e-Permit</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <?php        if (request()->user()->hasRole('permit')) {?>
                                                <li><a href="/Permit"><i class="fa fa-pencil text-purple"></i> Apply Permit</a></li>
                                                <li><a href="/Permit/Approves/0/0"><i class="fa fa-check-square-o text-green"></i>
                                                        Approval</a></li>
                                                <?php        }?>
                                                <?php        if (request()->user()->hasRole('permit_personalia')) {?>
                                                <li><a href="/Permit/Personalia/0/0"><i class="fa fa-gavel text-yellow"></i> Legalize HR</a>
                                                </li>
                                                <?php        }?>
                                                <?php        if (request()->user()->hasRole('permit_scurity')) {?>
                                                <li><a href="/Permit/Scurities/0/0"><i class="fa fa-shield text-red"></i> Security
                                                        Verification</a>
                                                </li>
                                                <?php        }?>
                                                <?php        if (request()->user()->hasRole('permit')) {?>
                                                <li><a href="/Permit/Report/0/0"><i class="fa fa-file-text-o text-aqua"></i> Report
                                                        Permit</a></li>
                                                <?php        }?>
                                            </ul>
                                        </li>
                            @endif

                            @if (request()->user()->hasRole('leave') || request()->user()->hasRole('leave_approve') || request()->user()->hasRole('leave_legalize') || request()->user()->hasRole('legal'))
                                        <li class="treeview<?php        if (isset($menu) && $menu == 'leave')
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-calendar-minus-o"></i> <span>e-Leave</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li class="treeview<?php        if (isset($submenu) && $submenu == 'apply')
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-pencil"></i> <span>Apply Leave</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Leave"><i class="fa fa-calendar text-aqua"></i> Annual Leave</a></li>
                                                        <li><a href="/Leave/Special"><i class="fa fa-star text-yellow"></i> Special
                                                                Leave</a></li>
                                                        <li><a href="/Leave/SKD/0/0"><i class="fa fa-user-md text-red"></i> Doctor / SKD</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <?php        if (request()->user()->hasRole('leave_approve')) {?>
                                                <li><a href="/Leave/Approves/0/0"><i class="fa fa-check-square-o text-green"></i> Approve
                                                        Leave</a>
                                                </li>
                                                <?php        }?>
                                                <?php        if (request()->user()->hasRole('leave_legalize')) {?>
                                                <li class="treeview<?php            if (isset($submenu) && $submenu == 'legalized')
                                    echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-gavel"></i> <span>Legalize</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Leave/Legalizes/0/0/Annual"><i class="fa fa-circle-o"></i> Annual</a>
                                                        </li>
                                                        <li><a href="/Leave/Legalizes/0/0/Special"><i class="fa fa-circle-o"></i>
                                                                Special</a></li>
                                                        <li><a href="/Leave/Legalizes/0/0/Docter"><i class="fa fa-circle-o"></i> Doctor /
                                                                SKD</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <?php        }?>
                                                <li class="treeview<?php        if (isset($submenu) && $submenu == 'report')
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-bar-chart"></i> <span>Report Leave</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Leave/Reports/0/0/Annual"><i class="fa fa-circle-o"></i> Annual
                                                                Report</a>
                                                        </li>
                                                        <li><a href="/Leave/Reports/0/0/Special"><i class="fa fa-circle-o"></i> Special
                                                                Report</a>
                                                        </li>
                                                        <li><a href="/Leave/Reports/0/0/Docter"><i class="fa fa-circle-o"></i> Doctor
                                                                Report</a>
                                                        </li>
                                                        <li><a href="/Leave/Reports/0/0/SKD"><i class="fa fa-circle-o"></i> SKD Report</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                            @endif
                        </ul>
                    </li>
            @endif

            @if(request()->user()->hasRole('spl_create') || request()->user()->hasRole('admin_department') || request()->user()->hasRole('spl_approval') || request()->user()->hasRole('spl_verification'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'overtime' || $menu == 'assigment'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-pencil-square-o text-warning"></i> <span>SPL & Assignment</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview<?php    if (isset($menu) && ($menu == 'overtime'))
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-clock-o"></i> <span>Overtime (SPL)</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    @if (request()->user()->hasRole('spl_create'))
                                        <li><a href="/Admin/Overtime"><i class="fa fa-file-o text-warning"></i> Create Form</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('admin_department'))
                                        <li><a href="/Admin/Overtime/Depts/0"><i class="fa fa-list text-warning"></i> Realisation
                                                SPL</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('spl_approval'))
                                        <li><a href="/ApprovalSPL"><i class="fa fa-check-square-o text-warning"></i> Approval
                                                SPL</a></li>
                                        <li><a href="/LegalizeSPL"><i class="fa fa-gavel text-warning"></i> Legalize SPL</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('spl_verification'))
                                        <li><a href="/Admin/Overtime/Verifications/0"><i class="fa fa-shield text-warning"></i>
                                                Verification SPL</a></li>
                                    @endif
                                </ul>
                            </li>
                            <li class="treeview<?php    if (isset($menu) && $menu == 'assigment')
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-briefcase"></i> <span>Assignment Form</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    @if (request()->user()->hasRole('spl_create'))
                                        <li><a href="/Assigment"><i class="fa fa-file-o text-warning"></i> Create Form</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('spl_approval'))
                                        <li><a href="/Assigment/Approvals/0"><i class="fa fa-check-square-o text-warning"></i>
                                                Approval</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('admin_department'))
                                        <li><a href="/Assigment/Realisations/0"><i class="fa fa-list-alt text-warning"></i>
                                                Realisation</a></li>
                                    @endif
                                    @if (request()->user()->hasRole('spl_verification'))
                                        <li><a href="/Assigment/Verifications/0"><i class="fa fa-shield text-warning"></i>
                                                Verification</a></li>
                                    @endif
                                </ul>
                            </li>
                        </ul>
                    </li>
            @endif
            <!-- PAYROLL -->
            @if(request()->user()->hasRole('payroll') || request()->user()->hasRole('accounting'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'overtime' || $menu == 'overtime_summary' || $menu == 'overtime_tax' || $menu == 'overtime_recap' || $menu == 'capture_assignment' || $menu == 'summary_assignment' || $menu == 'recap_journal' || $menu == 'recap_assignment' || $menu == 'payroll_calculation'||$menu == 'payroll_recap'||$menu == 'payroll_other'||$menu == 'meal'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-money text-warning"></i> <span>Payroll</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(request()->user()->hasRole('hr_access') || request()->user()->hasRole('allowance'))
                                    <?php if (request()->user()->hasRole('hr_access')) {?>
                                    <li class="treeview<?php if (isset($menu) && ($menu == 'payroll_calculation'))echo ' active';?>">
                                        <a href="#">
                                            <i class="fa fa-circle fa-calculator"></i> <span> Calculation</span>
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="/Contract/0/0/SAI"><i class="fa fa-circle-o"></i> <span>SAI</span></a></li>
                                            <li><a href="/Contract/0/0/MAGANG"><i class="fa fa-circle-o"></i> <span>Magang</span></a>
                                            </li>
                                            <li><a href="/Contract/0/0/MANAGEMENT"><i class="fa fa-circle-o"></i>
                                                    <span>Management</span></a></li>
                                            <li><a href="/ContractOther/PSAB"><i class="fa fa-circle-o"></i> <span>PSAB</span></a></li>
                                            <li><a href="/ContractOther/PKL"><i class="fa fa-circle-o"></i> <span>PKL</span></a></li>
                                        </ul>
                                    </li>
                                    <li class="treeview<?php if (isset($menu) && ($menu == 'payroll_recap')) echo ' active';?>">
                                        <a href="#">
                                            <i class="fa fa-list-alt"></i> <span> Recap</span>
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="/Recap/0/SAI"><i class="fa fa-circle-o"></i> <span>SAI</span></a></li>
                                            <li><a href="/Recap/0/MAGANG"><i class="fa fa-circle-o"></i> <span>Magang</span></a></li>
                                            <li><a href="/Recap/0/MANAGEMENT"><i class="fa fa-circle-o"></i> <span>Management</span></a>
                                            </li>
                                            <li><a href="/Recap/0/PSAB"><i class="fa fa-circle-o"></i> <span>PSAB</span></a></li>
                                            <li><a href="/Recap/0/PKL"><i class="fa fa-circle-o"></i> <span>PKL</span></a></li>
                                            <li><a href="/Recap/0/Compensation"><i class="fa fa-circle-o"></i>
                                                    <span>Compensation</span></a></li>
                                        </ul>
                                    </li>
                                    <li><a href="/Compensation/0"><i class="fa fa-credit-card"></i> <span>Other Payments</span></a></li>
                                    <li><a href="/loans"><i class="fa fa-dollar"></i> <span>Employee Loans</span></a></li>
                                    <?php        }?>
                                    <?php        if (request()->user()->hasRole('allowance')) {?>
                                    <li class="treeview<?php if (isset($menu) && ($menu == 'meal' || $menu == 'transport'))
                                echo ' active';?>">
                                        <a href="#">
                                            <i class="fa fa-gift"></i> <span> Allowance</span>
                                            <span class="pull-right-container">
                                                <i class="fa fa-angle-left pull-right"></i>
                                            </span>
                                        </a>
                                        <ul class="treeview-menu">
                                            <li><a href="/Meals/{{$periode}}/0"><i class="fa fa-circle-o"></i> Personal Entry</a></li>
                                            <li><a href="/MealsMonth/{{$periode}}/0"><i class="fa fa-circle-o"></i> Entry by Month</a>
                                            </li>
                                            <li><a href="/MealsVendorStay/{{$periode}}/0"><i class="fa fa-circle-o"></i> Vendor Stay</a>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php        }?>
                            @endif
                            @if(request()->user()->hasRole('payroll'))
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'overtime_summary' || $menu == 'overtime_tax' || $menu == 'overtime_recap'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-calculator"></i> <span>Overtime</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/payroll/summary_overtime/0/0"><i
                                                            class="fa fa-pencil-square-o text-warning"></i> Capture</a></li>
                                                <li class="treeview<?php        if (isset($menu) && ($menu == 'overtime_tax'))
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-list text-warning"></i> <span>Summary</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/payroll/tax_overtime/0/0"><i class="fa fa-circle-o"></i> SAI &
                                                                Magang</a></li>
                                                        <li><a href="/payroll/tax_overtime_psab/0/0"><i class="fa fa-circle-o"></i> PSAB</a>
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li class="treeview<?php        if (isset($menu) && ($menu == 'overtime_recap'))
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-list-alt text-warning"></i> <span>Recap</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/payroll/recap_overtime/0/0"><i class="fa fa-circle-o"></i> SAI &
                                                                Magang</a></li>
                                                        <li><a href="/payroll/recap_overtime_psab/0/0"><i class="fa fa-circle-o"></i>
                                                                PSAB</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'capture_assignment' || $menu == 'summary_assignment' || $menu == 'recap_assignment'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-briefcase"></i> <span>Assigment</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li><a href="/payroll/capture_assignment/0/0"><i
                                                            class="fa fa-pencil-square-o text-warning"></i> Capture</a></li>
                                                <li><a href="/payroll/summary_assignment/0/0"><i class="fa fa-list text-warning"></i>
                                                        Summary</a></li>
                                                <li><a href="/payroll/recap_assignment"><i class="fa fa-list-alt text-warning"></i>
                                                        Recap</a></li>
                                            </ul>
                                        </li>
                            @endif
                            @if(request()->user()->hasRole('accounting'))
                                <li><a href="/payroll/recap_journal/0"><i class="fa fa-list-alt"></i> Recap Journal</a></li>
                            @endif
                        </ul>
                    </li>
            @endif

            <!-- GENERAL MEMO -->
            @if (request()->user()->hasRole('memo'))
                    <li class="treeview<?php    if (isset($menu) && $menu == 'memo')
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-envelope text-blue"></i> <span>General Memo</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/GeneralMemo/1"><i class="fa fa-circle-o"></i> Overtime Produksi</a></li>
                            <li><a href="/GeneralMemo/2"><i class="fa fa-circle-o"></i> Unlock Overduedate</a></li>
                        </ul>
                    </li>
            @endif

            <!-- POLICIES & CONTROLS -->
            @if(request()->user()->hasRole('legal') || request()->user()->hasRole('policy') || request()->user()->hasRole('training'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'document' || $menu == 'elibrary'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-book text-blue"></i> <span>Document Control</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if(request()->user()->hasRole('legal'))
                                <li><a href="/PolicyControl"><i class="fa fa-circle-o"></i> <span>Legal Documents</span></a>
                                </li>
                            @endif
                            @if(request()->user()->hasRole('policy'))
                                <li><a href="/Policy"><i class="fa fa-circle-o"></i> <span>e-Policy</span></a></li>
                            @endif
                            @if(request()->user()->hasRole('training'))
                                <li><a href="/Training/Document/0"><i class="fa fa-circle-o"></i> e-Library</a></li>
                            @endif
                        </ul>
                    </li>
            @endif

            <!-- INFORMATION & REPORTS SECTION -->
            <li class="header">INFORMATION & REPORTS</li>
            @if (request()->user()->hasRole('info_employee') || request()->user()->hasRole('info_shift'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'department' || $menu == 'employees' || $menu == 'ksk' || $menu == 'performance'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-user-circle text-info"></i> <span>Employee</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (request()->user()->hasRole('info_employee'))
                                <li><a href="/Employees/0"><i class="fa fa-circle-o"></i> Employee Profile</a></li>
                            @endif
                            @if (request()->user()->hasRole('ksk'))
                                <li><a href="/Employees/KSK/0"><i class="fa fa-circle-o"></i> KSK</a> </li>
                            @endif
                        </ul>
                    </li>
            @endif

            @if (request()->user()->hasRole('report_overtime') || request()->user()->hasRole('info_overtime_dept') || request()->user()->hasRole('info_overtime'))
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'overtimes' || $menu == 'report_assigment'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-book text-warning"></i> <span>Report Overtime</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            @if (request()->user()->hasRole('info_overtime_dept'))
                                <li><a href="/Overtimes/Assigment/0"><i class="fa fa-circle-o"></i> Assignment Report</a></li>
                            @endif
                        </ul>
                    </li>
            @endif

        </ul>
    </section>
</aside>