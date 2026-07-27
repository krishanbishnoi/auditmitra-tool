<!-- Left Panel -->
<style>
aside.left-panel {
    background: #2f353a;
    height: 95%
}

.navbar .main-menu {
    padding: 0px !important;
}

.navbar .navbar-nav li>a {
    color: #FFF;
}

.navbar .navbar-nav>li.active {
    /* background: #fafafa; */
    background: #3a4248;
}

.navbar .navbar-nav li.active .menu-icon,
.navbar .navbar-nav li:hover .toggle_nav_button:before,
.navbar .navbar-nav li .toggle_nav_button.nav-open:before {
    color: #fff;
}

.navbar .navbar-nav li>a .menu-icon {
    color: #fff;
}

.navbar .navbar-nav li.menu-item-has-children a:before {

    border-color: #fff #fff transparent transparent;
}

.navbar .navbar-nav li>a:hover,
.navbar .navbar-nav li>a:hover .menu-icon {
    /* background: #20a8d8; */
    color: #20a8d8;
}

.navbar .navbar-nav li:hover {
    /* background: #20a8d8; */
    color: #20a8d8;
}

.navbar .navbar-nav li.menu-item-has-children .sub-menu {
    background: #3a4248;
    padding: 0 0 0 25px;
    color: #fff;
    margin: 0
}

.right-panel .navbar-brand img {
    max-width: 130px;

}

.right-panel header.header {
    height: 60px;

}

.navbar .navbar-nav li.menu-item-has-children.show .sub-menu {
    background: none
}

.nav .navbar-nav {}

body {
    font-family: -apple-system, BlinkMacSystemFont, segoe ui, Roboto, helvetica neue, Arial, sans-serif, apple color emoji, segoe ui emoji, segoe ui symbol, noto color emoji;
    font-size: .875rem;
    font-weight: 400;
    line-height: 1.5;
    /* color: #23282c; */
}

.content {
    background-color: #e4e5e6;
}

.btn-primary {
    color: #fff !important;
    background-color: #20a8d8 !important;
    border-color: #20a8d8 !important;
}

.btn-danger {
    color: #fff !important;
    background-color: #f86c6b !important;
    border-color: #f86c6b !important;
}

.btn-sm,
.btn-group-sm>.btn {
    padding: .25rem .5rem;
    font-size: .765625rem;
    line-height: 2.5;
    border-radius: .2rem;
}

.colors {
    color: #007bff !important;
}
</style>
<?php
 $segment1 = request()->segment(1);
 $segment2 = request()->segment(2);
 $segment3 = request()->segment(3);
 $segment4 = request()->segment(4);

//  print($segment2); die;

?>
<aside id="left-panel" class="left-panel">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav" style="background: #2f353a;">
                @hasanyrole('Collection Manager')
                <li>
                    <a href="{{ route('dashboard')}}"><i class="menu-icon fa fa-laptop"></i>Dashboard </a>
                </li>
                @else
                <li class="active">
                    <a href="{{ route('dashboard')}}">
                        <i class="menu-icon fa fa-laptop"></i>Dashboard
                    </a>
                </li>
                @endrole
                {{-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Permissions</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('permissions.create')}}">Create
                Permission</a></li>
                <li><i class="fa fa-users"></i><a href="{{route('permissions.index')}}">Permission List</a></li>

            </ul>
            </li> --}}
            @role('Client')
            {{-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Roles</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('roles.create')}}">Create Role</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('roles.index')}}">Role List</a></li>

            </ul>
            </li> --}}

            <li class="menu-item-has-children dropdown {{ in_array($segment1, ['location']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['location']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Location
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['location']) ? 'show active' : '' }}">
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('location.index') }}"
                            class="{{ $segment1 == 'location' && $segment2 == '' ? 'colors' : '' }}">Create State</a>
                    </li>
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('location.create') }}"
                            class="{{ $segment1 == 'location' && $segment2 == 'create' ? 'colors' : '' }}">Create
                            City</a>
                    </li>
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('location.city_view') }}"
                            class="{{ $segment1 === 'location' && $segment2 === 'city_view' ? 'colors' : '' }}">View
                            City</a>
                    </li>
                </ul>
            </li>

            <li
                class="menu-item-has-children dropdown {{ in_array($segment1, ['create-audit-cycle','list-audit-cycle']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['create-audit-cycle','list-audit-cycle']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Audit Cycle
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['create-audit-cycle','list-audit-cycle']) ? 'show active' : '' }}">
                    <li><i class="fa fa-plus"></i><a href="{{ url('create-audit-cycle') }}"
                            class="{{ $segment1 == 'create-audit-cycle' ? 'colors' : '' }}">Create Cycle</a></li>
                    <li><i class="fa fa-users"></i><a href="{{ url('list-audit-cycle') }}"
                            class="{{ $segment1 == 'list-audit-cycle' ? 'colors' : '' }}">List</a></li>
                </ul>
            </li>
@endrole
            @role('Client|Admin')
            <li
                class="menu-item-has-children dropdown {{ in_array($segment1, ['user']) || in_array($segment1,['userhierarchy']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['user']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Users
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['user']) || in_array($segment1,['userhierarchy']) ? 'show active' : '' }}">
                    <li><i class="fa fa-plus"></i><a href="{{ route('user.create') }}"
                            class="{{ $segment1 == 'user' && $segment2 == 'create' ? 'colors' : '' }}">Create User</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{ route('user.index') }}"
                            class="{{ $segment1 == 'user' && $segment2 == '' ? 'colors' : '' }}">User List</a></li>
                    <li><i class="fa fa-plus"></i><a href="{{route('userhierarchy.create')}}"
                            class="{{ $segment1 == 'userhierarchy' && $segment2 == 'create' ? 'colors' : '' }}">Create
                            User Hierarchy</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('userhierarchy.index')}}"
                            class="{{ $segment1 == 'userhierarchy' && $segment2 == '' ? 'colors' : '' }}">User Hierarchy
                            List</a></li>
                </ul>
            </li>
            @endrole
            @role('Client')
            <li
                class="menu-item-has-children dropdown {{ in_array($segment1, ['product']) || in_array($segment1,['productattribute']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['product']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Products
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['product']) || in_array($segment1,['productattribute']) ? 'show active' : '' }}">
                    <li><i class="fa fa-plus"></i><a href="{{ route('product.create') }}"
                            class="{{ $segment1 == 'product' && $segment2 == 'create' ? 'colors' : '' }}">Create
                            Product</a></li>
                    <li><i class="fa fa-users"></i><a href="{{ route('product.index') }}"
                            class="{{ $segment1 == 'product' && $segment2 == '' ? 'colors' : '' }}">Product List</a>
                    </li>
                    <li><i class="fa fa-plus"></i><a href="{{route('productattribute.create')}}"
                            class="{{ $segment1 == 'productattribute' && $segment2 == 'create' ? 'colors' : '' }}">Create
                            Sub Product</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('productattribute.index')}}"
                            class="{{ $segment1 == 'productattribute' && $segment2=='' ? 'colors' : '' }}">Sub Product
                            List</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('Hierarchy')}}"
                            class="{{ $segment1 == 'product' && $segment2 == 'hierarchy' && $segment3=='' ? 'colors' : '' }}">Product
                            Hierarchy</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('HierarchyView')}}"
                            class="{{ $segment1 == 'product' && $segment2 == 'hierarchy' && $segment3== 'view' ? 'colors' : '' }}">Product
                            Hierarchy View</a></li>

                </ul>
            </li>
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bank"></i>Branches</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('branch.create')}}">Create Branch</a></li>
                            <li><i class="fa fa-users"></i><a href="{{route('branch.index')}}">Branch List</a></li>

                        </ul>
                    </li>
                    <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bank"></i>Branch Repo</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('branchrepo.create')}}">Create Branch Repo</a></li>
                            <li><i class="fa fa-users"></i><a href="{{route('branchrepo.index')}}">Branch Repo List</a></li>

                        </ul>
                    </li> -->
            <li class="menu-item-has-children dropdown {{ in_array($segment1, ['agency']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['agency']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Agency
                </a>
                <ul class="sub-menu children dropdown-menu {{ in_array($segment1, ['agency']) ? 'show active' : '' }}">
                    <li>
                        <i class="fa fa-plus"></i><a
                            class="{{ $segment1 == 'agency' && $segment2 == 'create' ? 'colors' : '' }}"
                            href="{{ route('agency.create') }}">Create Agency</a>
                    </li>
                    <li>
                        <i class="fa fa-users"></i><a
                            class="{{ $segment1 == 'agency' && $segment2 == '' ? 'colors' : '' }}"
                            href="{{ route('agency.index') }}">Agency List</a>
                    </li>
                </ul>
            </li>
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-industry"></i>Agency Repo</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('agencyrepo.create')}}">Create Agency Repo</a></li>
                            <li><i class="fa fa-users"></i><a href="{{route('agencyrepo.index')}}">Agency Repo List</a></li>

                        </ul>
                    </li> -->
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-area-chart"></i>Yard</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('yard.create')}}">Create Yard</a></li>
                            <li><i class="fa fa-users"></i><a href="{{route('yard.index')}}">Yard List</a></li>

                        </ul>
                    </li> -->
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Yard Repo</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('yardrepo.create')}}">Create Yard Repo</a></li>
                            <li><i class="fa fa-users"></i><a href="{{route('yardrepo.index')}}">Yard Repo List</a></li>

                        </ul>
                    </li> -->
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bar-chart"></i>Analytics</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('upload.index')}}">upload</a></li>
                            <li><i class="fa fa-plus"></i><a href="{{route('gapShow')}}">Compliance</a></li>


                        </ul>
                    </li> -->
            <!-- <li class="menu-item-has-children dropdown {{ in_array($segment1, ['bulkUpload']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['bulkUpload']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Bulk Upload
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['bulkUpload']) ? 'show active' : '' }}">
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('bulkUpload.index') }}"
                            class="{{ $segment1 == 'bulkUpload' && $segment2 == '' ? 'colors' : '' }}">Bulk Upload</a>
                    </li>
                </ul>
            </li> -->
            {{-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Alert box</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            <li><i class="fa fa-plus"></i><a href="{{route('audit_alert_box.create')}}">Create Alert
            Box</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('audit_alert_box.index')}}">Alert Box List</a></li>

            </ul>
            </li> --}}
            <li class="menu-item-has-children dropdown {{ in_array($segment1, ['qm_sheet']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['qm_sheet']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Qm Sheet
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['qm_sheet']) ? 'show active' : '' }}">
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('qm_sheet.create') }}"
                            class="{{ $segment1 == 'qm_sheet' && $segment2 == 'create' ? 'colors' : '' }}">Create Qm
                            Sheet</a>
                    </li>
                    <li>
                        <i class="fa fa-users"></i>
                        <a href="{{ route('qm_sheet.index') }}"
                            class="{{ $segment1 == 'qm_sheet' && $segment2 == '' ? 'colors' : '' }}">Qm Sheet List</a>
                    </li>
                </ul>
            </li>
            @endrole

            @hasanyrole('Admin')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-envelope"></i>Intimation Mail</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('intimation_mail.create')}}">Create Intimation
                            Mail</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('intimation_mail.index')}}">List Intimation Mail</a>
                    </li>

                </ul>
            </li>
            @endrole

            @hasanyrole('Admin|Client')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-envelope"></i>Audit Allocation</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-users"></i><a href="{{route('audit_allocation.index')}}">Audit Allocation
                            List</a></li>
                </ul>
            </li>


            @endrole
            @hasanyrole('Admin')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-file-archive-o"></i> Assign Auditor</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-file-archive-o"></i><a href="{{route('audit_allocation_assign.index')}}">List</a></li>
                </ul>
            </li>
            @endrole
            @hasanyrole('Admin')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-user"></i>Auditor Assign</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('auditorassignupload')}}">Auditor Assign Upload </a>
                    </li>
                    <li><i class="fa fa-user"></i><a href="{{route('auditor_assign.index')}}">Auditor Assign List</a>
                    </li>
                </ul>
            </li>
            @endrole

            @hasanyrole('Admin')
            <li class="menu-item-has-children dropdown {{ in_array($segment1, ['beat_plan']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['beat_plan']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Beat Plan
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['beat_plan']) ? 'show active' : '' }}">
                    <li>
                        <i class="fa fa-plus"></i>
                        <a href="{{ route('beat_plan.create') }}"
                            class="{{ $segment1 == 'beat_plan' && $segment2 == 'create' ? 'colors' : '' }}">Create Beat
                            Plan</a>
                    </li>
                    <li>
                        <i class="fa fa-users"></i>
                        <a href="{{ route('beat_plan.index') }}"
                            class="{{ $segment1 == 'beat_plan' && $segment2 == '' ? 'colors' : '' }}">Beat Plan List</a>
                    </li>
                </ul>
            </li>
            @endrole
            @role('Admin')
            <li class="menu-item-has-children dropdown {{ in_array($segment1, ['allocation']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['allocation']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Allocation Check Sheet
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['allocation']) ? 'show active' : '' }}">
                    <li><i class="fa fa-plus"></i><a href="{{ route('allocation.create') }}"
                            class="{{ $segment1 == 'allocation' && $segment2 == 'create' ? 'colors' : '' }}">Allocation
                            sheet</a></li>
                    <li><i class="fa fa-users"></i><a href="{{ route('allocation.index') }}"
                            class="{{ $segment1 == 'allocation' && $segment2 == '' ? 'colors' : '' }}">Allocated sheets
                            List</a></li>
                </ul>
            </li>
            @endrole
            @hasanyrole('Admin|Quality Control')
            <!-- <li class="menu-item-has-children dropdown {{ in_array($segment1, ['qc']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="{{ in_array($segment1, ['qc']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-user"></i>QC
                            </a>
                            <ul class="sub-menu children dropdown-menu {{ in_array($segment1, ['qc']) ? 'show active' : '' }}">
                                {{-- <li><i class="fa fa-plus"></i><a href="{{ route('allocation.create') }}">Allocation sheet</a></li> --}}
                                <li><i class="fa fa-users"></i><a href="{{ route('audited_search') }}" class="{{ $segment1 == 'qc' && $segment2 == 'submitted' ? 'colors' : '' }}">Submitted</a></li>  
                                <li><i class="fa fa-users"></i><a href="{{ route('done_audited_list') }}" class="{{ $segment1 == 'qc' && $segment2 == 'approved' ? 'colors' : '' }}">Approved</a></li>
                            </ul>
                        </li> -->
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Action plan</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('action.create')}}">Allocation sheet</a></li> --}}
                            <li><i class="fa fa-users"></i><a href="{{route('action.index')}}">Action plan</a></li>  
                            <li><i class="fa fa-users"></i><a href="{{route('action-list')}}">Action plan Answer List</a></li>  
                        </ul>
                    </li> -->
            @endrole
            @role('Admin')
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Red Alert</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li> --}}
                            <li><i class="fa fa-users"></i><a href="{{route('red-alert.index')}}">Red Alert</a></li>  
                        </ul>
                    </li> -->
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Artifact</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li> --}}
                            <li><i class="fa fa-users"></i><a href="{{route('artifact.index')}}">Artifact List</a></li>  
                        </ul>
                    </li> -->
            <!-- added by kratika -->
            <li
                class="menu-item-has-children dropdown {{ in_array($segment1, ['reports']) || in_array($segment1,['reportAutomation']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['reports']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Reports
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['reports']) || in_array($segment1,['reportAutomation']) ? 'show active' : '' }}">
                    <li><i class="fa fa-file"></i><a href="{{ route('reports') }}"
                            class="{{ $segment1 == 'reports' && $segment2 == '' ? 'colors' : '' }}">QA-QC Report</a>
                    </li>
                    <li><i class="fa fa-file"></i><a href="{{ route('reportAutomation') }}"
                            class="{{ $segment1 == 'reportAutomation' ? 'colors' : '' }}">Report Automation</a></li>
                </ul>
            </li>
            @endrole

            @hasanyrole('Quality Auditor')
                <li class="menu-item-has-children dropdown">
                    <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="menu-icon fa fa-user"></i>Assign Audits</a>
                    <ul class="sub-menu children dropdown-menu">
                        <li><i class="fa fa-user"></i><a href="{{route('auditor_assign_cases.index')}}">Audit List</a></li>
                    </ul>
                </li>
                @endrole
            @hasanyrole('Admin|Client|Quality Auditor')
            <li
                class="menu-item-has-children dropdown {{ in_array($segment1, ['auditor_list']) || in_array($segment1,['submit_audited_list']) || in_array($segment1,['save_audited_list']) ? 'show active' : '' }}">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="{{ in_array($segment1, ['auditer_list','submit_audited_list','save_audited_list']) ? 'true' : 'false' }}">
                    <i class="menu-icon fa fa-user"></i>Audit Sheet
                </a>
                <ul
                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['auditor_list']) || in_array($segment1,['submit_audited_list']) || in_array($segment1,['save_audited_list']) ? 'show active' : '' }}">
                    {{-- <li><i class="fa fa-plus"></i><a href="{{ route('allocation.create') }}">Allocation sheet</a>
            </li> --}}
            <!-- <li><i class="fa fa-users"></i><a href="{{route('done_audited_list')}}">Approved Audit List</a></li>   -->
            <li><i class="fa fa-users"></i><a href="{{ route('auditor_list') }}"
                    class="{{ $segment1 == 'auditor_list' && $segment2 == '' ? 'colors' : '' }}">Audit Sheet List</a>
            </li>
            <li><i class="fa fa-users"></i><a href="{{ route('submit_audited_list') }}"
                    class="{{ $segment1 == 'submit_audited_list' && $segment2 == '' ? 'colors' : '' }}">Submitted
                    Audited List</a></li>
            <li><i class="fa fa-users"></i><a href="{{ route('save_audited_list') }}"
                    class="{{ $segment1 == 'save_audited_list' && $segment2 == '' ? 'colors' : '' }}">Saved Audited
                    List</a></li>

            </ul>
            </li>
            @endrole

            @hasanyrole('Admin|Client')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-envelope"></i>Audit Action Planing</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-users"></i><a href="{{ route('audit.closure.list', 0) }}">Send for Closure</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{ route('audit.closure.list', 1) }}">Close Audit List</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{ route('audit.closure.list', 2) }}">Rejected Audit
                            List</a></li>

                </ul>
            </li>
           @endrole
            </ul>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
<!-- /#left-panel -->