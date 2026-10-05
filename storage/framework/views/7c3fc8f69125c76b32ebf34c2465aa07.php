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

        <?php $sekarang = date('Y-m-d H:i:s');?>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <li class="header">MAIN NAVIGATION</li>

            <!-- CANDIDATE ROLE -->
            <?php if (request()->user()->hasRole('candidate')) {?>
            <li><a href="/candidate"><i class="fa fa-user-circle text-aqua"></i> <span>Candidate Portal</span></a></li>
            <?php }?>

            <!-- DASHBOARD -->
            <?php if(request()->user()->hasRole('dashboard')): ?>
                    <li class="<?php    if (isset($menu) && $menu == 'dashboard')
                echo 'active'; ?>">
                        <a href="/Setup"><i class="fa fa-dashboard text-light-blue"></i> <span>Dashboard</span></a>
                    </li>
            <?php endif; ?>

            <!-- USER MANAGEMENT & ACCESS -->
            <?php if (request()->user()->hasRole('add_user') || request()->user()->hasRole('role') || request()->user()->hasRole('user_role')) {?>
            <li class="treeview<?php    if (isset($menu) && ($menu == 'user_management'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-shield text-red"></i> <span>User Management</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php if(request()->user()->hasRole('add_user')): ?>
                        <li><a href="/user-management"><i class="fa fa-user text-red"></i> Users List</a></li>
                    <?php endif; ?>
                    <?php if(request()->user()->hasRole('role')): ?>
                        <li><a href="/role-management"><i class="fa fa-key text-yellow"></i> Roles Management</a></li>
                        <li><a href="/userrole-management"><i class="fa fa-cogs text-aqua"></i> User Role Assignment</a>
                        </li>
                        <li><a href="/Staff"><i class="fa fa-user-md text-green"></i> Staff Admin</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php }?>

            <!-- MASTER DATA & HCMIS -->
            <?php if (request()->user()->hasRole('admin_shift') || request()->user()->hasRole('admin_employee') || request()->user()->hasRole('admin_calendar')) {?>
            <li
                class="treeview<?php    if (isset($menu) && ($menu == 'shift' || $menu == 'employee' || $menu == 'leader' || $menu == 'calendar'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-database text-aqua"></i> <span>Master Data & HCMIS</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <?php    if (request()->user()->hasRole('admin_employee') || request()->user()->hasRole('contract') || request()->user()->hasRole('staff')) {?>
                    <li class="treeview<?php        if (isset($menu) && $menu == 'employee')
            echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-id-card-o text-aqua"></i> <span>Employee Management</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/Admin/Employee"><i class="fa fa-users text-aqua"></i> Employee List</a></li>
                            <li><a href="/Admin/Department/0/0"><i class="fa fa-pie-chart text-yellow"></i> Recap
                                    Database</a></li>

                            <?php        if (request()->user()->hasRole('contract')) {?>
                            <li
                                class="treeview<?php            if (isset($menu) && $menu == 'employee' && isset($submenu) && $submenu == 'contract')
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-file-text-o text-green"></i> <span>Contract</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/Status/Active"><i class="fa fa-check-circle text-green"></i>
                                            Registered Active</a></li>
                                    <li><a href="/Status/Draft"><i class="fa fa-times-circle text-red"></i> Non
                                            Active</a></li>
                                    <?php            if (request()->user()->hasRole('ksk_hr')) {?>
                                    <li><a href="/Status/KSK/0"><i class="fa fa-star text-yellow"></i> KSK Special</a>
                                    </li>
                                    <?php            }?>
                                </ul>
                            </li>
                            <?php        }?>
                        </ul>
                    </li>
                    <li><a href="/Leader"><i class="fa fa-sitemap text-teal"></i> Direct Leader</a></li>
                    <li><a href="/hcmis"><i class="fa fa-server text-purple"></i> HCMIS Integration Data</a></li>
                    <?php    }?>
                </ul>
            </li>
            <?php }?>

            <?php if(request()->user()->hasRole('hr_access')): ?>
                <li><a href="/Setup"><i class="fa fa-gears text-red"></i> <span>Setup Utility</span></a></li>
            <?php endif; ?>

            <!-- DEVELOPMENT & TRAINING -->
            <?php if(request()->user()->hasRole('training') || request()->user()->hasRole('competence') || request()->user()->hasRole('hr_access') || request()->user()->hasRole('performance') || request()->user()->hasRole('leader')): ?>
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'training' || $menu == 'training_activity' || $menu == 'training_tools' || $menu == 'training_actual' || $menu == 'competence' || $menu == 'master' || $menu == 'aktual' || $menu == 'performanceAll' || $menu == 'elibrary'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-graduation-cap text-green"></i> <span>Development & Skill</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/Training/Overview/0"><i class="fa fa-calendar-check-o text-green"></i> Annual Training
                                    Schedule</a></li>
                            <li><a href="/TrainingGraph/Periode/0"><i class="fa fa-bar-chart text-yellow"></i> Training
                                    Analytics Graph</a></li>
                            <li><a href="/Training/Document/0"><i class="fa fa-book text-aqua"></i> e-Library (Documents)</a>
                            </li>

                            <?php if(request()->user()->hasRole('training')): ?>
                                        <li
                                            class="treeview<?php        if (isset($menu) && ($menu == 'training' || $menu == 'training_activity' || $menu == 'training_tools' || $menu == 'training_actual'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-sliders text-teal"></i> <span>Training Management</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <li
                                                    class="treeview<?php        if (isset($menu) && $menu == 'training_tools' || $menu == 'training')
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-list-alt"></i> <span>Master List</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Training/List/0/0"><i class="fa fa-list"></i> Training Courses</a>
                                                        </li>
                                                        <li><a href="/Training/Examination"><i class="fa fa-check-square-o"></i> Question
                                                                Bank & Test</a></li>
                                                    </ul>
                                                </li>
                                                <li
                                                    class="treeview<?php        if (isset($menu) && ($menu == 'training_activity' || $menu == 'training_actual'))
                                echo ' active';?>">
                                                    <a href="#">
                                                        <i class="fa fa-tasks"></i> <span>Training Activity</span>
                                                        <span class="pull-right-container">
                                                            <i class="fa fa-angle-left pull-right"></i>
                                                        </span>
                                                    </a>
                                                    <ul class="treeview-menu">
                                                        <li><a href="/Training/Periode/0"><i class="fa fa-clock-o"></i> Plan Schedule</a>
                                                        </li>
                                                        <li
                                                            class="treeview<?php        if (isset($menu) && $menu == 'training_actual')
                                echo ' active';?>">
                                                            <a href="#">
                                                                <i class="fa fa-check-circle"></i> <span>Actual Activity</span>
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
                            <?php endif; ?>

                            <?php    if (request()->user()->hasRole('performance') || request()->user()->hasRole('info_employee')) {?>
                            <li><a href="/Performance/0/0/0"><i class="fa fa-line-chart text-orange"></i> Performance
                                    Ranking</a></li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('hr_access')) {?>
                            <li><a href="/Performances/0/0/0"><i class="fa fa-area-chart text-purple"></i> Performance
                                    Summary</a></li>
                            <?php    }?>

                            <?php    if (request()->user()->hasRole('competence') || request()->user()->hasRole('hr_access')) {?>
                            <li
                                class="treeview<?php        if (isset($menu) && ($menu == 'SkillMatric' || $menu == 'SkillMatric1' || $menu == 'SkillMatric2'))
                    echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-trophy text-yellow"></i> <span>Competence & Skill</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php        if (request()->user()->hasRole('competence')) {?>
                                    <li
                                        class="treeview<?php            if (isset($menu) && ($menu == 'SkillMatric' || $menu == 'SkillMatric0'))
                        echo ' active';?>">
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
                                    <?php        }?>
                                    <li class="treeview<?php        if (isset($menu) && ($menu == 'SkillMatric1'))
                    echo ' active';?>">
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
                                    <li class="treeview<?php        if (isset($menu) && ($menu == 'SkillMatric2'))
                    echo ' active';?>">
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
                            <?php    }?>
                        </ul>
                    </li>
            <?php endif; ?>

            <!-- PERSONALIA & RECRUITMENT -->
            <?php if (request()->user()->hasRole('admin_department')) {?>
            <li class="treeview<?php    if (isset($menu) && ($menu == 'recruitment'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-user-plus text-yellow"></i> <span>Personalia & HR</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li><a href="<?php echo e(route('structure.index')); ?>"><i class="fa fa-sitemap text-aqua"></i> Organization
                            Structure</a></li>
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'recruitment'))
        echo ' active';?>">
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
                    <li><a href="<?php echo e(route('renewal.index')); ?>"><i class="fa fa-refresh text-green"></i> Contract
                            Renewal</a></li>
                </ul>
            </li>
            <?php }?>

            <!-- E-WORKFLOWS & APPROVALS -->
            <?php if(request()->user()->hasRole('permit') || request()->user()->hasRole('permit_approve') || request()->user()->hasRole('permit_personalia') || request()->user()->hasRole('permit_scurity')): ?>
                    <li class="treeview<?php    if (isset($menu) && $menu == 'permit')
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-ticket text-purple"></i> <span>e-Permit</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php    if (request()->user()->hasRole('permit')) {?>
                            <li><a href="/Permit"><i class="fa fa-pencil text-purple"></i> Apply Permit</a></li>
                            <li><a href="/Permit/Approves/0/0"><i class="fa fa-check-square-o text-green"></i> Approval</a></li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('permit_personalia')) {?>
                            <li><a href="/Permit/Personalia/0/0"><i class="fa fa-gavel text-yellow"></i> Legalize HR</a></li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('permit_scurity')) {?>
                            <li><a href="/Permit/Scurities/0/0"><i class="fa fa-shield text-red"></i> Security Verification</a>
                            </li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('permit')) {?>
                            <li><a href="/Permit/Report/0/0"><i class="fa fa-file-text-o text-aqua"></i> Report Permit</a></li>
                            <?php    }?>
                        </ul>
                    </li>
            <?php endif; ?>

            <?php if(request()->user()->hasRole('leave') || request()->user()->hasRole('leave_approve') || request()->user()->hasRole('leave_legalize') || request()->user()->hasRole('legal')): ?>
                    <li class="treeview<?php    if (isset($menu) && $menu == 'leave')
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-calendar-minus-o text-purple"></i> <span>e-Leave</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="treeview<?php    if (isset($submenu) && $submenu == 'apply')
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-pencil"></i> <span>Apply Leave</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/Leave"><i class="fa fa-calendar text-aqua"></i> Annual Leave</a></li>
                                    <li><a href="/Leave/Special"><i class="fa fa-star text-yellow"></i> Special Leave</a></li>
                                    <li><a href="/Leave/SKD/0/0"><i class="fa fa-user-md text-red"></i> Doctor / SKD</a></li>
                                </ul>
                            </li>
                            <?php    if (request()->user()->hasRole('leave_approve')) {?>
                            <li><a href="/Leave/Approves/0/0"><i class="fa fa-check-square-o text-green"></i> Approve Leave</a>
                            </li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('leave_legalize')) {?>
                            <li class="treeview<?php        if (isset($submenu) && $submenu == 'legalized')
                    echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-gavel"></i> <span>Legalize</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/Leave/Legalizes/0/0/Annual"><i class="fa fa-circle-o"></i> Annual</a></li>
                                    <li><a href="/Leave/Legalizes/0/0/Special"><i class="fa fa-circle-o"></i> Special</a></li>
                                    <li><a href="/Leave/Legalizes/0/0/Docter"><i class="fa fa-circle-o"></i> Doctor / SKD</a>
                                    </li>
                                </ul>
                            </li>
                            <?php    }?>
                            <li class="treeview<?php    if (isset($submenu) && $submenu == 'report')
                echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-bar-chart"></i> <span>Report Leave</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <li><a href="/Leave/Reports/0/0/Annual"><i class="fa fa-circle-o"></i> Annual Report</a>
                                    </li>
                                    <li><a href="/Leave/Reports/0/0/Special"><i class="fa fa-circle-o"></i> Special Report</a>
                                    </li>
                                    <li><a href="/Leave/Reports/0/0/Docter"><i class="fa fa-circle-o"></i> Doctor Report</a>
                                    </li>
                                    <li><a href="/Leave/Reports/0/0/SKD"><i class="fa fa-circle-o"></i> SKD Report</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
            <?php endif; ?>

            <?php if(request()->user()->hasRole('spl_create') || request()->user()->hasRole('admin_department') || request()->user()->hasRole('spl_approval') || request()->user()->hasRole('spl_verification')): ?>
                        <li
                            class="treeview<?php    if (isset($menu) && ($menu == 'overtime' || $menu == 'assigment'))
                    echo ' active';?>">
                            <a href="#">
                                <i class="fa fa-pencil-square-o text-purple"></i> <span>SPL & Assignment Form</span>
                                <span class="pull-right-container">
                                    <i class="fa fa-angle-left pull-right"></i>
                                </span>
                            </a>
                            <ul class="treeview-menu">
                                <li class="treeview<?php    if (isset($menu) && ($menu == 'overtime'))
                    echo ' active';?>">
                                    <a href="#">
                                        <i class="fa fa-clock-o text-orange"></i> <span>Overtime (SPL)</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <?php    if (request()->user()->hasRole('spl_create')) {?>
                                        <li><a href="/Admin/Overtime"><i class="fa fa-file-o"></i> Form Overtime</a></li>
                                        <?php    }
                if (request()->user()->hasRole('admin_department')) {?>
                                        <li><a href="/Admin/Overtime/Depts/0"><i class="fa fa-list"></i> Realisation SPL</a></li>
                                        <?php    }
                if (request()->user()->hasRole('spl_approval')) {?>
                                        <li><a href="/ApprovalSPL"><i class="fa fa-check-square-o text-green"></i> Approval SPL</a>
                                        </li>
                                        <li><a href="/LegalizeSPL"><i class="fa fa-gavel text-yellow"></i> Legalize SPL</a></li>
                                        <?php    }
                if (request()->user()->hasRole('spl_verification')) {?>
                                        <li><a href="/Admin/Overtime/Verifications/0"><i class="fa fa-shield text-red"></i>
                                                Verification SPL</a></li>
                                        <?php    }?>
                                    </ul>
                                </li>
                                <li class="treeview<?php    if (isset($menu) && $menu == 'assigment')
                    echo ' active';?>">
                                    <a href="#">
                                        <i class="fa fa-briefcase text-aqua"></i> <span>Assignment Form</span>
                                        <span class="pull-right-container">
                                            <i class="fa fa-angle-left pull-right"></i>
                                        </span>
                                    </a>
                                    <ul class="treeview-menu">
                                        <?php    if (request()->user()->hasRole('spl_create')) {?>
                                        <li><a href="/Assigment"><i class="fa fa-file-o"></i> Create Form</a></li>
                                        <?php    }?>
                                        <?php    if (request()->user()->hasRole('spl_approval')) {?>
                                        <li><a href="/Assigment/Approvals/0"><i class="fa fa-check-square-o"></i> Approval</a></li>
                                        <?php    }?>
                                        <?php    if (request()->user()->hasRole('admin_department')) {?>
                                        <li><a href="/Assigment/Realisations/0"><i class="fa fa-list-alt"></i> Realisation</a></li>
                                        <?php    }
                if (request()->user()->hasRole('spl_verification')) {?>
                                        <li><a href="/Assigment/Verifications/0"><i class="fa fa-shield"></i> Verification</a></li>
                                        <?php    }?>
                                    </ul>
                                </li>
                            </ul>
                        </li>
            <?php endif; ?>

            <?php if(request()->user()->hasRole('memo')): ?>
                    <li class="treeview<?php    if (isset($menu) && $menu == 'memo')
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-file-word-o text-blue"></i> <span>General Memo</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/GeneralMemo/1"><i class="fa fa-circle-o"></i> Overtime Produksi</a></li>
                            <li><a href="/GeneralMemo/2"><i class="fa fa-circle-o"></i> Unlock Overduedate</a></li>
                        </ul>
                    </li>
            <?php endif; ?>

            <!-- TIME MANAGEMENT SHEET (TMS) -->
            <?php if(request()->user()->hasRole('tms') || request()->user()->hasRole('admin_department') || request()->user()->hasRole('admin_calendar')): ?>
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'tms' || $menu == 'calendar'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-clock-o text-teal"></i> <span>Time Management (TMS)</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php if(request()->user()->hasRole('admin_calendar') || request()->user()->hasRole('tms')): ?>
                                        <li class="treeview<?php        if (isset($menu) && ($menu == 'calendar'))
                                echo ' active';?>">
                                            <a href="#">
                                                <i class="fa fa-calendar"></i> <span>Calendar & Shift</span>
                                                <span class="pull-right-container">
                                                    <i class="fa fa-angle-left pull-right"></i>
                                                </span>
                                            </a>
                                            <ul class="treeview-menu">
                                                <?php if(request()->user()->hasRole('admin_calendar')): ?>
                                                                <li class="<?php            if (isset($menu) && $menu == 'calendar')
                                                    echo ' active';?>">
                                                                    <a href="/Admin/Freeday"><i class="fa fa-calendar-o"></i> Calendars</a>
                                                                </li>
                                                <?php endif; ?>
                                                <?php if(request()->user()->hasRole('tms')): ?>
                                                    <li><a href="/TMS/Group"><i class="fa fa-clock-o"></i> Shift List</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
                            <?php endif; ?>
                            <?php    if (request()->user()->hasRole('tms')) {?>
                            <li class="treeview<?php        if (isset($menu) && ($menu == 'tms'))
                    echo ' active';?>">
                                <a href="#">
                                    <i class="fa fa-sliders"></i> <span>Shift Management</span>
                                    <span class="pull-right-container">
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </span>
                                </a>
                                <ul class="treeview-menu">
                                    <?php if(request()->user()->hasRole('hr_access')): ?>
                                        <li><a href="/MonthlyCheck/0"><i class="fa fa-check-circle-o"></i> Check Absen</a></li>
                                        <li><a href="/AbsensiRateDetail/0"><i class="fa fa-pie-chart"></i> Check Summary</a></li>
                                    <?php endif; ?>
                                    <li><a href="/TMS/Draft/0/0"><i class="fa fa-file-text-o"></i> Draft Group</a></li>
                                    <li><a href="/TMS/Plan/0/0/100/0"><i class="fa fa-calendar-check-o"></i> Working
                                            Schedule</a></li>
                                    <li><a href="/DailyPresence/0/0/100"><i class="fa fa-user-check"></i> Daily Presence</a>
                                    </li>
                                    <li><a href="/AbsensiRate/0/0"><i class="fa fa-line-chart"></i> Absensi Rate</a></li>
                                    <li><a href="/FailedFinger/0/0"><i class="fa fa-exclamation-triangle text-red"></i> Failed
                                            Finger</a></li>
                                </ul>
                            </li>
                            <?php        if (request()->user()->hasRole('admin_department')) {?>
                            <li class="<?php            if (isset($menu) && $menu == 'dashboard')
                        echo ' active';?>">
                                <a href="/ChangeDay"><i class="fa fa-refresh text-yellow"></i> <span>Change Day</span></a>
                            </li>
                            <?php        }?>
                            <?php    }?>
                        </ul>
                    </li>
            <?php endif; ?>

            <!-- PAYROLL -->
            <?php if (request()->user()->hasRole('payroll')) {?>
            <li
                class="treeview<?php    if (isset($menu) && ($menu == 'overtime' || $menu == 'overtime_summary' || $menu == 'overtime_tax' || $menu == 'capture_assignment' || $menu == 'summary_assignment'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-money text-orange"></i> <span>Payroll</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'overtime_summary' || $menu == 'overtime_tax'))
        echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-calculator text-orange"></i> <span>Overtime Payroll</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/payroll/summary_overtime/0/0"><i class="fa fa-circle-o"></i> Capture SPL</a>
                            </li>
                            <li><a href="/payroll/tax_overtime/0/0"><i class="fa fa-circle-o"></i> Summary OT & Tax</a>
                            </li>
                        </ul>
                    </li>
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'capture_assignment' || $menu == 'summary_assignment'))
        echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-briefcase text-yellow"></i> <span>Assignment Payroll</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/payroll/capture_assignment/0/0"><i class="fa fa-circle-o"></i> Capture
                                    Assignment</a></li>
                            <li><a href="/payroll/summary_assignment/0/0"><i class="fa fa-circle-o"></i> Summary
                                    Assignment</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
            <?php }?>

            <!-- MANIFEST & AREA -->
            <?php if (request()->user()->hasRole('manifest')) {?>
            <li
                class="treeview<?php    if (isset($menu) && ($menu == 'manifest' || $menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area' || $menu == 'outside' || $menu == 'scurity'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-map text-green"></i> <span>Manifest & Area</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'master_ap' || $menu == 'master_area' || $menu == 'working_area'))
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
                    <li><a href="/outside"><i class="fa fa-car text-orange"></i> Outside Assignment</a></li>
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'scurity'))
        echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-shield text-red"></i> <span>Security Check</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="/scurity/assigment"><i class="fa fa-file-text-o"></i> Tugas Luar</a></li>
                            <li><a href="/scurity/permit"><i class="fa fa-file-text"></i> Form Ijin</a></li>
                        </ul>
                    </li>
                    <li><a href="/manifest"><i class="fa fa-list-alt text-teal"></i> Verify Manifest</a></li>
                </ul>
            </li>
            <?php }?>

            <!-- IMPROVEMENT -->
            <?php if (request()->user()->hasRole('improvement')) {?>
            <li
                class="treeview<?php    if (isset($menu) && ($menu == 'improvement' || $menu == 'qcc' || $menu == 'gqcc'))
        echo ' active';?>">
                <a href="#">
                    <i class="fa fa-line-chart text-green"></i> <span>Improvement & QCC</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="treeview<?php    if (isset($menu) && ($menu == 'qcc'))
        echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-gears text-orange"></i> <span>QCC</span>
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
            <?php }?>

            <!-- POLICIES & CONTROLS -->
            <?php if(request()->user()->hasRole('legal')): ?>
                <li><a href="/PolicyControl"><i class="fa fa-file-pdf-o text-red"></i> <span>Document Control</span></a>
                </li>
            <?php endif; ?>
            <li><a href="/Policy"><i class="fa fa-file-text-o text-aqua"></i> <span>e-Policy</span></a></li>

            <!-- INFORMATION & REPORTS SECTION -->
            <li class="header">INFORMATION & REPORTS</li>
            <?php if(request()->user()->hasRole('info_employee') || request()->user()->hasRole('info_shift')): ?>
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'department' || $menu == 'employees' || $menu == 'ksk' || $menu == 'performance'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-user-circle text-info"></i> <span>Employee Info</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php    if (request()->user()->hasRole('info_employee')) {?>
                            <li><a href="/Employees/0"><i class="fa fa-id-badge text-aqua"></i> Employee Profile</a></li>
                            <li><a href="/Performance/0"><i class="fa fa-line-chart text-green"></i> Performance Info</a></li>
                            <?php    }?>
                            <?php    if (request()->user()->hasRole('ksk')) {?>
                            <li><a href="/Employees/KSK/0"><i class="fa fa-exclamation-circle text-yellow"></i> KSK Info</a>
                            </li>
                            <?php    }?>
                        </ul>
                    </li>
            <?php endif; ?>

            <?php if(request()->user()->hasRole('report_overtime') || request()->user()->hasRole('info_overtime_dept') || request()->user()->hasRole('info_overtime')): ?>
                    <li
                        class="treeview<?php    if (isset($menu) && ($menu == 'overtimes' || $menu == 'report_assigment'))
                echo ' active';?>">
                        <a href="#">
                            <i class="fa fa-book text-warning"></i> <span>Report Overtime</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <?php    if (request()->user()->hasRole('info_overtime_dept')) {
                date_default_timezone_set("Asia/Bangkok");
                $periode = date('Y-m');?>
                            <li><a href="/Overtimes/Assigment/<?php echo e($periode); ?>"><i class="fa fa-file-text-o"></i> Assignment
                                    Report</a></li>
                            <?php    }?>
                        </ul>
                    </li>
            <?php endif; ?>

        </ul>
    </section>
</aside><?php /**PATH C:\Users\Admin\.gemini\antigravity-ide\scratch\hrms-app\resources\views/layouts/menu_v2.blade.php ENDPATH**/ ?>