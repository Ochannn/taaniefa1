<div class="container-fluid shop-checkout-page">
    <style>
        .shop-checkout-page {
            padding-bottom: 32px;
        }

        .shop-hero {
            background: linear-gradient(135deg, #075eea 0%, #0ea5e9 100%);
            border-radius: 24px;
            padding: 26px;
            color: #ffffff;
            margin-bottom: 22px;
            box-shadow: 0 16px 40px rgba(7, 94, 234, 0.22);
            position: relative;
            overflow: hidden;
        }

        .shop-hero::after {
            content: "";
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.13);
            right: -90px;
            top: -110px;
        }

        .shop-hero-content {
            position: relative;
            z-index: 2;
        }

        .shop-hero-title {
            font-size: 26px;
            font-weight: 850;
            margin: 0 0 6px;
            color: #ffffff;
        }

        .shop-hero-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 14px;
        }

        .shop-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(360px, 0.55fr);
            gap: 20px;
            align-items: flex-start;
        }

        .shop-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 22px;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            margin-bottom: 18px;
        }

        .shop-card-header {
            padding: 18px 22px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
        }

        .shop-card-title {
            margin: 0;
            font-size: 17px;
            font-weight: 850;
            color: #172033;
        }

        .shop-card-subtitle {
            display: block;
            margin-top: 4px;
            font-size: 12px;
            color: #7a8699;
        }

        .shop-card-body {
            padding: 22px;
        }

        .shop-step-badge {
            min-width: 34px;
            height: 34px;
            border-radius: 12px;
            background: #eaf2ff;
            color: #075eea;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 850;
        }

        .required-label::after {
            content: " *";
            color: #dc3545;
            font-weight: 700;
        }

        .shop-field label {
            font-size: 13px;
            font-weight: 750;
            color: #172033;
            margin-bottom: 7px;
        }

        .shop-field .form-control {
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            min-height: 44px;
            font-size: 13px;
            background-color: #ffffff;
        }

        .shop-field textarea.form-control {
            min-height: 88px;
        }

        .shop-field .form-control:focus {
            border-color: #075eea;
            box-shadow: 0 0 0 3px rgba(7, 94, 234, 0.12);
        }

        .shop-info-box {
            background: #f8fbff;
            border: 1px solid #e8edf5;
            border-radius: 18px;
            padding: 16px;
        }

        .shop-info-box h6 {
            font-size: 14px;
            font-weight: 850;
            margin-bottom: 10px;
            color: #172033;
        }

        .shop-product-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: 14px;
            margin-bottom: 16px;
        }

        .shop-product-pick {
            background: #f8fbff;
            border: 1px solid #e8edf5;
            border-radius: 20px;
            padding: 18px;
        }

        .shop-product-pick-title {
            margin: 0 0 14px;
            font-size: 15px;
            font-weight: 850;
            color: #172033;
        }

        .shop-product-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: 14px;
        }

        .shop-product-grid .col-product-category {
            grid-column: span 3;
        }

        .shop-product-grid .col-product-code {
            grid-column: span 3;
        }

        .shop-product-grid .col-product-name {
            grid-column: span 3;
        }

        .shop-product-grid .col-product-qty {
            grid-column: span 1;
        }

        .shop-product-grid .col-product-price {
            grid-column: span 2;
        }

        .shop-add-product {
            margin-top: 14px;
            display: flex;
            justify-content: flex-end;
        }

        .shop-cart-table {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e8edf5;
        }

        .shop-cart-table table {
            margin-bottom: 0;
        }

        .shop-cart-table thead th {
            background: #f8fbff;
            border-bottom: 1px solid #e8edf5;
            font-size: 12px;
            font-weight: 850;
            color: #475569;
            white-space: nowrap;
        }

        .shop-cart-table tbody td {
            vertical-align: middle;
            font-size: 13px;
        }

        .shop-side {
            position: sticky;
            top: 96px;
        }

        .shop-summary-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            border-radius: 24px;
            box-shadow: 0 14px 38px rgba(15, 23, 42, 0.10);
            overflow: hidden;
        }

        .shop-summary-header {
            padding: 20px;
            background: linear-gradient(135deg, #172033 0%, #075eea 100%);
            color: #ffffff;
        }

        .shop-summary-header h5 {
            margin: 0;
            font-size: 17px;
            font-weight: 850;
            color: #ffffff;
        }

        .shop-summary-header small {
            display: block;
            margin-top: 4px;
            color: rgba(255, 255, 255, 0.78);
        }

        .shop-summary-body {
            padding: 20px;
        }

        .shop-summary-line {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .shop-summary-line span:first-child {
            color: #64748b;
        }

        .shop-summary-line span:last-child {
            color: #172033;
            font-weight: 850;
        }

        .shop-summary-total {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding: 16px;
            border-radius: 18px;
            background: #eaf2ff;
            color: #075eea;
            margin-top: 14px;
            margin-bottom: 18px;
        }

        .shop-summary-total span:first-child {
            font-weight: 800;
        }

        .shop-summary-total span:last-child {
            font-weight: 900;
            font-size: 18px;
        }

        .shop-actions {
            display: grid;
            gap: 10px;
        }

        .shop-actions .btn {
            min-height: 44px;
            border-radius: 14px;
            font-weight: 800;
        }

        .btn-shop-primary {
            background: linear-gradient(135deg, #075eea, #0ea5e9);
            border: 0;
            color: #ffffff;
        }

        .btn-shop-primary:hover {
            color: #ffffff;
            filter: brightness(0.96);
        }

        .btn-shop-outline {
            border: 1px solid #dbe5f0;
            background: #ffffff;
            color: #172033;
        }

        .shop-mini-note {
            margin-top: 14px;
            font-size: 12px;
            color: #7a8699;
            line-height: 1.5;
        }

        .payment-info-box {
            background: #f8fbff;
            border: 1px solid #e8edf5;
            border-radius: 18px;
            padding: 16px;
            margin-top: 14px;
        }

        .payment-info-box h6 {
            font-weight: 850;
            color: #172033;
        }

        .select2-container--default .select2-selection--single {
            min-height: 44px;
            border-radius: 14px;
            border-color: #e2e8f0;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 42px;
            font-size: 13px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 42px;
        }

        @media (max-width: 1199px) {
            .shop-layout {
                grid-template-columns: 1fr;
            }

            .shop-side {
                position: static;
            }
        }

        @media (max-width: 991px) {
            .shop-product-grid .col-product-category,
            .shop-product-grid .col-product-code,
            .shop-product-grid .col-product-name,
            .shop-product-grid .col-product-qty,
            .shop-product-grid .col-product-price {
                grid-column: span 12;
            }

            .shop-product-toolbar {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div id="sales-alert"></div>

    <input type="hidden" id="form_mode_penjualan" value="create">
    <input type="hidden" id="edit_kode_pesanan" value="">

    <div class="shop-hero">
        <div class="shop-hero-content">
            <h4 class="shop-hero-title">Checkout Penjualan</h4>
            <p class="shop-hero-subtitle">
                Pilih produk, isi data pengiriman, lalu lanjutkan pembayaran melalui Midtrans.
            </p>
        </div>
    </div>

    <div class="shop-layout">
        <div class="shop-main">
            <div class="shop-card">
                <div class="shop-card-header">
                    <div>
                        <h5 class="shop-card-title">Informasi Pesanan</h5>
                        <span class="shop-card-subtitle">Lengkapi data customer dan pengiriman.</span>
                    </div>
                    <span class="shop-step-badge">1</span>
                </div>

                <div class="shop-card-body">
                    <form id="formPenjualan">
                        @csrf

                        <div class="row">
                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label>Kode Pesanan</label>
                                    <input
                                        type="text"
                                        id="kode_pesanan_preview"
                                        class="form-control soft-readonly"
                                        value="{{ $kodePreview }}"
                                        readonly
                                    >
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Tanggal Pesanan</label>
                                    <input
                                        type="date"
                                        id="tgl_pesanan"
                                        class="form-control"
                                        value="{{ date('Y-m-d') }}"
                                        required
                                    >
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                @if($user->kode_role === 'KRL001' || $user->kode_role === 'KRL002')
                                    <div class="form-group shop-field">
                                        <label for="kode_customer" class="required-label">Customer</label>
                                        <select name="kode_customer" id="kode_customer" class="form-control" required>
                                            <option value="">-- Pilih Customer --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->kode_customer }}">
                                                    {{ $customer->nama_customer }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group shop-field">
                                        <label class="required-label">Customer</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            value="{{ $customerAktif->nama_customer ?? '' }}"
                                            readonly
                                        >
                                        <input
                                            type="hidden"
                                            id="kode_customer"
                                            name="kode_customer"
                                            value="{{ $customerAktif->kode_customer ?? '' }}"
                                        >
                                    </div>
                                @endif
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Tujuan Pengiriman</label>
                                    <select id="rajaongkir_destination" class="form-control" required></select>
                                    <input type="hidden" id="provinsi_tujuan">
                                    <input type="hidden" id="kota_tujuan">
                                    <input type="hidden" id="jenis_pengiriman">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Kurir</label>
                                    <select id="kurir" class="form-control" required>
                                        <option value="">Pilih Kurir</option>
                                        <option value="jne">JNE</option>
                                        <option value="pos">POS Indonesia</option>
                                        <option value="tiki">TIKI</option>
                                        <option value="jnt">J&T</option>
                                        <option value="sicepat">SiCepat</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Berat Kiriman</label>
                                    <input
                                        type="number"
                                        id="berat_pengiriman"
                                        class="form-control"
                                        value="1000"
                                        min="1"
                                        required
                                    >
                                    <small class="text-muted">Berat dalam gram.</small>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Layanan Kurir</label>
                                    <select id="layanan_kurir" class="form-control" required>
                                        <option value="">Cek ongkir terlebih dahulu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Ongkir Pengiriman</label>
                                    <input
                                        type="number"
                                        id="ongkir_pesanan"
                                        class="form-control soft-readonly"
                                        min="0"
                                        step="1"
                                        value="0"
                                        readonly
                                        required
                                    >
                                    <input type="hidden" id="estimasi_pengiriman">
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button
                                        type="button"
                                        id="btnCekOngkir"
                                        class="btn btn-info btn-soft-primary btn-block"
                                    >
                                        Cek Ongkir
                                    </button>
                                </div>
                            </div>

                            <div class="col-lg-4 col-md-6">
                                <div class="form-group shop-field">
                                    <label class="required-label">Status Pesanan</label>

                                    @if($user->kode_role === 'KRL001' || $user->kode_role === 'KRL002')
                                        <select id="status_pesanan" class="form-control" required>
                                            <option value="">Pilih Status Pesanan</option>
                                            <option value="Pending" selected>Pending</option>
                                            <option value="Diproses">Diproses</option>
                                            <option value="Dikirim">Dikirim</option>
                                            <option value="Selesai">Selesai</option>
                                            <option value="Batal">Batal</option>
                                        </select>
                                    @else
                                        <input type="text" class="form-control soft-readonly" value="Pending" readonly>
                                        <input type="hidden" id="status_pesanan" value="Pending">
                                        <small class="text-muted">
                                            Status awal customer selalu Pending dan hanya dapat diubah oleh admin.
                                        </small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group shop-field">
                                    <label class="required-label">Alamat Kirim Pesanan</label>
                                    <textarea
                                        id="alamat_kirim_pesanan"
                                        rows="3"
                                        class="form-control"
                                        placeholder="Masukkan alamat pengiriman"
                                        required
                                    ></textarea>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group shop-field">
                                    <label class="required-label">Catatan Pesanan</label>
                                    <textarea
                                        id="catatan_pesanan"
                                        rows="3"
                                        class="form-control"
                                        placeholder="Masukkan catatan pesanan jika diperlukan"
                                        required
                                    ></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="shop-card">
                <div class="shop-card-header">
                    <div>
                        <h5 class="shop-card-title">Metode Pembayaran</h5>
                        <span class="shop-card-subtitle">Pembayaran diproses menggunakan Midtrans.</span>
                    </div>
                    <span class="shop-step-badge">2</span>
                </div>

                <div class="shop-card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group shop-field">
                                <label class="required-label">Metode Pembayaran</label>
                                <select id="metode_pembayaran" class="form-control" required>
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="Midtrans">Midtrans</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6" id="wrap_bank_tujuan" style="display: none;">
                            <div class="form-group shop-field">
                                <label>Bank Tujuan</label>
                                <select id="bank_tujuan" class="form-control">
                                    <option value="">Pilih Rekening</option>

                                    @foreach($rekeningPembayaran->where('metode_pembayaran', 'Transfer Bank') as $rekening)
                                        <option
                                            value="{{ $rekening->nama_bank }}"
                                            data-bank="{{ $rekening->nama_bank }}"
                                            data-rekening="{{ $rekening->nomor_rekening }}"
                                            data-atas-nama="{{ $rekening->atas_nama }}"
                                        >
                                            {{ $rekening->nama_bank }} - {{ $rekening->nomor_rekening }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="info_transfer_bank" class="payment-info-box" style="display: none;">
                        <h6>Informasi Transfer</h6>
                        <p class="mb-1">Bank: <strong id="info_bank">-</strong></p>
                        <p class="mb-1">Nomor Rekening: <strong id="info_rekening">-</strong></p>
                        <p class="mb-0">Atas Nama: <strong id="info_atas_nama">-</strong></p>
                    </div>

                    <div id="info_qris" class="payment-info-box" style="display: none;">
                        <h6>Pembayaran QRIS</h6>

                        @php
                            $qris = $rekeningPembayaran->firstWhere('metode_pembayaran', 'QRIS');
                        @endphp

                        @if($qris && $qris->gambar_qris)
                            <img
                                src="{{ asset($qris->gambar_qris) }}"
                                alt="QRIS CV Syavir Jaya Utama"
                                style="max-width: 240px; width: 100%; border-radius: 12px; border: 1px solid #e8edf5;"
                            >
                            <p class="mt-2 mb-0 text-muted">
                                Scan QRIS untuk melakukan pembayaran.
                            </p>
                        @else
                            <p class="mb-0 text-muted">
                                Gambar QRIS belum tersedia.
                            </p>
                        @endif
                    </div>

                    <div id="info_cash" class="payment-info-box" style="display: none;">
                        <h6>Pembayaran Cash</h6>
                        <p class="mb-0 text-muted">
                            Pembayaran dilakukan secara tunai kepada admin atau pegawai yang bertugas.
                        </p>
                    </div>
                </div>
            </div>

            <div class="shop-card">
                <div class="shop-card-header">
                    <div>
                        <h5 class="shop-card-title">Pilih Produk</h5>
                        <span class="shop-card-subtitle">Tambahkan barang ke keranjang pesanan.</span>
                    </div>
                    <span class="shop-step-badge">3</span>
                </div>

                <div class="shop-card-body">
                    <div class="shop-product-pick">
                        <h6 class="shop-product-pick-title">Cari dan pilih barang</h6>

                        <div class="shop-product-grid">
                            <div class="shop-field col-product-category">
                                <label>Pilih Kategori Barang</label>
                                <select id="detail_kategori_barang_penjualan" class="form-control">
                                    <option value="">Semua Kategori</option>

                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->kode_kategori }}">
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="shop-field col-product-code">
                                <label class="required-label">Pilih Kode Barang</label>
                                <select
                                    id="detail_kode_barang_penjualan"
                                    class="form-control select-barang-custom"
                                    required
                                >
                                    <option value="">Pilih Kode Barang</option>

                                    @foreach($barangs as $barang)
                                        <option
                                            value="{{ $barang->kode_barang }}"
                                            data-kode="{{ $barang->kode_barang }}"
                                            data-nama="{{ $barang->nama_barang }}"
                                            data-kategori="{{ $barang->kode_kategori }}"
                                            data-nama-kategori="{{ $barang->nama_kategori }}"
                                            data-kapasitas="{{ $barang->kapasitas }}"
                                            data-harga="{{ $barang->harga_jual }}"
                                            {{ (float) $barang->kapasitas <= 0 ? 'disabled' : '' }}
                                        >
                                            {{ $barang->kode_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="shop-field col-product-name">
                                <label class="required-label">Pilih Nama Barang</label>
                                <select
                                    id="detail_nama_barang_penjualan"
                                    class="form-control select-barang-custom"
                                    required
                                >
                                    <option value="">Pilih Nama Barang</option>

                                    @foreach($barangs as $barang)
                                        <option
                                            value="{{ $barang->nama_barang }}"
                                            data-kode="{{ $barang->kode_barang }}"
                                            data-nama="{{ $barang->nama_barang }}"
                                            data-kategori="{{ $barang->kode_kategori }}"
                                            data-nama-kategori="{{ $barang->nama_kategori }}"
                                            data-kapasitas="{{ $barang->kapasitas }}"
                                            data-harga="{{ $barang->harga_jual }}"
                                            {{ (float) $barang->kapasitas <= 0 ? 'disabled' : '' }}
                                        >
                                            {{ $barang->nama_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="shop-field col-product-qty">
                                <label class="required-label">Qty</label>
                                <input
                                    type="number"
                                    id="detail_qty_penjualan"
                                    class="form-control"
                                    min="1"
                                    step="1"
                                    placeholder="Qty"
                                    required
                                >
                            </div>

                            <div class="shop-field col-product-price">
                                <label class="required-label">Harga Satuan</label>
                                <input
                                    type="number"
                                    id="detail_harga_satuan_penjualan"
                                    class="form-control soft-readonly"
                                    min="0"
                                    step="0.01"
                                    placeholder="Harga otomatis"
                                    readonly
                                    required
                                >
                            </div>
                        </div>

                        <div class="shop-add-product">
                            <button
                                type="button"
                                id="btnTambahDetailPenjualan"
                                class="btn btn-info btn-soft-primary"
                            >
                                Tambah ke Keranjang
                            </button>
                        </div>
                    </div>

                    <div class="shop-cart-table mt-4">
                        <div class="table-responsive">
                            <table class="table table-hover" id="detailTablePenjualan">
                                <thead>
                                    <tr>
                                        <th width="60">No</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th width="100">Qty</th>
                                        <th width="150">Harga Satuan</th>
                                        <th width="170">Subtotal</th>
                                        <th width="140">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="detailTableBodyPenjualan">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            Belum ada item penjualan.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <input type="hidden" id="total_barang_penjualan" value="0">
                    <input type="hidden" id="grand_total_penjualan" value="0">
                </div>
            </div>
        </div>

        <div class="shop-side">
            <div class="shop-summary-card">
                <div class="shop-summary-header">
                    <h5>Ringkasan Belanja</h5>
                    <small>Pastikan produk dan alamat sudah benar sebelum membayar.</small>
                </div>

                <div class="shop-summary-body">
                    <div class="shop-summary-line">
                        <span>Total Item</span>
                        <span id="total_item_penjualan_text">0</span>
                    </div>

                    <div class="shop-summary-line">
                        <span>Total Barang</span>
                        <span id="total_barang_penjualan_text">0</span>
                    </div>

                    <div class="shop-summary-line">
                        <span>Ongkir</span>
                        <span>Rp<span id="shop_ongkir_preview">0</span></span>
                    </div>

                    <div class="shop-summary-total">
                        <span>Grand Total</span>
                        <span>Rp<span id="grand_total_penjualan_text">0</span></span>
                    </div>

                    <div class="shop-actions">
                        <button type="button" id="btnSavePenjualan" class="btn btn-shop-primary">
                            Bayar Sekarang
                        </button>

                        <button type="button" id="btnResetPenjualan" class="btn btn-shop-outline">
                            Kosongkan Form
                        </button>

                        <button type="button" id="btnViewPenjualan" class="btn btn-shop-outline">
                            Lihat Data Transaksi
                        </button>
                    </div>

                    <div class="shop-mini-note">
                        Pembayaran akan diarahkan ke popup Midtrans. Jika pembayaran ditutup,
                        transaksi dapat dilanjutkan dari halaman riwayat.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="modalViewPenjualan"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalViewPenjualanLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewPenjualanLabel">
                        Data Transaksi Penjualan
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableViewPenjualan" width="100%">
                            <thead>
                                <tr>
                                    <th>Kode Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Kode Customer</th>
                                    <th>Customer</th>
                                    <th>Jenis Pengiriman</th>
                                    <th>Status Pesanan</th>
                                    <th>Status Pembayaran</th>
                                    <th>Grand Total</th>
                                    <th width="220">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="modalUploadBuktiPenjualan"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalUploadBuktiPenjualanLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog" role="document">
            <form id="formUploadBuktiPenjualan" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="upload_kode_pesanan" name="kode_pesanan">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUploadBuktiPenjualanLabel">
                            Upload Bukti Pembayaran
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Unggah bukti pembayaran untuk transaksi
                            <strong id="upload_kode_pesanan_text">-</strong>.
                        </p>

                        <div class="form-group">
                            <label class="required-label">Bukti Pembayaran</label>
                            <input
                                type="file"
                                name="bukti_pembayaran"
                                id="bukti_pembayaran"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf"
                                required
                            >
                            <small class="text-muted">
                                Format: JPG, JPEG, PNG, atau PDF. Maksimal 2 MB.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success">
                            Upload Bukti
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.transaksiPenjualanConfig = {
        barangList: @json($barangs),
        customerName: "{{ $customerAktif->nama_customer ?? '' }}",
        customerAktifKode: "{{ $customerAktif->kode_customer ?? '' }}",
        requireCustomerSelection: @json(in_array($user->kode_role, ['KRL001', 'KRL002'])),
        isAdmin: @json(in_array($user->kode_role, ['KRL001', 'KRL002'])),
        storeUrl: "{{ route('ajax.transaksi.penjualan.store') }}",
        reloadUrl: "{{ route('ajax.transaksi.penjualan') }}",
        dataUrl: "{{ route('ajax.transaksi.penjualan.data') }}",
        showUrlBase: "{{ url('/ajax/transaksi/penjualan/show') }}",
        updateUrlBase: "{{ url('/ajax/transaksi/penjualan/update') }}",
        deleteUrlBase: "{{ url('/ajax/transaksi/penjualan/delete') }}",
        uploadBuktiUrlBase: "{{ url('/ajax/transaksi/penjualan/upload-bukti') }}",
        validasiPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/validasi-pembayaran') }}",
        tolakPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/tolak-pembayaran') }}",
        updateStatusUrlBase: "{{ url('/ajax/transaksi/penjualan/update-status') }}",
        searchDestinationUrl: "{{ route('ajax.rajaongkir.search_destination') }}",
        checkOngkirUrl: "{{ route('ajax.rajaongkir.check_ongkir') }}",
        defaultDate: "{{ date('Y-m-d') }}",
        csrfToken: "{{ csrf_token() }}",
        midtransClientKey: "{{ config('midtrans.client_key') }}",
        midtransTokenUrlBase: "{{ url('/ajax/transaksi/penjualan/midtrans-token') }}",
        syncMidtransStatusUrlBase: "{{ url('/ajax/transaksi/penjualan/sync-midtrans-status') }}",
        riwayatPenjualanUrl: "{{ route('ajax.transaksi.riwayat.penjualan') }}",
    };

    if (typeof initTransaksiPenjualan === 'function') {
        initTransaksiPenjualan(window.transaksiPenjualanConfig);
    }

    $(document)
        .off('input change', '#ongkir_pesanan')
        .on('input change', '#ongkir_pesanan', function () {
            $('#shop_ongkir_preview').text(
                new Intl.NumberFormat('id-ID').format(parseFloat($(this).val()) || 0)
            );
        });

    const observerOngkir = new MutationObserver(function () {
        $('#shop_ongkir_preview').text(
            new Intl.NumberFormat('id-ID').format(parseFloat($('#ongkir_pesanan').val()) || 0)
        );
    });

    if (document.getElementById('ongkir_pesanan')) {
        observerOngkir.observe(document.getElementById('ongkir_pesanan'), {
            attributes: true,
            childList: false,
            subtree: false,
        });
    }
</script>