@php
    $user = Auth::user();
    $colorCode = $user->color_code ?? '#2f353a'; // fallback color
    $segment1 = request()->segment(1);
    $segment2 = request()->segment(2);
    $segment3 = request()->segment(3);
    $segment4 = request()->segment(4);
@endphp

@php
    $user = Auth::user();

    // If the user has a client_id, fetch that client
    if ($user->client_id) {
        $client = \App\User::find($user->client_id);
        $colorCode = $client && $client->color_code ? $client->color_code : '#2f353a';
        $isLegalRoute = request()->is('legal/*') || ($client->is_legal == 1 && $client->is_compliance == 0);
    } else {
        // If the user is a client themselves or no client_id is set
        $colorCode = $user->color_code ?? '#2f353a';
    }

    $allocatedmodule = App\Helpers\Helper::allocatedmodulelist();
@endphp


<style>
    aside.left-panel,
    .left-panel .navbar-nav.pb-4 {
        background-color: {{ $colorCode }} !important;
    }

    .navbar .main-menu {
        padding: 0px !important;
    }

    .navbar .navbar-nav li>a {
        color: #FFF;
    }

    .navbar .navbar-nav>li.active {
        background-color: {{ $colorCode }} !important;
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
        color: #20a8d8;
    }

    .navbar .navbar-nav li:hover {
        color: #20a8d8;
    }

    .navbar .navbar-nav li.menu-item-has-children .sub-menu {
        background-color: {{ $colorCode }} !important;
        padding: 0 0 0 25px;
        color: #fff;
        margin: 0;
    }

    .navbar .navbar-nav li.menu-item-has-children.show .sub-menu {
        background-color: {{ $colorCode }} !important;
    }

    .right-panel .navbar-brand img {
        max-width: 130px;
    }

    .right-panel header.header {
        height: 60px;
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

    aside.left-panel {
        height: calc(100vh - 55px);
        overflow: auto;
    }
</style>


<aside id="left-panel" class="left-panel" style="top:50px;">
    <nav class="navbar navbar-expand-sm navbar-default">
        <div id="main-menu" class="main-menu collapse navbar-collapse">
            <ul class="nav navbar-nav pb-4" style="background: #2f353a;">
                @if (!$isLegalRoute)
                    @hasanyrole('Collection Manager')
                        <li>
                            <a href="{{ route('dashboard') }}"><i class="menu-icon fa fa-tachometer"></i>Dashboard </a>
                        </li>
                    @else
                        <li class="active">
                            <a href="{{ route('dashboard') }}">
                                <i class="menu-icon fa fa-tachometer"></i>Dashboard
                            </a>
                        </li>
                    @endrole
                    @role('Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['client']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['client']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-user-circle"></i>Client
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['client']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ route('client.create') }}"
                                        class="{{ $segment1 == 'client' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        Client</a>
                                </li>
                                <li><i class="fa fa-users"></i><a href="{{ route('client.index') }}"
                                        class="{{ $segment1 == 'client' && $segment2 == '' ? 'colors' : '' }}">Client
                                        List</a>
                                </li>

                                <!-- @role('Client')
                                                    <li><i class="fa fa-users"></i><a href="{{ route('auditor.request') }}"
                                                            class="{{ $segment1 == 'auditor' && $segment2 == 'request' ? 'colors' : '' }}">Auditor Request</a></li>
                                            @endrole -->
                                <!-- <li><i class="fa fa-plus"></i><a href="{{ route('userhierarchy.create') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                            User Hierarchy</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('userhierarchy.index') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == '' ? 'colors' : '' }}">User Hierarchy
                                            List</a></li> -->
                            </ul>
                        </li>
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['masterqa']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['masterqa']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-user-circle"></i>Master QA
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['masterqa']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ route('masterqa.list') }}"
                                        class="{{ $segment1 == 'masterqa' && $segment2 == 'create' ? 'colors' : '' }}">Master QA List
                                        </a>
                                </li>
                                <!-- <li><i class="fa fa-users"></i><a href="{{ route('client.index') }}"
                                        class="{{ $segment1 == 'client' && $segment2 == '' ? 'colors' : '' }}">Client
                                        List</a>
                                </li> -->

                                <!-- @role('Client')
                                                    <li><i class="fa fa-users"></i><a href="{{ route('auditor.request') }}"
                                                            class="{{ $segment1 == 'auditor' && $segment2 == 'request' ? 'colors' : '' }}">Auditor Request</a></li>
                                            @endrole -->
                                <!-- <li><i class="fa fa-plus"></i><a href="{{ route('userhierarchy.create') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                            User Hierarchy</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('userhierarchy.index') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == '' ? 'colors' : '' }}">User Hierarchy
                                            List</a></li> -->
                            </ul>
                        </li>
                    @endrole
                    @role('Client|Admin|Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['user']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['user']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-user"></i>Users
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['user']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ route('user.create') }}"
                                        class="{{ $segment1 == 'user' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        User</a>
                                </li>
                                <li><i class="fa fa-users"></i><a href="{{ route('user.index') }}"
                                        class="{{ $segment1 == 'user' && $segment2 == '' ? 'colors' : '' }}">User List</a>
                                </li>

                                @role('Client')
                                    <li><i class="fa fa-paper-plane"></i><a href="{{ route('auditor.request') }}"
                                            class="{{ $segment1 == 'auditor' && $segment2 == 'request' ? 'colors' : '' }}">Auditor
                                            Request</a></li>
                                @endrole
                                <!-- <li><i class="fa fa-plus"></i><a href="{{ route('userhierarchy.create') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                            User Hierarchy</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('userhierarchy.index') }}"
                                            class="{{ $segment1 == 'userhierarchy' && $segment2 == '' ? 'colors' : '' }}">User Hierarchy
                                            List</a></li> -->
                            </ul>
                        </li>
                    @endrole


                    @role('Client|Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit_agency']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['audit_agency']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-shield"></i>Audit {{ $agencyLabel }}
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit_agency']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('audit_agency.create') }}"
                                        class="{{ $segment1 == 'audit_agency' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Audit {{ $agencyLabel }}
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('audit_agency.index') }}"
                                        class="{{ $segment1 == 'audit_agency' && $segment2 == '' ? 'colors' : '' }}">
                                        List of Audit {{ $agencyLabel }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endrole

                    @role('Super Admin')


                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"> <i class="menu-icon fa fa-id-badge"></i>Roles</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus"></i><a href="{{ route('roles.create') }}">Create Role</a></li>
                                <li><i class="fa fa-users"></i><a href="{{ route('roles.index') }}">Role List</a></li>

                            </ul>
                        </li>

                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"> <i class="menu-icon fa fa-lock"></i>Permissions</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus"></i><a href="{{ route('permissions.create') }}">Create
                                        Permission</a></li>
                                <li><i class="fa fa-users"></i><a href="{{ route('permissions.index') }}">Permission
                                        List</a>
                                </li>

                            </ul>


                        </li>
                    @endrole

                    @hasanyrole('Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['module_permissions']) || in_array($segment1, ['module_permissions']) || in_array($segment1, ['module_permissions']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['module_permissions.index', 'module_permissions.index']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-cogs"></i>Modules
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['module_permissions.index']) || in_array($segment1, ['module_permissions.index']) ? 'show active' : '' }}">

                                <li><i class="fa fa-users"></i><a href="{{ route('module_permissions.index') }}"
                                        class="{{ $segment1 == 'module_permissions' && $segment2 == '' ? 'colors' : '' }}">List</a>
                                </li>

                                <li><i class="fa fa-users"></i><a href="{{ route('module_permissions.create') }}"
                                        class="{{ $segment1 == 'roles' && $segment2 == '' ? 'colors' : '' }}">Create</a>
                                </li>



                            </ul>
                        </li>
                    @endrole

                    @role('Client')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['product']) || in_array($segment1, ['productattribute']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['product']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-cubes"></i>Products
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['product']) || in_array($segment1, ['productattribute']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus-square"></i><a href="{{ route('product.create') }}"
                                        class="{{ $segment1 == 'product' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        Product</a></li>
                                <li><i class="fa fa-shopping-bag"></i><a href="{{ route('product.index') }}"
                                        class="{{ $segment1 == 'product' && $segment2 == '' ? 'colors' : '' }}">Product
                                        List</a>
                                </li>
                                <li><i class="fa fa-sitemap	"></i><a href="{{ route('productattribute.create') }}"
                                        class="{{ $segment1 == 'productattribute' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        Sub Product</a></li>
                                <li><i class="fa fa-th-list	"></i><a href="{{ route('productattribute.index') }}"
                                        class="{{ $segment1 == 'productattribute' && $segment2 == '' ? 'colors' : '' }}">Sub
                                        Product
                                        List</a></li>
                                <!-- <li><i class="fa fa-users"></i><a href="{{ route('Hierarchy') }}"
                                            class="{{ $segment1 == 'product' && $segment2 == 'hierarchy' && $segment3 == '' ? 'colors' : '' }}">Product
                                            Hierarchy</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('HierarchyView') }}"
                                            class="{{ $segment1 == 'product' && $segment2 == 'hierarchy' && $segment3 == 'view' ? 'colors' : '' }}">Product
                                            Hierarchy View</a></li> -->

                            </ul>
                        </li>
                    @endrole

                    @role('Client')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['location']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['location']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-map-marker"></i>Location
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['location']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-flag"></i>
                                    <a href="{{ route('location.index') }}"
                                        class="{{ $segment1 == 'location' && $segment2 == '' ? 'colors' : '' }}">Create
                                        State</a>
                                </li>
                                <li>
                                    <i class="fa fa-map"></i>
                                    <a href="{{ route('location.state_view') }}"
                                        class="{{ $segment1 === 'location' && $segment2 === '' ? 'colors' : '' }}">View
                                        State</a>
                                </li>
                                <li>
                                    <i class="fa fa-map-marker"></i>
                                    <a href="{{ route('location.create') }}"
                                        class="{{ $segment1 == 'location' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        City</a>
                                </li>
                                <li>
                                    <i class="fa fa-building"></i>
                                    <a href="{{ route('location.city_view') }}"
                                        class="{{ $segment1 === 'location' && $segment2 === 'city_view' ? 'colors' : '' }}">View
                                        City</a>
                                </li>
                            </ul>
                        </li>
                    @endrole

                    @role('Client')
                        @if (in_array(17, $allocatedmodule))
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> <i class="menu-icon fa fa-code-fork"></i>Branch</a>
                                <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-plus"></i><a href="{{ route('branch.create') }}">Create
                                            Branch</a>
                                    </li>
                                    <li><i class="fa fa-code-fork	"></i><a href="{{ route('branch.index') }}">Branch
                                            List</a>
                                    </li>

                                </ul>
                            </li>
                            @if (in_array(18, $allocatedmodule))
                            @endif
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> <i class="menu-icon fa fa-folder-open"></i>Branch Repo</a>
                                <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-plus"></i><a href="{{ route('branchrepo.create') }}">Create
                                            Branch
                                            Repo</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('branchrepo.index') }}">Branch Repo
                                            List</a></li>

                                </ul>
                            </li>
                        @endif
                        @if (in_array(15, $allocatedmodule))
                            <li
                                class="menu-item-has-children dropdown {{ in_array($segment1, ['agency']) ? 'show active' : '' }}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="{{ in_array($segment1, ['agency']) ? 'true' : 'false' }}">
                                    <i class="menu-icon fa fa-building"></i>{{ $agencyLabel }}
                                </a>
                                <ul
                                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['agency']) ? 'show active' : '' }}">
                                    <li>
                                        <i class="fa fa-plus"></i><a
                                            class="{{ $segment1 == 'agency' && $segment2 == 'create' ? 'colors' : '' }}"
                                            href="{{ route('agency.create') }}">Create {{ $agencyLabel }}</a>
                                    </li>
                                    <li>
                                        <i class="fa fa-users"></i><a
                                            class="{{ $segment1 == 'agency' && $segment2 == '' ? 'colors' : '' }}"
                                            href="{{ route('agency.index') }}">{{ $agencyLabel }} List</a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        @if (in_array(16, $allocatedmodule))
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> <i class="menu-icon fa fa-archive"></i>{{ $agencyLabel }} Repo</a>
                                <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-plus"></i><a href="{{ route('agencyrepo.create') }}">Create
                                            {{ $agencyLabel }} Repo</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('agencyrepo.index') }}">{{ $agencyLabel }} Repo
                                            List</a></li>

                                </ul>
                            </li>
                        @endif
                        @if (in_array(19, $allocatedmodule))
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> <i class="menu-icon fa fa-truck"></i>Yard</a>
                                <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-plus"></i><a href="{{ route('yard.create') }}">Create Yard</a>
                                    </li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('yard.index') }}">Yard List</a></li>

                                </ul>
                            </li>
                        @endif
                        @if (in_array(20, $allocatedmodule))
                            <li class="menu-item-has-children dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false"> <i class="menu-icon fa fa-folder"></i>Yard Repo</a>
                                <ul class="sub-menu children dropdown-menu">
                                    <li><i class="fa fa-plus"></i><a href="{{ route('yardrepo.create') }}">Create Yard
                                            Repo</a></li>
                                    <li><i class="fa fa-users"></i><a href="{{ route('yardrepo.index') }}">Yard Repo
                                            List</a>
                                    </li>

                                </ul>
                            </li>
                        @endif
                        <!-- <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bar-chart"></i>Analytics</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            <li><i class="fa fa-plus"></i><a href="{{ route('upload.index') }}">upload</a></li>
                                            <li><i class="fa fa-plus"></i><a href="{{ route('gapShow') }}">Compliance</a></li>


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

                    @endrole


                    @role('Client|Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['qm_sheet']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['qm_sheet']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-check-square"></i>Audit Checksheet
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['qm_sheet']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('qm_sheet.create') }}"
                                        class="{{ $segment1 == 'qm_sheet' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        Audit Checksheet </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('qm_sheet.index') }}"
                                        class="{{ $segment1 == 'qm_sheet' && $segment2 == '' ? 'colors' : '' }}">Audit
                                        Checksheet List</a>
                                </li>
                            </ul>
                        </li>
                    @endrole


                    @role('Client')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['create-audit-cycle', 'list-audit-cycle']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['create-audit-cycle', 'list-audit-cycle']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-repeat"></i>Audit Cycle
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['create-audit-cycle', 'list-audit-cycle']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ url('create-audit-cycle') }}"
                                        class="{{ $segment1 == 'create-audit-cycle' ? 'colors' : '' }}">Create Cycle</a>
                                </li>
                                <li><i class="fa fa-users"></i><a href="{{ url('list-audit-cycle') }}"
                                        class="{{ $segment1 == 'list-audit-cycle' ? 'colors' : '' }}">List</a></li>
                            </ul>
                        </li>
                    @endrole


                    @hasanyrole('Client|Client(External)')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit_allocation']) ? 'show active' : '' }}">
                            <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i class="menu-icon fa fa-random "></i>Audit Allocation</a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit_allocation']) ? 'show active' : '' }}">
                                <li><i class="fa fa-tasks"></i><a href="{{ route('audit_allocation.index') }}"
                                        class="{{ $segment1 == 'audit_allocation' && $segment2 == '' ? 'colors' : '' }}">Audit
                                        Allocation List</a></li>
                            </ul>
                        </li>
                    @endrole
                    @hasanyrole('Admin|Client|Quality Auditor|Super Admin|Client(External)')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['auditor_list']) || in_array($segment1, ['submit_audited_list']) || in_array($segment1, ['save_audited_list']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['auditer_list', 'submit_audited_list', 'save_audited_list']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-list"></i>Audit List
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['auditor_list']) || in_array($segment1, ['submit_audited_list']) || in_array($segment1, ['save_audited_list']) ? 'show active' : '' }}">

                                @hasanyrole('Quality Auditor')
                                    <li><i class="fa fa-users"></i><a href="{{ route('auditor_list') }}"
                                            class="{{ $segment1 == 'auditor_list' && $segment2 == '' ? 'colors' : '' }}">Audit Sheet List</a>
                                    </li>

                                @endrole
                                @hasanyrole('Admin|Quality Auditor')
                                    <li><i class="fa fa-save"></i><a href="{{ route('save_audited_list') }}"
                                            class="{{ $segment1 == 'save_audited_list' && $segment2 == '' ? 'colors' : '' }}">
                                            {{ auth()->user()->hasRole('Admin') ? 'QC Audit List' : 'Saved Audited List' }}
                                            </a></li>
                                @endrole
                                <li><i class="fa fa-check-circle"></i><a href="{{ route('submit_audited_list') }}"
                                        class="{{ $segment1 == 'submit_audited_list' && $segment2 == '' ? 'colors' : '' }}">Submitted
                                        Audited List</a></li>


                            </ul>
                        </li>
                    @endrole
                    <!-- @role('Client')
                                <li class="menu-item-has-children dropdown {{ in_array($segment1, ['Paretochart']) ? 'show active' : '' }}">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="{{ in_array($segment1, ['Paretochart']) ? 'true' : 'false' }}">
                                        <i class="menu-icon fa fa-industry"></i>Pareto Chart
                                    </a>
                                    <ul class="sub-menu children dropdown-menu {{ in_array($segment1, ['Paretochart']) ? 'show active' : '' }}">
                                        <li>
                                            <i class="fa fa-plus"></i>
                                            <a href="{{ route('paretochart') }}"
                                                class="{{ $segment1 == 'Paretochart' && $segment2 == 'create' ? 'colors' : '' }}">
                                                Show Pareto Chart
                                            </a>
                                        </li>
                                    </ul>
                                </li>

            @endrole -->

                    @hasanyrole('Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit_allocation_assign']) ? 'show active' : '' }}">
                            <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i
                                    class="menu-icon fa fa-file-archive-o {{ in_array($segment1, ['audit_allocation_assign']) ? 'show active' : '' }}"></i>Assigned
                                Audits</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-file-archive-o"></i><a
                                        href="{{ route('audit_allocation_assign.index') }}"
                                        class="{{ $segment1 == 'audit_allocation_assign' && $segment2 == '' ? 'colors' : '' }}">Assigned
                                        Audit List</a></li>
                            </ul>
                        </li>
                    @endrole
                    @hasanyrole('Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit_allocation_assign']) ? 'show active' : '' }}">
                            <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i
                                    class="menu-icon fa fa-hand-o-right	 {{ in_array($segment1, ['audit_allocation_assign']) ? 'show active' : '' }}"></i>Auditor
                                Assign</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus"></i><a href="{{ route('auditorassignupload') }}"
                                        class="{{ $segment1 == 'auditorassignupload' && $segment2 == '' ? 'colors' : '' }}">Auditor
                                        Assign Upload </a>
                                </li>
                                <li><i class="fa fa-user"></i><a href="{{ route('auditor_assign.index') }}"
                                        class="{{ $segment1 == 'auditor_assign' && $segment2 == '' ? 'colors' : '' }}">Auditor
                                        Assign List</a>
                                </li>
                            </ul>
                        </li>
                    @endrole

                    @role('Client|Client(External)')

                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit-reports']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['audit-reports']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-file-text"></i>Audit Reports
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit-reports']) ? 'show active' : '' }}">
                                <li><i class="fa fa-list"></i><a href="{{ url('audit-reports') }}"
                                        class="{{ $segment1 == 'audit-reports' ? 'colors' : '' }}">List</a></li>
                            </ul>
                        </li>
                    @endrole

                    @role('Super Admin')
                        <li class="menu-item-has-children dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"> <i class="menu-icon fa fa-exclamation-triangle"></i>Alert box</a>
                            <ul class="sub-menu children dropdown-menu">
                                <li><i class="fa fa-plus"></i><a href="{{ route('audit_alert_box.create') }}">Create
                                        Alert Box</a></li>
                                <li><i class="fa fa-users"></i><a href="{{ route('audit_alert_box.index') }}">Alert Box
                                        List</a></li>
                            </ul>
                        </li>

                    @endrole

                    @hasanyrole('Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['intimation_mail']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['intimation_mail']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-envelope"></i>Intimation Mail
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['intimation_mail']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('intimation_mail.create') }}"
                                        class="{{ $segment1 == 'intimation_mail' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Intimation Mail
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('intimation_mail.index') }}"
                                        class="{{ $segment1 == 'intimation_mail' && $segment2 == '' ? 'colors' : '' }}">
                                        List Intimation Mail
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endrole



                    @hasanyrole('Admin')
                        <!-- <li class="menu-item-has-children dropdown {{ in_array($segment1, ['beat_plan']) ? 'show active' : '' }}">
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
                            </li> -->
                    @endrole
                    <!-- @role('Admin')
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
            @endrole -->
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
                                            <li><i class="fa fa-users"></i><a href="{{ route('action.index') }}">Action plan</a></li>
                                            <li><i class="fa fa-users"></i><a href="{{ route('action-list') }}">Action plan Answer List</a></li>
                                        </ul>
                                    </li> -->
                    @endrole
                    @role('Admin')
                        <!-- <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Red Alert</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li> --}}
                                            <li><i class="fa fa-users"></i><a href="{{ route('red-alert.index') }}">Red Alert</a></li>
                                        </ul>
                                    </li> -->
                        <!-- <li class="menu-item-has-children dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-user"></i>Artifact</a>
                                        <ul class="sub-menu children dropdown-menu">
                                            {{-- <li><i class="fa fa-plus"></i><a href="{{route('allocation.create')}}">Allocation sheet</a></li> --}}
                                            <li><i class="fa fa-users"></i><a href="{{ route('artifact.index') }}">Artifact List</a></li>
                                        </ul>
                                    </li> -->
                        <!-- added by kratika -->
                        <!-- <li
                                class="menu-item-has-children dropdown {{ in_array($segment1, ['reports']) || in_array($segment1, ['reportAutomation']) ? 'show active' : '' }}">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="{{ in_array($segment1, ['reports']) ? 'true' : 'false' }}">
                                    <i class="menu-icon fa fa-user"></i>Reports
                                </a>
                                <ul
                                    class="sub-menu children dropdown-menu {{ in_array($segment1, ['reports']) || in_array($segment1, ['reportAutomation']) ? 'show active' : '' }}">
                                    <li><i class="fa fa-file"></i><a href="{{ route('reports') }}"
                                            class="{{ $segment1 == 'reports' && $segment2 == '' ? 'colors' : '' }}">QA-QC Report</a>
                                    </li>
                                    <li><i class="fa fa-file"></i><a href="{{ route('reportAutomation') }}"
                                            class="{{ $segment1 == 'reportAutomation' ? 'colors' : '' }}">Report Automation</a></li>
                                </ul>
                            </li> -->
                    @endrole

                    @hasanyrole('Quality Auditor')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['auditor_assign_cases']) ? 'show active' : '' }}">
                            <a href="#" class="drop|down-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <i
                                    class="menu-icon fa fa-file-archive-o {{ in_array($segment1, ['auditor_assign_cases']) ? 'show active' : '' }}"></i>Assigned
                                Audits</a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['auditor_assign_cases']) ? 'show active' : '' }}">
                                <li><i class="fa fa-user"></i><a href="{{ route('auditor_assign_cases.index') }}"
                                        class="{{ $segment1 == 'auditor_assign_cases' && $segment2 == '' ? 'colors' : '' }}">Audit
                                        List</a></li>
                            </ul>
                        </li>
                    @endrole


                    @hasanyrole('Admin|Client|Super Admin|Client(External)')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit']) || in_array($segment2, ['closure-list']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['audit.closure.list']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-tasks"></i>Audit Action Planning
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit']) || in_array($segment2, ['closure-list']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-envelope-open"></i>
                                    <a href="{{ route('audit.closure.list', 0) }}"
                                        class="{{ $segment1 == 'audit' && $segment2 == 'closure-list' && $segment3 == '0' ? 'colors' : '' }}">
                                        Send for Closure
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('audit.closure.list', 2) }}"
                                        class="{{ $segment1 == 'audit' && $segment2 == 'closure-list' && $segment3 == '2' ? 'colors' : '' }}">
                                        Received Closures
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-check-circle"></i>
                                    <a href="{{ route('audit.closure.list', 1) }}"
                                        class="{{ $segment1 == 'audit' && $segment2 == 'closure-list' && $segment3 == '1' ? 'colors' : '' }}">
                                        Close Audit List
                                    </a>
                                </li>
                                <!-- <li>
                                        <i class="fa fa-users"></i>
                                        <a href="{{ route('audit.closure.list', 2) }}"
                                            class="{{ $segment1 == 'audit' && $segment2 == 'closure-list' && $segment3 == '2' ? 'colors' : '' }}">
                                            Rejected Audit List
                                        </a>
                                    </li> -->
                            </ul>
                        </li>

                    @endrole

                    @role('Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['cms']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['cms']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-envelope"></i> CMS Pages
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['cms']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('cms.create') }}"
                                        class="{{ $segment1 == 'cms' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create CMS Page
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('cms.index') }}"
                                        class="{{ $segment1 == 'cms' && ($segment2 == '' || $segment2 == 'index') ? 'colors' : '' }}">
                                        List of CMS Pages
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @hasanyrole('Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['questions']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['questions']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-question-circle"></i>Question Management
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['questions']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('predefined_questions.create') }}"
                                        class="{{ $segment1 == 'questions' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Question
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('predefined_questions.index') }}"
                                        class="{{ $segment1 == 'questions' && $segment2 == '' ? 'colors' : '' }}">
                                        Question List
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endhasanyrole
                    <li
                        class="menu-item-has-children dropdown {{ in_array(Route::currentRouteName(), ['support_tickets.index', 'support_tickets.create', 'support_tickets.edit', 'help_topics.index', 'help_topics.create', 'issue_types.index', 'issue_types.create']) ? 'show active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="{{ in_array(Route::currentRouteName(), ['support_tickets.index', 'support_tickets.create', 'support_tickets.edit', 'help_topics.index', 'help_topics.create', 'issue_types.index', 'issue_types.create']) ? 'true' : 'false' }}">
                            <i class="menu-icon fa fa-ticket"></i> Support Tickets
                        </a>
                        <ul
                            class="sub-menu children dropdown-menu {{ in_array(Route::currentRouteName(), ['support_tickets.index', 'support_tickets.create', 'support_tickets.edit', 'help_topics.index', 'help_topics.create', 'issue_types.index', 'issue_types.create']) ? 'show' : '' }}">

                            <li>
                                <i class="fa fa-list"></i>
                                <a href="{{ route('support_tickets.index') }}"
                                    class="{{ Route::currentRouteName() == 'support_tickets.index' ? 'colors' : '' }}">
                                    List Tickets
                                </a>
                            </li>

                            @unlessrole('Super Admin')
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('support_tickets.create') }}"
                                        class="{{ Route::currentRouteName() == 'support_tickets.create' ? 'colors' : '' }}">
                                        Create Ticket
                                    </a>
                                </li>
                            @endunlessrole

                            @role('Super Admin')
                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('help_topics.index') }}"
                                        class="{{ Route::currentRouteName() == 'help_topics.index' ? 'colors' : '' }}">
                                        List Help Topics
                                    </a>
                                </li>

                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('help_topics.create') }}"
                                        class="{{ Route::currentRouteName() == 'help_topics.create' ? 'colors' : '' }}">
                                        Create Help Topics
                                    </a>
                                </li>

                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('issue_types.index') }}"
                                        class="{{ Route::currentRouteName() == 'issue_types.index' ? 'colors' : '' }}">
                                        List Issue Types
                                    </a>
                                </li>

                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('issue_types.create') }}"
                                        class="{{ Route::currentRouteName() == 'issue_types.create' ? 'colors' : '' }}">
                                        Create Issue Types
                                    </a>
                                </li>
                            @endrole

                        </ul>
                    </li>

                @endif

                @if ($isLegalRoute)

                <li class="active mt-2">
                            <a href="{{ route('legal.dashboard') }}">
                                <i class="menu-icon fa fa-tachometer"></i>Dashboard
                            </a>
                        </li>
                    @role('Client|Admin|Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['user']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['user']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-user"></i>Users
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['user']) || in_array($segment1, ['auditor']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ route('user.create') }}"
                                        class="{{ $segment1 == 'user' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        User</a>
                                </li>
                                <li><i class="fa fa-users"></i><a href="{{ route('user.index') }}"
                                        class="{{ $segment1 == 'user' && $segment2 == '' ? 'colors' : '' }}">User
                                        List</a>
                                </li>

                                @role('Client')
                                    <li><i class="fa fa-paper-plane"></i><a href="{{ route('auditor.request') }}"
                                            class="{{ $segment1 == 'auditor' && $segment2 == 'request' ? 'colors' : '' }}">Auditor
                                            Request</a></li>
                                @endrole
                                <!-- <li><i class="fa fa-plus"></i><a href="{{ route('userhierarchy.create') }}"
                                                                                    class="{{ $segment1 == 'userhierarchy' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                                                                    User Hierarchy</a></li>
                                                                            <li><i class="fa fa-users"></i><a href="{{ route('userhierarchy.index') }}"
                                                                                    class="{{ $segment1 == 'userhierarchy' && $segment2 == '' ? 'colors' : '' }}">User Hierarchy
                                                                                    List</a></li> -->
                            </ul>
                        </li>
                    @endrole


                    @role('Client|Super Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit_agency']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['audit_agency']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-shield"></i>Audit {{ $agencyLabel }}
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit_agency']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('audit_agency.create') }}"
                                        class="{{ $segment1 == 'audit_agency' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Audit {{ $agencyLabel }}
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('audit_agency.index') }}"
                                        class="{{ $segment1 == 'audit_agency' && $segment2 == '' ? 'colors' : '' }}">
                                        List of Audit {{ $agencyLabel }}
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endrole
                    @role('Client')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['legal_qm_sheet']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['qm_sheet']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-check-square"></i>Legal Checksheet
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['legal_qm_sheet']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('legal.qm_sheet.create') }}"
                                        class="{{ $segment1 == 'legal_qm_sheet' && $segment2 == 'create' ? 'colors' : '' }}">Create
                                        Audit Checksheet </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('legal.qm_sheet.index') }}"
                                        class="{{ $segment1 == 'legal_qm_sheet' && $segment2 == '' ? 'colors' : '' }}">Audit
                                        Checksheet List</a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['advocates']) ? 'show active' : '' }}">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['advocates']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-balance-scale"></i>Advocates
                            </a>

                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['advocates']) ? 'show active' : '' }}">

                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('legal.advocates.create') }}"
                                        class="{{ $segment1 == 'advocates' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Advocate
                                    </a>
                                </li>

                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('legal.advocates.index') }}"
                                        class="{{ $segment1 == 'advocates' && ($segment2 == '' || $segment2 == null) ? 'colors' : '' }}">
                                        List of Advocates
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endrole
                    @role('Client')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['create-cycle', 'list-cycle']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['create-cycle', 'list-cycle']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-repeat"></i>Legal Cycle
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['create-cycle', 'list-cycle']) ? 'show active' : '' }}">
                                <li><i class="fa fa-plus"></i><a href="{{ url('legal/create-cycle') }}"
                                        class="{{ $segment1 == 'create-cycle' ? 'colors' : '' }}">Create Cycle</a>
                                </li>
                                <li><i class="fa fa-users"></i><a href="{{ url('legal/list-cycle') }}"
                                        class="{{ $segment1 == 'list-cycle' ? 'colors' : '' }}">List</a></li>
                            </ul>
                        </li>
                    @endrole
                    @role('Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['audit-assign']) ? 'show active' : '' }}">

                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['audit-assign']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-gavel"></i>Legal Audit Assign
                            </a>

                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['audit-assign']) ? 'show active' : '' }}">

                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('legal.audit.assign.create') }}"
                                        class="{{ $segment1 == 'audit-assign' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Assign Legal Audit
                                    </a>
                                </li>

                                <li>
                                    <i class="fa fa-list"></i>
                                    <a href="{{ route('legal.audit.assign.index') }}"
                                        class="{{ $segment1 == 'audit-assign' && ($segment2 == '' || $segment2 == null) ? 'colors' : '' }}">
                                        Assignment List
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endrole

                    @role('Quality Auditor')
                        <li class="{{ request()->is('legal.assigned.audits') ? 'active' : '' }}">
                            <a href="{{ route('legal.assigned.audits') }}">
                                <i class="menu-icon fa fa-gavel"></i>
                                Assigned Legal Audits
                            </a>
                        </li>
                    @endrole
@hasanyrole('Admin')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['intimation_mail']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['intimation_mail']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-envelope"></i>Legal Intimation Mail
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['intimation_mail']) ? 'show active' : '' }}">
                                <li>
                                    <i class="fa fa-plus"></i>
                                    <a href="{{ route('legal.intimation.create') }}"
                                        class="{{ $segment1 == 'intimation_mail' && $segment2 == 'create' ? 'colors' : '' }}">
                                        Create Intimation Mail
                                    </a>
                                </li>
                                <li>
                                    <i class="fa fa-users"></i>
                                    <a href="{{ route('legal.intimation.list') }}"
                                        class="{{ $segment1 == 'intimation_mail' && $segment2 == '' ? 'colors' : '' }}">
                                        List Intimation Mail
                                    </a>
                                </li>
                            </ul>
                        </li>

                    @endrole
                    @hasanyrole('Admin|Client|Quality Auditor')
                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['legal_auditor_list']) || in_array($segment1, ['legal_submit_audited_list']) || in_array($segment1, ['legal_save_audited_list']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['legal_auditer_list', 'legal_submit_audited_list', 'legal_save_audited_list']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-list"></i>Legal Audit List
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['legal_auditor_list']) || in_array($segment1, ['legal_submit_audited_list']) || in_array($segment1, ['legal_save_audited_list']) ? 'show active' : '' }}">

                                @hasanyrole('Quality Auditor')
                                    <li><i class="fa fa-users"></i><a href="{{ route('legal.audit.create') }}"
                                            class="{{ $segment1 == 'legal_auditor_list' && $segment2 == '' ? 'colors' : '' }}">Legal
                                            Audit
                                            Form</a>
                                    </li>

                                @endrole
                                @hasanyrole('Admin|Quality Auditor')
                                    <li><i class="fa fa-save"></i><a href="{{ route('legal.saved.audit.list') }}"
                                            class="{{ $segment1 == 'legal_save_audited_list' && $segment2 == '' ? 'colors' : '' }}">Saved
                                            Legal Audited
                                            List</a></li>
                                @endrole
                                <li><i class="fa fa-check-circle"></i><a
                                        href="{{ route('legal.submitted.audit.list') }}"
                                        class="{{ $segment1 == 'legal_submit_audited_list' && $segment2 == '' ? 'colors' : '' }}">List of Submitted Legal Audits</a></li>


                            </ul>
                        </li>
                    @endrole

                    @role('Client|Client(External)')



                        <li
                            class="menu-item-has-children dropdown {{ in_array($segment1, ['legal-audit-reports']) ? 'show active' : '' }}">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="{{ in_array($segment1, ['legal-audit-reports']) ? 'true' : 'false' }}">
                                <i class="menu-icon fa fa-file-text"></i>Legal Audit Reports
                            </a>
                            <ul
                                class="sub-menu children dropdown-menu {{ in_array($segment1, ['legal-audit-reports']) ? 'show active' : '' }}">
                                <li><i class="fa fa-list"></i><a href="{{ url('legal/legal-audit-reports') }}"
                                        class="{{ $segment1 == 'legal-audit-reports' ? 'colors' : '' }}">List</a></li>
                            </ul>
                        </li>
                    @endrole
                @endif

            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>
</aside>
<!-- /#left-panel -->
