<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-fw fa-cogs"></i>
        </div>
        <div class="sidebar-brand-text mx-3">Bengkel POS</div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Transaksi
    </div>

    <li class="nav-item {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('transaksi.create') }}">
            <i class="fas fa-fw fa-cash-register"></i>
            <span>Kasir / Transaksi Baru</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('transaksi.index') ? 'active' : '' }}"> <a class="nav-link"
            href="{{ route('transaksi.index') }}">
            <i class="fas fa-fw fa-history"></i>
            <span>Riwayat Transaksi</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('booking.index') ? 'active' : '' }}"> <a class="nav-link"
            href="{{ route('booking.index') }}">
            <i class="fas fa-fw fa-calendar-alt"></i>
            <span>Manajemen Booking</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Data Master
    </div>

    <li class="nav-item {{ request()->routeIs('pelanggan.index') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('pelanggan.index') }}">
            <i class="fas fa-fw fa-users"></i>
            <span>Pelanggan</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('sparepart.index') ? 'active' : '' }}"> <a class="nav-link"
            href="{{ route('sparepart.index') }}"> <i class="fas fa-fw fa-box"></i>
            <span>Spare Part</span></a>
    </li>

    <li class="nav-item {{ request()->routeIs('service.index') ? 'active' : '' }}"> <a class="nav-link"
            href="{{ route('service.index') }}"> <i class="fas fa-fw fa-wrench"></i>
            <span>Jasa Servis</span></a>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
