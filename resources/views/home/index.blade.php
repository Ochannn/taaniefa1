@include('layout.header')

@php
    $user = Auth::user();
@endphp

<div id="main-content" class="container-fluid dashboard-page">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dashboard</h1>
            <p class="mb-0 text-muted">
                Selamat datang, {{ $user->nama_user }}.
            </p>
        </div>
    </div>

    @if($user->isAdmin())
        <div class="dashboard-section mb-4">
            <div class="section-title-wrap">
                <h4 class="section-title">Menu Master</h4>
                <p class="section-subtitle">Akses seluruh data master utama sistem.</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'jenis-palet']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-pallet"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Jenis Palet</h5>
                            <p>Kelola data jenis palet barang.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'kualitas']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-star"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Kualitas</h5>
                            <p>Kelola kategori kualitas barang.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'satuan']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-balance-scale"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Satuan</h5>
                            <p>Kelola satuan barang dan produk.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'kategori']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-tags"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Kategori</h5>
                            <p>Kelola kelompok kategori barang.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'role']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-user-shield"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Role</h5>
                            <p>Kelola hak akses pengguna sistem.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'user']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-users"></i></div>
                        <div class="dashboard-info">
                            <h5>Master User</h5>
                            <p>Kelola akun pengguna aplikasi.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'supplier']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-truck"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Supplier</h5>
                            <p>Kelola data supplier barang.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'karyawan']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-id-badge"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Karyawan</h5>
                            <p>Kelola data karyawan perusahaan.</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                    <div class="dashboard-card" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'barang']) }}')">
                        <div class="dashboard-icon"><i class="fas fa-boxes"></i></div>
                        <div class="dashboard-info">
                            <h5>Master Barang</h5>
                            <p>Kelola data barang dan stok.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-divider"></div>
    @endif

    <div class="dashboard-section mt-4">
        <div class="section-title-wrap">
            <h4 class="section-title">Menu Transaksi</h4>
            <p class="section-subtitle">Akses transaksi sesuai hak akses pengguna.</p>
        </div>

        <div class="row">
            @if($user->isAdmin() || $user->isKaryawan())
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
               <div class="dashboard-card transaksi-card" onclick="loadContent('{{ route('ajax.transaksi.pembelian') }}')">
                    <div class="dashboard-icon"><i class="fas fa-cart-plus"></i></div>
                    <div class="dashboard-info">
                        <h5>Transaksi Pembelian</h5>
                        <p>Input dan kelola transaksi pembelian barang.</p>
                    </div>
                </div>
            </div>
            @endif

            @if($user->isAdmin() || $user->isKaryawan() || $user->isCustomer())
            <div class="col-lg-6 col-md-6 col-sm-12 mb-4">
                <div class="dashboard-card transaksi-card" onclick="loadContent('{{ route('ajax.transaksi.penjualan') }}')">
                    <div class="dashboard-icon"><i class="fas fa-cash-register"></i></div>
                    <div class="dashboard-info">
                        <h5>Transaksi Penjualan</h5>
                        <p>Input dan kelola transaksi penjualan barang.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@include('layout.footer')