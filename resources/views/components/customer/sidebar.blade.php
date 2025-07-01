<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block sidebar collapse">
    <div class="sidebar-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}"
                    href="{{ route('customer.dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.vehicles*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-car-front"></i>
                    My Vehicles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.invoices*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-file-text"></i>
                    My Invoices
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.services*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-receipt"></i>
                    Service History
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}" href="#">
                    <i class="bi bi-person-gear"></i>
                    My Profile
                </a>
            </li>
        </ul>
    </div>
</nav>
