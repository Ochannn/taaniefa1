@include('layout.header')

@php
    $user = Auth::user();

    $isAdmin = $user && $user->isAdmin();
    $isKaryawan = $user && $user->isKaryawan();
    $isCustomer = $user && $user->isCustomer();

    $namaUser = $user->nama_user ?? 'Pengguna';

    $totalPesanan = 0;
    $pesananMenunggu = 0;
    $pesananDiproses = 0;
    $pesananSelesai = 0;
    $pesananRetur = 0;

    if ($isCustomer && $user && !empty($user->kode_user)) {
        $customerAktif = \Illuminate\Support\Facades\DB::table('master_customer')
            ->select('kode_customer', 'kode_user')
            ->where('kode_user', $user->kode_user)
            ->first();

        if ($customerAktif && !empty($customerAktif->kode_customer)) {
            $kodeCustomerAktif = $customerAktif->kode_customer;

            $totalPesanan = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
                ->where('kode_customer', $kodeCustomerAktif)
                ->count();

            $pesananMenunggu = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
                ->where('kode_customer', $kodeCustomerAktif)
                ->where('status_pesanan', 'Pending')
                ->count();

            $pesananDiproses = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
                ->where('kode_customer', $kodeCustomerAktif)
                ->whereIn('status_pesanan', ['Diproses', 'Dikirim'])
                ->count();

            $pesananSelesai = \Illuminate\Support\Facades\DB::table('transaksi_penjualan')
                ->where('kode_customer', $kodeCustomerAktif)
                ->where('status_pesanan', 'Selesai')
                ->count();

            $pesananRetur = \Illuminate\Support\Facades\DB::table('retur_penjualan')
                ->where('kode_customer', $kodeCustomerAktif)
                ->count();
        }
    }

    $pesananPerluValidasi = $pesananPerluValidasi ?? 0;
    $transaksiHariIni = $transaksiHariIni ?? 0;
    $pengirimanDiproses = $pengirimanDiproses ?? 0;
    $returMasuk = $returMasuk ?? 0;

    $masterMenus = [
        [
            'title' => 'Master Barang',
            'desc' => 'Kelola data barang, stok, dan informasi produk.',
            'icon' => 'fas fa-boxes',
            'route' => route('ajax.master.index', ['module' => 'barang']),
            'tone' => 'red'
        ],
        [
            'title' => 'Master Kategori',
            'desc' => 'Atur pengelompokan kategori barang.',
            'icon' => 'fas fa-tags',
            'route' => route('ajax.master.index', ['module' => 'kategori']),
            'tone' => 'green'
        ],
        [
            'title' => 'Master Satuan',
            'desc' => 'Kelola satuan barang yang digunakan sistem.',
            'icon' => 'fas fa-balance-scale',
            'route' => route('ajax.master.index', ['module' => 'satuan']),
            'tone' => 'blue'
        ],
        [
            'title' => 'Rekening Pembayaran',
            'desc' => 'Kelola bank, QRIS, dan metode pembayaran.',
            'icon' => 'fas fa-credit-card',
            'route' => route('ajax.master.index', ['module' => 'rekening-pembayaran']),
            'tone' => 'cyan'
        ],
        [
            'title' => 'Master User',
            'desc' => 'Kelola akun pengguna aplikasi.',
            'icon' => 'fas fa-users',
            'route' => route('ajax.master.index', ['module' => 'user']),
            'tone' => 'purple'
        ],
        [
            'title' => 'Master Supplier',
            'desc' => 'Kelola data pemasok barang.',
            'icon' => 'fas fa-truck',
            'route' => route('ajax.master.index', ['module' => 'supplier']),
            'tone' => 'orange'
        ],
    ];

    $adminMainMenus = [
        [
            'title' => 'Validasi Pesanan',
            'desc' => 'Periksa pesanan customer dan lanjutkan proses transaksi.',
            'icon' => 'fas fa-clipboard-check',
            'route' => route('ajax.transaksi.penjualan'),
            'tone' => 'blue'
        ],
        [
            'title' => 'Transaksi Pembelian',
            'desc' => 'Input pembelian barang dari supplier.',
            'icon' => 'fas fa-cart-plus',
            'route' => route('ajax.transaksi.pembelian'),
            'tone' => 'green'
        ],
        [
            'title' => 'Retur Penjualan',
            'desc' => 'Kelola pengembalian barang dari customer.',
            'icon' => 'fas fa-exchange-alt',
            'route' => route('ajax.transaksi.retur.penjualan'),
            'tone' => 'red'
        ],
        [
            'title' => 'Retur Pembelian',
            'desc' => 'Kelola pengembalian barang kepada supplier.',
            'icon' => 'fas fa-undo-alt',
            'route' => route('ajax.transaksi.retur.pembelian'),
            'tone' => 'orange'
        ],
        [
            'title' => 'Riwayat Transaksi',
            'desc' => 'Lihat daftar transaksi yang sudah tersimpan.',
            'icon' => 'fas fa-history',
            'route' => route('ajax.transaksi.riwayat.penjualan'),
            'tone' => 'purple'
        ],
        [
            'title' => 'Pengiriman',
            'desc' => 'Pantau proses pengiriman pesanan customer.',
            'icon' => 'fas fa-shipping-fast',
            'route' => route('ajax.laporan.pengiriman'),
            'tone' => 'cyan'
        ],
    ];

    $karyawanMainMenus = [
        [
            'title' => 'Transaksi Penjualan',
            'desc' => 'Input dan proses transaksi penjualan customer.',
            'icon' => 'fas fa-cash-register',
            'route' => route('ajax.transaksi.penjualan'),
            'tone' => 'blue'
        ],
        [
            'title' => 'Transaksi Pembelian',
            'desc' => 'Input pembelian barang dari supplier.',
            'icon' => 'fas fa-cart-plus',
            'route' => route('ajax.transaksi.pembelian'),
            'tone' => 'green'
        ],
        [
            'title' => 'Riwayat Transaksi',
            'desc' => 'Lihat transaksi yang sudah tersimpan.',
            'icon' => 'fas fa-history',
            'route' => route('ajax.transaksi.riwayat.penjualan'),
            'tone' => 'purple'
        ],
        [
            'title' => 'Retur Penjualan',
            'desc' => 'Kelola pengembalian barang dari customer.',
            'icon' => 'fas fa-exchange-alt',
            'route' => route('ajax.transaksi.retur.penjualan'),
            'tone' => 'red'
        ],
    ];

    $laporanMenus = [
        [
            'title' => 'Laporan Stok',
            'desc' => 'Pantau jumlah stok barang.',
            'icon' => 'fas fa-warehouse',
            'route' => route('ajax.laporan.stok'),
            'tone' => 'orange'
        ],
        [
            'title' => 'Laporan Penjualan',
            'desc' => 'Rekap penjualan berdasarkan periode.',
            'icon' => 'fas fa-chart-line',
            'route' => route('ajax.laporan.penjualan'),
            'tone' => 'blue'
        ],
        [
            'title' => 'Laporan Pembelian',
            'desc' => 'Rekap pembelian barang dari supplier.',
            'icon' => 'fas fa-cart-plus',
            'route' => route('ajax.laporan.pembelian'),
            'tone' => 'green'
        ],
        [
            'title' => 'Laporan Keuangan',
            'desc' => 'Ringkasan pemasukan, pengeluaran, dan saldo.',
            'icon' => 'fas fa-balance-scale',
            'route' => route('ajax.laporan.keuangan'),
            'tone' => 'green'
        ],
        [
            'title' => 'Laporan Pengiriman',
            'desc' => 'Pantau proses distribusi pesanan.',
            'icon' => 'fas fa-shipping-fast',
            'route' => route('ajax.laporan.pengiriman'),
            'tone' => 'cyan'
        ],
        [
            'title' => 'Laporan Retur Penjualan',
            'desc' => 'Rekap pengembalian barang dari customer.',
            'icon' => 'fas fa-undo-alt',
            'route' => route('ajax.laporan.retur.penjualan'),
            'tone' => 'red'
        ],
        [
            'title' => 'Laporan Retur Pembelian',
            'desc' => 'Rekap pengembalian barang ke supplier.',
            'icon' => 'fas fa-exchange-alt',
            'route' => route('ajax.laporan.retur.pembelian'),
            'tone' => 'purple'
        ],
        [
            'title' => 'Log Aktivitas',
            'desc' => 'Melihat riwayat aktivitas pengguna dalam sistem.',
            'icon' => 'fas fa-history',
            'route' => route('ajax.laporan.log.aktivitas'),
            'tone' => 'purple'
        ],
    ];

    $customerPrimaryActions = [
        [
            'title' => 'Buat Pesanan Baru',
            'desc' => 'Mulai pemesanan barang dengan langkah yang sederhana.',
            'button' => 'Pesan Sekarang',
            'icon' => 'fas fa-shopping-bag',
            'route' => route('ajax.transaksi.penjualan'),
            'class' => 'primary'
        ],
        [
            'title' => 'Cek Status Pesanan',
            'desc' => 'Lihat apakah pesanan masih menunggu, diproses, atau selesai.',
            'button' => 'Cek Status',
            'icon' => 'fas fa-search-location',
            'route' => route('ajax.transaksi.riwayat.penjualan'),
            'class' => 'secondary'
        ],
    ];

    $customerShortcutMenus = [
        [
            'title' => 'Riwayat Pemesanan',
            'desc' => 'Lihat semua transaksi yang pernah Anda lakukan.',
            'icon' => 'fas fa-receipt',
            'route' => route('ajax.transaksi.riwayat.penjualan'),
            'tone' => 'blue'
        ],
        [
            'title' => 'Ajukan Retur',
            'desc' => 'Ajukan pengembalian barang sesuai transaksi Anda.',
            'icon' => 'fas fa-retweet',
            'route' => route('ajax.transaksi.retur.penjualan'),
            'tone' => 'red'
        ],
        [
            'title' => 'Profil Saya',
            'desc' => 'Lihat atau perbarui informasi akun customer.',
            'icon' => 'fas fa-user-circle',
            'route' => route('customer.profile'),
            'tone' => 'green'
        ],
    ];
@endphp

<style>
    .role-dashboard {
        padding: 28px;
    }

    .tone-blue { background: #eaf2ff; color: #075eea; }
    .tone-green { background: #eafbf2; color: #16a34a; }
    .tone-orange { background: #fff3e6; color: #f97316; }
    .tone-cyan { background: #e6faff; color: #0891b2; }
    .tone-purple { background: #f1edff; color: #6d5dfc; }
    .tone-red { background: #fff1f2; color: #e11d48; }

    .admin-hero {
        border-radius: 26px;
        padding: 28px;
        color: #ffffff;
        background: linear-gradient(135deg, #073f9f 0%, #075eea 52%, #0ea5e9 100%);
        box-shadow: 0 18px 48px rgba(7, 94, 234, 0.23);
        position: relative;
        overflow: hidden;
    }

    .admin-hero::after {
        content: "";
        position: absolute;
        width: 310px;
        height: 310px;
        border-radius: 999px;
        right: -110px;
        top: -145px;
        background: rgba(255, 255, 255, 0.13);
    }

    .admin-hero-inner {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
    }

    .admin-eyebrow,
    .customer-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .admin-eyebrow {
        color: rgba(255,255,255,0.8);
    }

    .admin-title {
        margin: 0;
        color: #ffffff;
        font-size: 30px;
        font-weight: 850;
    }

    .admin-desc {
        margin: 10px 0 0;
        color: rgba(255,255,255,0.86);
        max-width: 760px;
        font-size: 14px;
        line-height: 1.65;
    }

    .admin-date-badge {
        flex: 0 0 auto;
        display: inline-flex;
        align-items: center;
        gap: 9px;
        border-radius: 16px;
        padding: 12px 15px;
        background: rgba(255,255,255,0.16);
        border: 1px solid rgba(255,255,255,0.23);
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
    }

    .admin-stat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin: 22px 0;
    }

    .admin-stat-card,
    .admin-section-card,
    .admin-menu-card,
    .customer-panel,
    .customer-order-card,
    .customer-small-card,
    .customer-step-card,
    .customer-help-card,
    .customer-summary-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        box-shadow: 0 14px 35px rgba(15, 23, 42, 0.08);
    }

    .admin-stat-card {
        border-radius: 20px;
        padding: 19px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .admin-stat-icon,
    .admin-menu-icon,
    .customer-mini-icon,
    .customer-step-icon {
        width: 52px;
        height: 52px;
        border-radius: 17px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        font-size: 20px;
    }

    .admin-stat-label {
        margin: 0 0 5px;
        color: #7a8699;
        font-size: 12px;
        font-weight: 800;
    }

    .admin-stat-value {
        margin: 0;
        color: #172033;
        font-size: 25px;
        font-weight: 850;
        line-height: 1;
    }

    .admin-stat-desc {
        margin: 7px 0 0;
        color: #7a8699;
        font-size: 12px;
    }

    .admin-main-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.25fr) minmax(310px, 0.75fr);
        gap: 20px;
    }

    .admin-section-card {
        border-radius: 22px;
        padding: 22px;
        margin-bottom: 20px;
    }

    .section-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 18px;
    }

    .section-heading h4 {
        margin: 0;
        color: #172033;
        font-size: 18px;
        font-weight: 850;
    }

    .section-heading p {
        margin: 5px 0 0;
        color: #7a8699;
        font-size: 13px;
        line-height: 1.55;
    }

    .role-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 11px;
        background: #eaf2ff;
        color: #075eea;
        font-size: 11px;
        font-weight: 850;
        white-space: nowrap;
    }

    .admin-menu-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .admin-menu-card {
        border-radius: 18px;
        padding: 17px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.22s ease;
    }

    .admin-menu-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.13);
        border-color: rgba(7, 94, 234, 0.30);
    }

    .admin-menu-card h5 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 14px;
        font-weight: 850;
    }

    .admin-menu-card p {
        margin: 0;
        color: #7a8699;
        font-size: 12px;
        line-height: 1.45;
    }

    .admin-report-list {
        display: grid;
        gap: 12px;
    }

    .admin-report-item {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 14px;
        border-radius: 17px;
        background: #f8fbff;
        border: 1px solid #e8edf5;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .admin-report-item:hover {
        background: #ffffff;
        transform: translateX(4px);
    }

    .admin-report-item h6 {
        margin: 0 0 4px;
        color: #172033;
        font-size: 13px;
        font-weight: 850;
    }

    .admin-report-item p {
        margin: 0;
        color: #7a8699;
        font-size: 12px;
    }

    .customer-dashboard {
        padding: 26px;
        background:
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.10), transparent 32%),
            radial-gradient(circle at bottom right, rgba(22, 163, 74, 0.08), transparent 26%),
            #f6f9fd;
        min-height: calc(100vh - 76px);
    }

    .customer-container {
        max-width: 1180px;
        margin: 0 auto;
    }

    .customer-hero {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 310px;
        gap: 18px;
        align-items: stretch;
        margin-bottom: 20px;
    }

    .customer-welcome {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        padding: 30px;
        color: #ffffff;
        background: linear-gradient(135deg, #0f766e 0%, #0891b2 50%, #2563eb 100%);
        box-shadow: 0 20px 50px rgba(8, 145, 178, 0.22);
    }

    .customer-welcome::before {
        content: "";
        position: absolute;
        right: -80px;
        top: -90px;
        width: 260px;
        height: 260px;
        border-radius: 999px;
        background: rgba(255,255,255,0.13);
    }

    .customer-welcome::after {
        content: "";
        position: absolute;
        right: 70px;
        bottom: -70px;
        width: 160px;
        height: 160px;
        border-radius: 999px;
        background: rgba(255,255,255,0.10);
    }

    .customer-welcome-content {
        position: relative;
        z-index: 2;
    }

    .customer-eyebrow {
        color: rgba(255,255,255,0.84);
    }

    .customer-title {
        margin: 0;
        color: #ffffff;
        font-size: 31px;
        font-weight: 850;
        line-height: 1.15;
    }

    .customer-title span {
        display: block;
        margin-top: 4px;
        color: #dffcff;
    }

    .customer-desc {
        max-width: 690px;
        margin: 14px 0 0;
        color: rgba(255,255,255,0.88);
        font-size: 15px;
        line-height: 1.7;
    }

    .customer-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 24px;
    }

    .customer-btn {
        border: 0;
        border-radius: 16px;
        padding: 13px 18px;
        font-size: 13px;
        font-weight: 850;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .customer-btn-primary {
        color: #075eea;
        background: #ffffff;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.16);
    }

    .customer-btn-light {
        color: #ffffff;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.22);
    }

    .customer-btn:hover {
        transform: translateY(-2px);
    }

    .customer-date-card {
        border-radius: 28px;
        padding: 24px;
        background: #ffffff;
        border: 1px solid #e8edf5;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .customer-date-icon {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eafbf2;
        color: #16a34a;
        font-size: 22px;
    }

    .customer-date-card h4 {
        margin: 16px 0 6px;
        color: #172033;
        font-size: 17px;
        font-weight: 850;
    }

    .customer-date-card p {
        margin: 0;
        color: #7a8699;
        font-size: 13px;
        line-height: 1.55;
    }

    .customer-main-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.38fr) minmax(315px, 0.62fr);
        gap: 20px;
    }

    .customer-panel {
        border-radius: 26px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .customer-panel-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .customer-panel-heading h4 {
        margin: 0;
        color: #172033;
        font-size: 20px;
        font-weight: 850;
    }

    .customer-panel-heading p {
        margin: 7px 0 0;
        color: #7a8699;
        font-size: 13px;
        line-height: 1.55;
    }

    .customer-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 12px;
        border-radius: 999px;
        background: #eafbf2;
        color: #16a34a;
        font-size: 11px;
        font-weight: 850;
        white-space: nowrap;
    }

    .customer-order-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 15px;
    }

    .customer-order-card {
        border-radius: 24px;
        padding: 22px;
        cursor: pointer;
        transition: all 0.22s ease;
        position: relative;
        overflow: hidden;
    }

    .customer-order-card.primary {
        background: linear-gradient(135deg, #eff6ff 0%, #ffffff 78%);
        border-color: rgba(7, 94, 234, 0.18);
    }

    .customer-order-card.secondary {
        background: linear-gradient(135deg, #ecfeff 0%, #ffffff 78%);
        border-color: rgba(8, 145, 178, 0.18);
    }

    .customer-order-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 46px rgba(15, 23, 42, 0.13);
    }

    .customer-order-top {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }

    .customer-order-icon {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #075eea;
        color: #ffffff;
        font-size: 23px;
    }

    .customer-order-card.secondary .customer-order-icon {
        background: #0891b2;
    }

    .customer-order-card h5 {
        margin: 0;
        color: #172033;
        font-size: 17px;
        font-weight: 850;
    }

    .customer-order-card p {
        margin: 0 0 18px;
        color: #66758c;
        font-size: 13px;
        line-height: 1.6;
    }

    .customer-card-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 13px;
        border-radius: 13px;
        background: #172033;
        color: #ffffff;
        font-size: 12px;
        font-weight: 850;
    }

    .customer-summary-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
    }

    .customer-summary-card {
        border-radius: 21px;
        padding: 18px;
    }

    .customer-summary-card h6 {
        margin: 12px 0 5px;
        color: #172033;
        font-size: 13px;
        font-weight: 850;
    }

    .customer-summary-value {
        margin: 0;
        color: #172033;
        font-size: 25px;
        font-weight: 850;
        line-height: 1;
    }

    .customer-summary-card p {
        margin: 8px 0 0;
        color: #7a8699;
        font-size: 12px;
        line-height: 1.45;
    }

    .customer-shortcut-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .customer-small-card {
        border-radius: 21px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.22s ease;
    }

    .customer-small-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.12);
        border-color: rgba(7, 94, 234, 0.25);
    }

    .customer-small-card h5 {
        margin: 14px 0 6px;
        color: #172033;
        font-size: 14px;
        font-weight: 850;
    }

    .customer-small-card p {
        margin: 0;
        color: #7a8699;
        font-size: 12px;
        line-height: 1.5;
    }

    .customer-step-list {
        display: grid;
        gap: 13px;
    }

    .customer-step-card {
        border-radius: 20px;
        padding: 16px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        box-shadow: none;
        background: #f8fbff;
    }

    .customer-step-number {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        background: #075eea;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 850;
        flex: 0 0 auto;
        margin-top: 10px;
    }

    .customer-step-card h5 {
        margin: 0 0 5px;
        color: #172033;
        font-size: 14px;
        font-weight: 850;
    }

    .customer-step-card p {
        margin: 0;
        color: #7a8699;
        font-size: 12px;
        line-height: 1.55;
    }

    .customer-help-card {
        border-radius: 24px;
        padding: 22px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .customer-help-card h4 {
        margin: 0 0 8px;
        color: #172033;
        font-size: 18px;
        font-weight: 850;
    }

    .customer-help-card p {
        margin: 0 0 16px;
        color: #7a8699;
        font-size: 13px;
        line-height: 1.6;
    }

    .customer-help-list {
        display: grid;
        gap: 10px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .customer-help-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: #475569;
        font-size: 12.5px;
        line-height: 1.5;
    }

    .customer-help-list i {
        color: #16a34a;
        margin-top: 2px;
    }

    .empty-access-card {
        max-width: 620px;
        margin: 60px auto;
        padding: 32px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 26px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    }

    .empty-access-card i {
        font-size: 40px;
        color: #075eea;
        margin-bottom: 15px;
    }

    .empty-access-card h4 {
        margin: 0 0 8px;
        color: #172033;
        font-weight: 850;
    }

    .empty-access-card p {
        margin: 0;
        color: #7a8699;
        line-height: 1.6;
    }

    @media (max-width: 1199px) {
        .admin-stat-grid,
        .customer-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .admin-main-grid,
        .customer-main-layout,
        .customer-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .role-dashboard,
        .customer-dashboard {
            padding: 16px;
        }

        .admin-hero,
        .customer-welcome,
        .customer-date-card,
        .customer-panel {
            border-radius: 22px;
        }

        .admin-hero-inner,
        .section-heading,
        .customer-panel-heading {
            display: block;
        }

        .admin-date-badge,
        .role-pill,
        .customer-badge {
            margin-top: 14px;
        }

        .admin-title,
        .customer-title {
            font-size: 24px;
        }

        .admin-stat-grid,
        .admin-menu-grid,
        .customer-order-grid,
        .customer-summary-grid,
        .customer-shortcut-grid {
            grid-template-columns: 1fr;
        }

        .customer-hero-actions {
            display: grid;
        }

        .customer-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

@if($isCustomer)
    <section class="customer-dashboard">
        <div class="customer-container">
            <div class="customer-hero">
                <div class="customer-welcome">
                    <div class="customer-welcome-content">
                        <div class="customer-eyebrow">
                            <i class="fas fa-user-check"></i>
                            Area Customer
                        </div>
                        <h1 class="customer-title">
                            Halo, {{ $namaUser }}
                            <span>Mau melakukan apa hari ini?</span>
                        </h1>
                        <p class="customer-desc">
                            Di halaman ini Anda dapat membuat pesanan, mengecek status pesanan, melihat riwayat transaksi, dan mengajukan retur tanpa perlu membuka banyak menu.
                        </p>

                        <div class="customer-hero-actions">
                            <button type="button" class="customer-btn customer-btn-primary" onclick="loadContent('{{ route('ajax.transaksi.penjualan') }}')">
                                <i class="fas fa-shopping-bag"></i>
                                Buat Pesanan Baru
                            </button>
                            <button type="button" class="customer-btn customer-btn-light" onclick="loadContent('{{ route('ajax.transaksi.riwayat.penjualan') }}')">
                                <i class="fas fa-search"></i>
                                Cek Status Pesanan
                            </button>
                        </div>
                    </div>
                </div>

                <div class="customer-date-card">
                    <div>
                        <div class="customer-date-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h4>{{ now()->translatedFormat('d F Y') }}</h4>
                        <p>Gunakan tombol utama untuk memulai pemesanan atau memantau pesanan yang sudah dibuat.</p>
                    </div>
                </div>
            </div>

            <div class="customer-main-layout">
                <div>
                    <div class="customer-panel">
                        <div class="customer-panel-heading">
                            <div>
                                <h4>Pilih Layanan</h4>
                                <p>Menu dibuat singkat agar mudah digunakan oleh customer umum.</p>
                            </div>
                            <span class="customer-badge">
                                <i class="fas fa-check-circle"></i>
                                Mudah Diakses
                            </span>
                        </div>

                        <div class="customer-order-grid">
                            @foreach($customerPrimaryActions as $action)
                                <div class="customer-order-card {{ $action['class'] }}" onclick="loadContent('{{ $action['route'] }}')">
                                    <div class="customer-order-top">
                                        <div class="customer-order-icon">
                                            <i class="{{ $action['icon'] }}"></i>
                                        </div>
                                        <h5>{{ $action['title'] }}</h5>
                                    </div>
                                    <p>{{ $action['desc'] }}</p>
                                    <span class="customer-card-link">
                                        {{ $action['button'] }}
                                        <i class="fas fa-arrow-right"></i>
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="customer-panel">
                        <div class="customer-panel-heading">
                            <div>
                                <h4>Ringkasan Pesanan Saya</h4>
                                <p>Angka berikut menampilkan gambaran pesanan milik akun customer yang sedang login.</p>
                            </div>
                        </div>

                        <div class="customer-summary-grid">
                            <div class="customer-summary-card">
                                <div class="customer-mini-icon tone-blue">
                                    <i class="fas fa-shopping-bag"></i>
                                </div>
                                <h6>Total Pesanan</h6>
                                <p class="customer-summary-value">{{ $totalPesanan }}</p>
                                <p>Semua pesanan Anda.</p>
                            </div>

                            <div class="customer-summary-card">
                                <div class="customer-mini-icon tone-orange">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h6>Menunggu</h6>
                                <p class="customer-summary-value">{{ $pesananMenunggu }}</p>
                                <p>Belum diproses admin.</p>
                            </div>

                            <div class="customer-summary-card">
                                <div class="customer-mini-icon tone-cyan">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <h6>Diproses</h6>
                                <p class="customer-summary-value">{{ $pesananDiproses }}</p>
                                <p>Sedang ditangani.</p>
                            </div>

                            <div class="customer-summary-card">
                                <div class="customer-mini-icon tone-green">
                                    <i class="fas fa-check"></i>
                                </div>
                                <h6>Selesai</h6>
                                <p class="customer-summary-value">{{ $pesananSelesai }}</p>
                                <p>Pesanan selesai.</p>
                            </div>
                        </div>
                    </div>

                    <div class="customer-panel">
                        <div class="customer-panel-heading">
                            <div>
                                <h4>Menu Lainnya</h4>
                                <p>Akses cepat untuk melihat transaksi, retur, dan data akun.</p>
                            </div>
                        </div>

                        <div class="customer-shortcut-grid">
                            @foreach($customerShortcutMenus as $menu)
                                <div class="customer-small-card" onclick="loadContent('{{ $menu['route'] }}')">
                                    <div class="customer-mini-icon tone-{{ $menu['tone'] }}">
                                        <i class="{{ $menu['icon'] }}"></i>
                                    </div>
                                    <h5>{{ $menu['title'] }}</h5>
                                    <p>{{ $menu['desc'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <div class="customer-panel">
                        <div class="customer-panel-heading">
                            <div>
                                <h4>Alur Pesanan</h4>
                                <p>Tahapan sederhana setelah Anda membuat pesanan.</p>
                            </div>
                        </div>

                        <div class="customer-step-list">
                            <div class="customer-step-card">
                                <div class="customer-step-number">1</div>
                                <div>
                                    <h5>Buat Pesanan</h5>
                                    <p>Customer memilih barang dan menyimpan transaksi penjualan.</p>
                                </div>
                            </div>

                            <div class="customer-step-card">
                                <div class="customer-step-number">2</div>
                                <div>
                                    <h5>Menunggu Validasi</h5>
                                    <p>Admin memeriksa data pesanan dan kelengkapan pembayaran.</p>
                                </div>
                            </div>

                            <div class="customer-step-card">
                                <div class="customer-step-number">3</div>
                                <div>
                                    <h5>Pesanan Diproses</h5>
                                    <p>Pesanan yang sudah valid akan disiapkan dan diteruskan ke proses pengiriman.</p>
                                </div>
                            </div>

                            <div class="customer-step-card">
                                <div class="customer-step-number">4</div>
                                <div>
                                    <h5>Selesai</h5>
                                    <p>Pesanan tercatat selesai setelah proses transaksi dan pengiriman selesai.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="customer-help-card">
                        <h4>Panduan Singkat</h4>
                        <p>Gunakan panduan berikut agar customer awam dapat memahami fitur utama.</p>
                        <ul class="customer-help-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Tekan <strong>Buat Pesanan Baru</strong> untuk memulai transaksi.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Tekan <strong>Cek Status Pesanan</strong> untuk melihat perkembangan pesanan.</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Gunakan <strong>Ajukan Retur</strong> apabila barang perlu dikembalikan.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@elseif($isAdmin || $isKaryawan)
    <section class="role-dashboard">
        <div class="admin-hero">
            <div class="admin-hero-inner">
                <div>
                    <div class="admin-eyebrow">
                        <i class="fas fa-layer-group"></i>
                        {{ $isAdmin ? 'Panel Administrator' : 'Panel Karyawan' }}
                    </div>
                    <h1 class="admin-title">{{ $isAdmin ? 'Dashboard Admin' : 'Dashboard Karyawan' }}</h1>
                    <p class="admin-desc">
                        {{ $isAdmin ? 'Pantau data operasional, validasi pesanan customer, transaksi, laporan, serta data master perusahaan melalui satu halaman kerja.' : 'Kelola transaksi pembelian, transaksi penjualan, retur, dan laporan operasional sesuai hak akses karyawan.' }}
                    </p>
                </div>
                <div class="admin-date-badge">
                    <i class="fas fa-calendar-alt"></i>
                    {{ now()->translatedFormat('d F Y') }}
                </div>
            </div>
        </div>

        <div class="admin-stat-grid">
            <div class="admin-stat-card">
                <div class="admin-stat-icon tone-orange">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <p class="admin-stat-label">Perlu Validasi</p>
                    <h3 class="admin-stat-value">{{ $pesananPerluValidasi }}</h3>
                    <p class="admin-stat-desc">pesanan customer</p>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon tone-blue">
                    <i class="fas fa-cash-register"></i>
                </div>
                <div>
                    <p class="admin-stat-label">Transaksi Hari Ini</p>
                    <h3 class="admin-stat-value">{{ $transaksiHariIni }}</h3>
                    <p class="admin-stat-desc">transaksi masuk</p>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon tone-cyan">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div>
                    <p class="admin-stat-label">Pengiriman</p>
                    <h3 class="admin-stat-value">{{ $pengirimanDiproses }}</h3>
                    <p class="admin-stat-desc">sedang diproses</p>
                </div>
            </div>

            <div class="admin-stat-card">
                <div class="admin-stat-icon tone-red">
                    <i class="fas fa-undo-alt"></i>
                </div>
                <div>
                    <p class="admin-stat-label">Retur Masuk</p>
                    <h3 class="admin-stat-value">{{ $returMasuk }}</h3>
                    <p class="admin-stat-desc">perlu ditindaklanjuti</p>
                </div>
            </div>
        </div>

        <div class="admin-main-grid">
            <div>
                <div class="admin-section-card">
                    <div class="section-heading">
                        <div>
                            <h4>Menu Operasional</h4>
                            <p>Digunakan untuk memproses transaksi, validasi pesanan, retur, dan pengiriman.</p>
                        </div>
                        <span class="role-pill">{{ $isAdmin ? 'Admin' : 'Karyawan' }}</span>
                    </div>

                    <div class="admin-menu-grid">
                        @foreach($isAdmin ? $adminMainMenus : $karyawanMainMenus as $menu)
                            <div class="admin-menu-card" onclick="loadContent('{{ $menu['route'] }}')">
                                <div class="admin-menu-icon tone-{{ $menu['tone'] }}">
                                    <i class="{{ $menu['icon'] }}"></i>
                                </div>
                                <div>
                                    <h5>{{ $menu['title'] }}</h5>
                                    <p>{{ $menu['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if($isAdmin)
                    <div class="admin-section-card">
                        <div class="section-heading">
                            <div>
                                <h4>Master Data</h4>
                                <p>Menu pengelolaan data dasar yang mendukung proses transaksi dan laporan.</p>
                            </div>
                        </div>

                        <div class="admin-menu-grid">
                            @foreach($masterMenus as $menu)
                                <div class="admin-menu-card" onclick="loadContent('{{ $menu['route'] }}')">
                                    <div class="admin-menu-icon tone-{{ $menu['tone'] }}">
                                        <i class="{{ $menu['icon'] }}"></i>
                                    </div>
                                    <div>
                                        <h5>{{ $menu['title'] }}</h5>
                                        <p>{{ $menu['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div>
                <div class="admin-section-card">
                    <div class="section-heading">
                        <div>
                            <h4>Laporan</h4>
                            <p>Akses laporan untuk memantau kondisi operasional perusahaan.</p>
                        </div>
                    </div>

                    <div class="admin-report-list">
                        @foreach($laporanMenus as $menu)
                            <div class="admin-report-item" onclick="loadContent('{{ $menu['route'] }}')">
                                <div class="admin-menu-icon tone-{{ $menu['tone'] }}" style="width:44px;height:44px;font-size:17px;border-radius:14px;">
                                    <i class="{{ $menu['icon'] }}"></i>
                                </div>
                                <div>
                                    <h6>{{ $menu['title'] }}</h6>
                                    <p>{{ $menu['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    <section class="role-dashboard">
        <div class="empty-access-card">
            <i class="fas fa-user-lock"></i>
            <h4>Hak Akses Tidak Dikenali</h4>
            <p>Silakan hubungi administrator untuk memastikan role akun Anda sudah sesuai.</p>
        </div>
    </section>
@endif

@include('layout.footer')