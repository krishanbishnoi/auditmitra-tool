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
</style>
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
            @role('Admin')
            {{-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Roles</a>
                        <ul class="sub-menu children dropdown-menu">
                            <li><i class="fa fa-plus"></i><a href="{{route('roles.create')}}">Create Role</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('roles.index')}}">Role List</a></li>

            </ul>
            </li> --}}

            <li class="menu-item-has-children dropdown">

                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-map-marker"></i>Location</a>

                <ul class="sub-menu children dropdown-menu">

                    <li><i class="fa fa-plus"></i><a href="{{route('location.index')}}">Create State</a></li>

                    <li><i class="fa fa-plus"></i><a href="{{route('location.create')}}">Create City</a></li>
                    <li><i class="fa fa-plus"></i><a href="{{route('location.city_view')}}">View City</a></li>



                </ul>

            </li>
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-file"></i>Audit Cycle</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{url('create-audit-cycle')}}">Create Cycle</a></li>
                    <li><i class="fa fa-users"></i><a href="{{url('list-audit-cycle')}}">List</a></li>

                </ul>
            </li>
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-user"></i>Users</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('user.create')}}">Create User</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('user.index')}}">User List</a></li>
                    <li><i class="fa fa-plus"></i><a href="{{route('userhierarchy.create')}}">Create User Hierarchy</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{route('userhierarchy.index')}}">User Hierarchy List</a>
                    </li>
                </ul>
            </li>
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-gift"></i>Products</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('product.create')}}">Create Product</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('product.index')}}">Product List</a></li>
                    <li><i class="fa fa-plus"></i><a href="{{route('productattribute.create')}}">Create Sub Product</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{route('productattribute.index')}}">Sub Product List</a>
                    </li>
                    <li><i class="fa fa-users"></i><a href="{{route('Hierarchy')}}">Product Hierarchy</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('HierarchyView')}}">Product Hierarchy View</a></li>

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
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-industry"></i>Agency</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('agency.create')}}">Create Agency</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('agency.index')}}">Agency List</a></li>

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
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-upload"></i>Bulk Upload</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('bulkUpload.index')}}">Bulk Upload</a></li>
                    {{-- <li><i class="fa fa-plus"></i><a href="{{route('gapShow')}}">Compliance</a>
            </li> --}}


            </ul>
            </li>
            {{-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Alert box</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            <li><i class="fa fa-plus"></i><a href="{{route('audit_alert_box.create')}}">Create Alert
            Box</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('audit_alert_box.index')}}">Alert Box List</a></li>

            </ul>
            </li> --}}
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-file-excel-o"></i>Qm Sheet</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('qm_sheet.create')}}">Create Qm Sheet</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('qm_sheet.index')}}">Qm Sheet List</a></li>

                </ul>
            </li>
            @endrole
            @hasanyrole('Admin|Quality Auditor')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-user"></i>Beat Plan</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('beat_plan.create')}}">Create Beat plan</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('beat_plan.index')}}">Beat plan List</a></li>

                </ul>
            </li>
            @endrole
            @role('Admin')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-user"></i>Allocation</a>
                <ul class="sub-menu children dropdown-menu">
                    <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li>
                    <li><i class="fa fa-users"></i><a href="{{route('allocation.index')}}">Allocated sheets List</a>
                    </li>

                </ul>
            </li>
            @endrole
            @hasanyrole('Admin|Quality Control')
            <!-- <li class="menu-item-has-children dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-check"></i>QC</a>
                        <ul class="sub-menu children dropdown-menu">                         
                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li> --}}
                            <li><i class="fa fa-users"></i><a href="{{route('audited_search')}}">Submitted</a></li>  
                            <li><i class="fa fa-users"></i><a href="{{route('done_audited_list')}}">Approved</a></li>  
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
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-file-archive-o"></i>Reports</a>
                <ul class="sub-menu children dropdown-menu">

                    <li><i class="fa fa-file"></i><a href="{{route('reports')}}">QA-QC Report</a></li>
                    <li><i class="fa fa-file"></i><a href="{{route('reportAutomation')}}">Report Automation</a></li>
                </ul>
            </li>
            @endrole
            @hasanyrole('Quality Auditor')
            <li class="menu-item-has-children dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="menu-icon fa fa-user"></i>Audit Sheet</a>
                <ul class="sub-menu children dropdown-menu">
                    {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a>
            </li> --}}
            <li><i class="fa fa-users"></i><a href="{{route('auditor_list')}}">Audit Sheet List</a></li>
            <!-- <li><i class="fa fa-users"></i><a href="{{route('submit_audited_list')}}">Submited Audited List</a></li>  -->
            <li><i class="fa fa-users"></i><a href="{{route('save_audited_list')}}">Saved Audit List</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('audited_search')}}">Submit Audit list</a></li>
            <li><i class="fa fa-users"></i><a href="{{route('done_audited_list')}}">Approved Audit list</a></li>

            </ul>
            </li>

            
            @endrole
            </ul>
            <div></div>
        </div><!-- /.navbar-collapse -->
    </nav>
</aside>
<!-- /#left-panel -->