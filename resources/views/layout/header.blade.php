<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>CV. Syavir Jaya Utama</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom styles for this template-->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css" />
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/card.css') }}" rel="stylesheet">
    <link href="{{ asset('css/transaksi.css') }}" rel="stylesheet">

<style>

table.dataTable th,
table.dataTable td {
    text-align: left !important;
}

.btn-tambah-kualitas {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.btn-tambah-kualitas:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
    color: #fff;
}
</style>

</head>
<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
                <div class="sidebar-brand-text mx-3">CV. Syavir Jaya Utama</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Pages Collapse Menu -->
            @php
                $user = Auth::user();
            @endphp

            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Aktivitas
            </div>

            @if($user && $user->isAdmin())
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Master</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Daftar Master:</h6>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'barang']) }}'); return false;">Master Barang</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'kategori']) }}'); return false;">Master Kategori</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'jenis-palet']) }}'); return false;">Master Jenis Palet</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'kualitas']) }}'); return false;">Master Kualitas</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'satuan']) }}'); return false;">Master Satuan</a>
                        <hr class="sidebar-divider" style="background-color: #b9b9b9" >
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'role']) }}'); return false;">Master Role</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'user']) }}'); return false;">Master User</a>
                        <hr class="sidebar-divider" style="background-color: #b9b9b9" >
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'supplier']) }}'); return false;">Master Supplier</a>
                        <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'karyawan']) }}'); return false;">Master Karyawan</a>
                    </div>
                </div>
            </li>
            @endif

            @if($user && ($user->isAdmin() || $user->isKaryawan() || $user->isCustomer()))
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Transaksi</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Daftar Transaksi:</h6>

                        @if($user->isAdmin() || $user->isKaryawan())
                            <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.transaksi.pembelian') }}'); return false;">
                                Transaksi Pembelian
                            </a>
                        @endif

                        @if($user->isAdmin() || $user->isKaryawan()|| $user->isCustomer())
                            <a class="collapse-item" href="#" onclick="loadContent('{{ route('ajax.transaksi.penjualan') }}'); return false;">
                                Transaksi Penjualan
                            </a>
                        @endif
                    </div>
                </div>
            </li>
            @endif

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            @if($user && ($user->isAdmin() || $user->isKaryawan()))
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                        aria-expanded="true" aria-controls="collapseLaporan">
                        <i class="fas fa-fw fa-chart-bar"></i>
                        <span>Laporan</span>
                    </a>
                    <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            <h6 class="collapse-header">Daftar Laporan:</h6>

                            <a class="collapse-item" href="#"
                            onclick="loadContent('{{ route('ajax.laporan.stok') }}'); return false;">
                                Laporan Stok Barang
                            </a>
                        </div>
                    </div>
                </li>
            @endif

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    {{ Auth::user()->nama_user ?? 'User' }}
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>

                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#"
                                onclick="loadContent('{{ route('customer.profile') }}'); return false;">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                <div class="dropdown-divider"></div>

                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>


                <!-- End of Topbar -->