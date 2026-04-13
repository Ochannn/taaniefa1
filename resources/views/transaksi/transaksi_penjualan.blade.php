<div class="container-fluid">
    <div id="sales-alert"></div>

    <input type="hidden" id="form_mode_penjualan" value="create">
    <input type="hidden" id="edit_kode_pesanan" value="">

    <div class="purchase-card">
        <div class="card-header">
            Form Transaksi Penjualan
        </div>
        <div class="card-body">
            <form id="formPenjualan">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Pesanan</label>
                            <input type="text" id="kode_pesanan_preview" class="form-control soft-readonly" value="{{ $kodePreview }}" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Tanggal Pesanan</label>
                            <input type="date" id="tgl_pesanan" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    
                    <div class="col-lg-4 col-md-6">
                        @if($user->kode_role === 'KRL001' || $user->kode_role === 'KRL002')
                            <div class="form-group">
                                <label for="kode_customer">Customer</label>
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
                            <div class="form-group">
                                <label>Customer</label>
                                <input type="text" class="form-control" value="{{ $customerAktif->nama_customer ?? '' }}" readonly>
                                <input type="hidden" id="kode_customer" name="kode_customer" value="{{ $customerAktif->kode_customer ?? '' }}">
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Jenis Pesanan</label>
                            <select id="jenis_pesanan" class="form-control">
                                <option value="">Pilih Jenis Pesanan</option>
                                <option value="Reguler">Reguler</option>
                                <option value="Express">Express</option>
                                <option value="Preorder">Preorder</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Ongkir Pesanan</label>
                            <input type="number" id="ongkir_pesanan" class="form-control soft-readonly" min="0" step="1" value="0" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Status Pesanan</label>

                            @if($user->kode_role === 'KRL001' || $user->kode_role === 'KRL002')
                                <select id="status_pesanan" class="form-control">
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
                                <small class="text-muted">Status awal customer selalu Pending dan hanya dapat diubah oleh admin.</small>
                            @endif
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Alamat Kirim Pesanan</label>
                            <textarea id="alamat_kirim_pesanan" rows="3" class="form-control" placeholder="Masukkan alamat pengiriman"></textarea>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Catatan Pesanan</label>
                            <textarea id="catatan_pesanan" rows="3" class="form-control" placeholder="Masukkan catatan pesanan jika diperlukan"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="purchase-card">
        <div class="card-header">
            Detail Barang Penjualan
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Kode Barang</label>
                        <select id="detail_kode_barang_penjualan" class="form-control">
                            <option value="">Pilih Kode Barang</option>
                            @foreach($barangs as $barang)
                                <option
                                    value="{{ $barang->kode_barang }}"
                                    data-kode="{{ $barang->kode_barang }}"
                                    data-nama="{{ $barang->nama_barang }}"
                                    data-kapasitas="{{ $barang->kapasitas }}"
                                    data-harga="{{ $barang->harga_jual }}"
                                    {{ (float)$barang->kapasitas <= 0 ? 'disabled' : '' }}
                                >
                                    {{ $barang->kode_barang }} - {{ (float)$barang->kapasitas <= 0 ? 'Stok tidak tersedia' : $barang->kapasitas }} - Rp.{{ number_format($barang->harga_jual, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Nama Barang</label>
                        <select id="detail_nama_barang_penjualan" class="form-control">
                            <option value="">Pilih Nama Barang</option>
                            @foreach($barangs as $barang)
                                <option
                                    value="{{ $barang->nama_barang }}"
                                    data-kode="{{ $barang->kode_barang }}"
                                    data-nama="{{ $barang->nama_barang }}"
                                    data-kapasitas="{{ $barang->kapasitas }}"
                                    data-harga="{{ $barang->harga_jual }}"
                                    {{ (float)$barang->kapasitas <= 0 ? 'disabled' : '' }}
                                >
                                    {{ $barang->nama_barang }} - {{ (float)$barang->kapasitas <= 0 ? 'Stok tidak tersedia' : $barang->kapasitas }} - Rp.{{ number_format($barang->harga_jual, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty</label>
                        <input type="number" id="detail_qty_penjualan" class="form-control" min="1" step="1" placeholder="Qty">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Harga Satuan</label>
                        <input type="number" id="detail_harga_satuan_penjualan" class="form-control soft-readonly" min="0" step="0.01" placeholder="Harga otomatis" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <button type="button" id="btnTambahDetailPenjualan" class="btn btn-info btn-block btn-soft-primary">
                        Tambah Item
                    </button>
                </div>
            </div>

            <div class="detail-table-wrapper mt-3">
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

            <div class="row mt-4">
                <div class="col-lg-6"></div>
                <div class="col-lg-6">
                    <div class="summary-box">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="summary-label">Total Item</span>
                            <span class="summary-value" id="total_item_penjualan_text">0</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="summary-label">Total Barang</span>
                            <span class="summary-value" id="total_barang_penjualan_text">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="summary-label">Grand Total</span>
                            <span class="summary-value" id="grand_total_penjualan_text">0</span>
                        </div>
                        <input type="hidden" id="total_barang_penjualan" value="0">
                        <input type="hidden" id="grand_total_penjualan" value="0">
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap" style="gap: 10px;">
                <button type="button" id="btnSavePenjualan" class="btn btn-success btn-soft-primary">
                    Save Data
                </button>
                <button type="button" id="btnResetPenjualan" class="btn btn-primary btn-soft-primary">
                    New Data
                </button>
                <button type="button" id="btnViewPenjualan" class="btn btn-secondary btn-soft-primary">
                    View Data
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewPenjualan" tabindex="-1" role="dialog" aria-labelledby="modalViewPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewPenjualanLabel">Data Transaksi Penjualan</h5>
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
                                    <th>Jenis Pesanan</th>
                                    <th>Status</th>
                                    <th width="160">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
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
        defaultDate: "{{ date('Y-m-d') }}",
        csrfToken: "{{ csrf_token() }}"
    };

    if (typeof initTransaksiPenjualan === 'function') {
        initTransaksiPenjualan(window.transaksiPenjualanConfig);
    }
</script>