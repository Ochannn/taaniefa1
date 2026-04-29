@include('layout.header')

@php
    $user = Auth::user();

    if ($user->isAdmin()) {
        $dashboardTitle = 'Dashboard Admin';
        $dashboardRole = 'Administrator';
    } elseif ($user->isKaryawan()) {
        $dashboardTitle = 'Dashboard Karyawan';
        $dashboardRole = 'Karyawan';
    } elseif ($user->isCustomer()) {
        $dashboardTitle = 'Dashboard Customer';
        $dashboardRole = 'Customer';
    } else {
        $dashboardTitle = 'Dashboard Pengguna';
        $dashboardRole = 'Pengguna';
    }

    $masterMenus = [
        [
            'title' => 'Master Satuan',
            'desc' => 'Kelola satuan barang dan produk.',
            'icon' => 'fas fa-balance-scale',
            'route' => route('ajax.master.index', ['module' => 'satuan']),
            'color' => 'blue'
        ],
        [
            'title' => 'Master Kategori',
            'desc' => 'Kelola kelompok kategori barang.',
            'icon' => 'fas fa-tags',
            'route' => route('ajax.master.index', ['module' => 'kategori']),
            'color' => 'green'
        ],
        [
            'title' => 'Master Rekening Pembayaran',
            'desc' => 'Kelola rekening bank, QRIS, dan metode pembayaran.',
            'icon' => 'fas fa-credit-card',
            'route' => route('ajax.master.index', ['module' => 'rekening-pembayaran']),
            'color' => 'blue'
        ],
        [
            'title' => 'Master Role',
            'desc' => 'Kelola hak akses pengguna sistem.',
            'icon' => 'fas fa-user-shield',
            'route' => route('ajax.master.index', ['module' => 'role']),
            'color' => 'purple'
        ],
        [
            'title' => 'Master User',
            'desc' => 'Kelola akun pengguna aplikasi.',
            'icon' => 'fas fa-users',
            'route' => route('ajax.master.index', ['module' => 'user']),
            'color' => 'cyan'
        ],
        [
            'title' => 'Master Supplier',
            'desc' => 'Kelola data supplier barang.',
            'icon' => 'fas fa-truck',
            'route' => route('ajax.master.index', ['module' => 'supplier']),
            'color' => 'orange'
        ],
        [
            'title' => 'Master Karyawan',
            'desc' => 'Kelola data karyawan perusahaan.',
            'icon' => 'fas fa-id-badge',
            'route' => route('ajax.master.index', ['module' => 'karyawan']),
            'color' => 'indigo'
        ],
        [
            'title' => 'Master Barang',
            'desc' => 'Kelola data barang dan stok.',
            'icon' => 'fas fa-boxes',
            'route' => route('ajax.master.index', ['module' => 'barang']),
            'color' => 'red'
        ],
    ];

    $transactionMenus = [];

    if ($user->isAdmin() || $user->isKaryawan()) {
        $transactionMenus[] = [
            'title' => 'Transaksi Pembelian',
            'desc' => 'Input dan kelola transaksi pembelian barang.',
            'icon' => 'fas fa-cart-plus',
            'route' => route('ajax.transaksi.pembelian'),
            'color' => 'green'
        ];

        $transactionMenus[] = [
            'title' => 'Retur Pembelian',
            'desc' => 'Kelola pengembalian barang kepada supplier.',
            'icon' => 'fas fa-undo-alt',
            'route' => route('ajax.transaksi.retur.pembelian'),
            'color' => 'orange'
        ];
    }

    if ($user->isAdmin() || $user->isKaryawan() || $user->isCustomer()) {
        $transactionMenus[] = [
            'title' => 'Transaksi Penjualan',
            'desc' => 'Input dan kelola transaksi penjualan barang.',
            'icon' => 'fas fa-cash-register',
            'route' => route('ajax.transaksi.penjualan'),
            'color' => 'blue'
        ];

        $transactionMenus[] = [
            'title' => 'Retur Penjualan',
            'desc' => 'Ajukan dan kelola pengembalian barang dari transaksi penjualan.',
            'icon' => 'fas fa-exchange-alt',
            'route' => route('ajax.transaksi.retur.penjualan'),
            'color' => 'red'
        ];

        $transactionMenus[] = [
            'title' => 'Riwayat Transaksi',
            'desc' => 'Lihat riwayat transaksi penjualan yang telah tersimpan.',
            'icon' => 'fas fa-history',
            'route' => route('ajax.transaksi.riwayat.penjualan'),
            'color' => 'indigo'
        ];
    }

    $summaryCards = [
        [
            'title' => 'Data Master',
            'value' => $user->isAdmin() ? count($masterMenus) : 0,
            'desc' => 'menu tersedia',
            'icon' => 'fas fa-database',
            'color' => 'blue'
        ],
        [
            'title' => 'Menu Transaksi',
            'value' => count($transactionMenus),
            'desc' => 'akses transaksi',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'green'
        ],
        [
            'title' => 'Laporan',
            'value' => '1',
            'desc' => 'laporan tersedia',
            'icon' => 'fas fa-file-alt',
            'color' => 'orange'
        ],
        [
            'title' => 'Status Akun',
            'value' => 'Aktif',
            'desc' => $user->nama_user,
            'icon' => 'fas fa-user-check',
            'color' => 'cyan'
        ],
    ];
@endphp

<section class="hero-section">
    <div class="hero-content">
        <div>
            <h1 class="hero-title">{{ $dashboardTitle }}</h1>
            <p class="hero-subtitle">
                Selamat datang kembali, {{ $user->nama_user }}. Anda masuk sebagai {{ $dashboardRole }}.
            </p>
        </div>

        <div class="date-pill">
            <i class="fas fa-calendar-alt"></i>
            {{ now()->translatedFormat('d F Y') }}
        </div>
    </div>
</section>

<section class="dashboard-content">
    <div class="summary-grid">
        @foreach($summaryCards as $card)
            <div class="summary-card">
                <div class="summary-icon tone-{{ $card['color'] }}">
                    <i class="{{ $card['icon'] }}"></i>
                </div>
                <div>
                    <p class="summary-label">{{ $card['title'] }}</p>
                    <h3 class="summary-value">{{ $card['value'] }}</h3>
                    <p class="summary-desc">{{ $card['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <div class="dashboard-grid">
        <div class="content-card" id="section-master">
            <div class="card-heading">
                <div>
                    <h4 class="card-title">
                        @if($user->isAdmin())
                            Menu Master
                        @else
                            Akses Utama
                        @endif
                    </h4>
                    <p class="card-subtitle">
                        @if($user->isAdmin())
                            Kelola seluruh data master utama sistem.
                        @else
                            Gunakan menu transaksi yang tersedia sesuai hak akses akun.
                        @endif
                    </p>
                </div>
            </div>

            <div class="menu-grid">
                @if($user->isAdmin())
                    @foreach($masterMenus as $menu)
                        <div class="menu-card" onclick="loadContent('{{ $menu['route'] }}')">
                            <div class="menu-icon tone-{{ $menu['color'] }}">
                                <i class="{{ $menu['icon'] }}"></i>
                            </div>
                            <div>
                                <h5 class="menu-title">{{ $menu['title'] }}</h5>
                                <p class="menu-desc">{{ $menu['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach($transactionMenus as $menu)
                        <div class="menu-card" onclick="loadContent('{{ $menu['route'] }}')">
                            <div class="menu-icon tone-{{ $menu['color'] }}">
                                <i class="{{ $menu['icon'] }}"></i>
                            </div>
                            <div>
                                <h5 class="menu-title">{{ $menu['title'] }}</h5>
                                <p class="menu-desc">{{ $menu['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <div class="content-card">
            <div class="card-heading">
                <div>
                    <h4 class="card-title">Ringkasan Aktivitas</h4>
                    <p class="card-subtitle">Visualisasi ringkas aktivitas sistem.</p>
                </div>
                <span class="status-pill">Aktif</span>
            </div>

            <div class="chart-box">
                <div class="chart-bar" style="height: 42%"></div>
                <div class="chart-bar" style="height: 58%"></div>
                <div class="chart-bar" style="height: 48%"></div>
                <div class="chart-bar" style="height: 64%"></div>
                <div class="chart-bar" style="height: 72%"></div>
                <div class="chart-bar" style="height: 86%"></div>
                <div class="chart-bar" style="height: 96%"></div>
            </div>

            <div class="chart-summary">
                <div>
                    <strong>{{ count($transactionMenus) }}</strong>
                    <span>Menu transaksi</span>
                </div>
                <div>
                    <strong>{{ $user->isAdmin() ? count($masterMenus) : '-' }}</strong>
                    <span>Data master</span>
                </div>
            </div>
        </div>
    </div>

    @if(count($transactionMenus) > 0)
        <div class="content-card mb-4" id="section-transaksi">
            <div class="card-heading">
                <div>
                    <h4 class="card-title">Menu Transaksi</h4>
                    <p class="card-subtitle">Akses transaksi sesuai hak akses pengguna.</p>
                </div>
            </div>

            <div class="transaction-grid">
                @foreach($transactionMenus as $menu)
                    <div class="menu-card" onclick="loadContent('{{ $menu['route'] }}')">
                        <div class="menu-icon tone-{{ $menu['color'] }}">
                            <i class="{{ $menu['icon'] }}"></i>
                        </div>
                        <div>
                            <h5 class="menu-title">{{ $menu['title'] }}</h5>
                            <p class="menu-desc">{{ $menu['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="content-card" id="section-laporan">
        <div class="card-heading">
            <div>
                <h4 class="card-title">Menu Laporan</h4>
                <p class="card-subtitle">Ringkasan laporan untuk memantau data operasional.</p>
            </div>
        </div>

        <div class="report-grid">
            @if($user->isAdmin() || $user->isKaryawan())
                <div class="report-card" onclick="loadContent('{{ route('ajax.laporan.stok') }}')" style="cursor: pointer;">
                    <div class="menu-icon tone-orange">
                        <i class="fas fa-warehouse"></i>
                    </div>
                    <h6>Laporan Stok Barang</h6>
                    <p>Mengawasi jumlah barang, stok masuk, dan stok keluar.</p>
                </div>
            @endif

            <div class="report-card">
                <div class="menu-icon tone-blue">
                    <i class="fas fa-file-invoice"></i>
                </div>
                <h6>Ringkasan Transaksi</h6>
                <p>Memantau aktivitas transaksi yang tersedia sesuai hak akses.</p>
            </div>

            <div class="report-card">
                <div class="menu-icon tone-green">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h6>Aktivitas Sistem</h6>
                <p>Melihat ringkasan penggunaan menu dan aktivitas utama.</p>
            </div>
        </div>
    </div>

    <div class="info-strip">
        <div class="info-card">
            <div class="info-icon tone-blue">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <h6>Keamanan Data</h6>
                <p>Akses menu mengikuti hak pengguna.</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon tone-green">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div>
                <h6>Data Terintegrasi</h6>
                <p>Master dan transaksi saling terhubung.</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon tone-orange">
                <i class="fas fa-clock"></i>
            </div>
            <div>
                <h6>Akses Cepat</h6>
                <p>Menu utama dapat dibuka dari dashboard.</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon tone-cyan">
                <i class="fas fa-headset"></i>
            </div>
            <div>
                <h6>Siap Digunakan</h6>
                <p>Tampilan responsif untuk berbagai perangkat.</p>
            </div>
        </div>
    </div>
</section>

@include('layout.footer')