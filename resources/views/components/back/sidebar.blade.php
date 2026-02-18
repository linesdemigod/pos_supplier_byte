<div class="sidebar" data-background-color="dark">
    <div class="sidebar-logo">
        <!-- Logo Header -->
        <div class="logo-header" data-background-color="dark">
            <a href="{{ route('dashboard') }}" class="logo">
                <img src="{{ asset('images/store.png') }}" alt="Billbyte" class="navbar-brand" height="20" />
            </a>
            <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                    <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                    <i class="gg-menu-left"></i>
                </button>
            </div>
            <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
            </button>
        </div>
        <!-- End Logo Header -->
    </div>
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-secondary">
                {{-- <li class="nav-item active">
                    <a data-bs-toggle="collapse" href="#dashboard" class="collapsed" aria-expanded="false">
                        <i class="fas fa-home"></i>
                        <p>Dashboard</p>
                        <span class="caret"></span>
                    </a>
                    <div class="collapse" id="dashboard">
                        <ul class="nav nav-collapse">
                            <li>
                                <a href="{{ route('report.index') }}">
                                    <span class="sub-item">Report</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                <li class="nav-section">
                    <span class="sidebar-mini-icon">
                        <i class="fa fa-ellipsis-h"></i>
                    </span>
                    <h4 class="text-section">Components</h4>
                </li>
                @can('report')
                    <li @class([
                        'nav-item',
                        'active' => Str::startsWith(request()->route()->getName(), 'report'),
                    ])>

                        <a href="{{ route('report.index') }}">
                            <i class="fas fa-code-branch"></i>
                            <p>Report</p>

                        </a>
                    </li>
                @endcan
                {{-- @can('saleSession.menu')
                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'monthly',
                            'daily',
                        ]),
                    ])>


                        <a data-bs-toggle="collapse" href="#salesession">
                            <i class="fas fa-layer-group"></i>
                            <p>Sales Session</p>
                            <span class="caret"></span>
                        </a>

                        <div id="salesession" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), [
                                'monthly',
                                'daily',
                            ]),
                        ])>
                            <ul class="nav nav-collapse">
                                @can('saleSession.day')
                                    <li @class([
                                        'active' => request()->route()->getname() == 'daily.index',
                                    ])>
                                        <a href="{{ route('daily.index') }}">
                                            <span class="sub-item">Daily Session</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('saleSession.month')
                                    <li @class([
                                        'active' => request()->route()->getname() == 'monthly.index',
                                    ])>
                                        <a href="{{ route('monthly.index') }}">
                                            <span class="sub-item">Monthly Session</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan --}}
                @can('shop.menu')
                    <li @class([
                        'nav-item',
                        'active' => Str::startsWith(request()->route()->getName(), 'shop'),
                    ])>
                        <a href="{{ route('shop.index') }}">
                            <i class="fas fa-store"></i>
                            <p>Shop</p>

                        </a>
                    </li>
                @endcan
                @can('customer.menu')
                    <li @class([
                        'nav-item',
                        'active' => Str::startsWith(request()->route()->getName(), 'customer'),
                    ])>
                        <a href="{{ route('customer.index') }}">
                            <i class="fas fa-users"></i>
                            <p>Customer</p>

                        </a>
                    </li>
                @endcan
                @can('returnItem.menu')
                    <li @class([
                        'nav-item',
                        'active' => Str::startsWith(request()->route()->getName(), 'return'),
                    ])>
                        <a href="{{ route('return.index') }}">
                            <i class="fas fa-undo"></i>
                            <p>Return Item</p>

                        </a>
                    </li>
                @endcan
                @can('sale.menu')
                    <li @class([
                        'nav-item',
                        'active' => Str::startsWith(request()->route()->getName(), 'sale'),
                    ])>
                        <a href="{{ route('sale.index') }}">
                            <i class="fas fa-sitemap"></i>
                            <p>Sales</p>

                        </a>
                    </li>
                @endcan

                @can('inventory.menu')
                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'category',
                            'item',
                            'storeinventory',
                            'warehouseinventory',
                        ]),
                    ])>
                        <a data-bs-toggle="collapse" href="#inventory">
                            <i class="fas fa-truck-loading"></i>
                            <p>Inventory</p>
                            <span class="caret"></span>
                        </a>
                        <div id="inventory" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), [
                                'category',
                                'item',
                                'storeinventory',
                                'warehouseinventory',
                            ]),
                        ])>
                            <ul class="nav nav-collapse">
                                @can('category.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'category'),
                                    ])>
                                        <a href="{{ route('category.index') }}">
                                            <span class="sub-item">Category</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('item.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'item'),
                                    ])>
                                        <a href="{{ route('item.index') }}">
                                            <span class="sub-item">Items</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('storeInventory.menu')
                                    <li @class([
                                        'active' => Str::startsWith(
                                            request()->route()->getName(),
                                            'storeinventory'),
                                    ])>
                                        <a href="{{ route('storeinventory.index') }}">
                                            <span class="sub-item">Store Inventory</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('warehouseInventory.menu')
                                    <li @class([
                                        'active' => Str::startsWith(
                                            request()->route()->getName(),
                                            'warehouseinventory'),
                                    ])>
                                        <a href="{{ route('warehouseinventory.index') }}">
                                            <span class="sub-item">Warehouse Inventory</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('priceManagement.menu')
                                    <li>
                                        <a href="{{ route('item.price.adjustment') }}">
                                            <span class="sub-item">Price Management</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcan

                <li @class([
                    'nav-item',
                    'active submenu' => Str::startsWith(request()->route()->getName(), [
                        'supplier',
                    ]),
                ])>
                    <a data-bs-toggle="collapse" href="#supplier">
                        <i class="fas fa-layer-group"></i>
                        <p>Supplier</p>
                        <span class="caret"></span>
                    </a>
                    <div id="supplier" @class([
                        'collapse',
                        'show' => Str::startsWith(request()->route()->getName(), ['supplier']),
                    ])>
                        <ul class="nav nav-collapse">

                            <li @class([
                                'active' => Str::startsWith(
                                    request()->route()->getName(),
                                    'supplier.index'),
                            ])>
                                <a href="{{ route('supplier.index') }}">
                                    <span class="sub-item">Supplier List</span>
                                </a>
                            </li>

                            <li @class([
                                'active' => Str::startsWith(
                                    request()->route()->getName(),
                                    'supplier.create'),
                            ])>
                                <a href="{{ route('supplier.create') }}">
                                    <span class="sub-item">Create Supplier</span>
                                </a>
                            </li>

                            <li @class([
                                'active' => Str::startsWith(
                                    request()->route()->getName(),
                                    'supplier.purchase'),
                            ])>
                                <a href="{{ route('supplier.purchase') }}">
                                    <span class="sub-item">Purchase</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                @can('utilities.menu')

                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'storerequest',
                            'transfer',
                        ]),
                    ])>
                        <a data-bs-toggle="collapse" href="#utilities">
                            <i class="fas fa-th-list"></i>
                            <p>Utilities</p>
                            <span class="caret"></span>
                        </a>
                        <div id="utilities" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), [
                                'storerequest',
                                'transfer',
                            ]),
                        ])>
                            <ul class="nav nav-collapse">
                                @can('storeRequest.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'storerequest'),
                                    ])>
                                        <a href="{{ route('storerequest.index') }}">
                                            <span class="sub-item">Store Request</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('transferOrder.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'transfer'),
                                    ])>
                                        <a href="{{ route('transfer.index') }}">
                                            <span class="sub-item">Transfer Order</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan
                @can('audit')
                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'audit',
                        ]),
                    ])>
                        <a data-bs-toggle="collapse" href="#audit">
                            <i class="fas fa-th-list"></i>
                            <p>Audit</p>
                            <span class="caret"></span>
                        </a>
                        <div id="audit" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), ['audit']),
                        ])>
                            <ul class="nav nav-collapse">
                                <li @class([
                                    'active' => request()->route()->getname() == 'audit.date',
                                ])>
                                    <a href="{{ route('audit.date') }}">
                                        <span class="sub-item">Date</span>
                                    </a>
                                </li>
                                <li @class([
                                    'active' => request()->route()->getname() == 'audit.user',
                                ])>
                                    <a href="{{ route('audit.user') }}">
                                        <span class="sub-item">User</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endcan
                @can('administrativeUtility.menu')

                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'company',
                            'user',
                            'branch',
                            'restriction',
                            'store',
                            'warehouse',
                        ]),
                    ])>
                        <a data-bs-toggle="collapse" href="#setting">
                            <i class="fas fa-cog"></i>
                            <p>Adminstrative Utility</p>
                            <span class="caret"></span>
                        </a>
                        <div id="setting" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), [
                                'company',
                                'user',
                                'branch',
                                'restriction',
                                'store',
                                'warehouse',
                            ]),
                        ])>
                            <ul class="nav nav-collapse">
                                @can('company')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'company'),
                                    ])>
                                        <a href="{{ route('company.index') }}">
                                            <span class="sub-item">Company</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('systemUser.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'user'),
                                    ])>
                                        <a href="{{ route('user.index') }}">
                                            <span class="sub-item">System users</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('branch.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'branch'),
                                    ])>
                                        <a href="{{ route('branch.index') }}">
                                            <span class="sub-item">Branch</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('switchBranch.menu')
                                    <li>
                                        <a href="{{ route('dashboard.selection') }}">
                                            <span class="sub-item">Switch Branch</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('timeRestriction.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'restriction'),
                                    ])>
                                        <a href="{{ route('restriction.index') }}">
                                            <span class="sub-item">Time Restriction</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('store.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'store'),
                                    ])>
                                        <a href="{{ route('store.index') }}">
                                            <span class="sub-item">Store</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('warehouse.menu')
                                    <li @class([
                                        'active' => Str::startsWith(request()->route()->getName(), 'warehouse'),
                                    ])>
                                        <a href="{{ route('warehouse.index') }}">
                                            <span class="sub-item">Warehouse</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcan
                @can('permissions')
                    <li @class([
                        'nav-item',
                        'active submenu' => Str::startsWith(request()->route()->getName(), [
                            'permission',
                        ]),
                    ])>
                        <a data-bs-toggle="collapse" href="#permission">
                            <i class="fas fa-cogs"></i>
                            <p>Permission</p>
                            <span class="caret"></span>
                        </a>
                        <div id="permission" @class([
                            'collapse',
                            'show' => Str::startsWith(request()->route()->getName(), ['permission']),
                        ])>
                            <ul class="nav nav-collapse">
                                <li @class([
                                    'active' => request()->route()->getname() == 'permission.all.permission',
                                ])>
                                    <a href="{{ route('permission.all.permission') }}">
                                        <span class="sub-item">Permissions</span>
                                    </a>
                                </li>
                                <li @class([
                                    'active' => request()->route()->getname() == 'permission.all.role',
                                ])>
                                    <a href="{{ route('permission.all.role') }}">
                                        <span class="sub-item">Roles</span>
                                    </a>
                                </li>
                                <li @class([
                                    'active' =>
                                        request()->route()->getname() == 'permission.add.role.permission',
                                ])>
                                    <a href="{{ route('permission.add.role.permission') }}">
                                        <span class="sub-item">Set Role Permission</span>
                                    </a>
                                </li>
                                <li @class([
                                    'active' =>
                                        request()->route()->getname() == 'permission.all.role.permission',
                                ])>
                                    <a href="{{ route('permission.all.role.permission') }}">
                                        <span class="sub-item">All Roles and Permission</span>
                                    </a>
                                </li>
                                <li @class([
                                    'active' => request()->route()->getname() == 'permission.sale.point',
                                ])>
                                    <a href="{{ route('permission.sale.point') }}">
                                        <span class="sub-item">Sales Point Permission</span>
                                    </a>
                                </li>

                            </ul>
                        </div>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
