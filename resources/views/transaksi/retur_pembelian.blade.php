<div class="container-fluid">
    <div id="retur-pembelian-alert"></div>

    <input type="hidden" id="form_mode_retur_pembelian" value="create">

    <div class="purchase-card">
        <div class="card-header">
            Form Retur Pembelian
        </div>

        <div class="card-body">
            <form id="formReturPembelian">
                @csrf

                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Retur Pembelian</label>
                            <input type="text" id="kode_rpembelian_preview" class="form-control soft-readonly" value="{{ $kodePreview }}" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Tanggal Retur</label>
                            <input type="date" id="tgl_pembelian_retur" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Pembelian</label>
                            <select id="kode_pembelian_retur" class="form-control">
                                <option value="">Pilih Kode Pembelian</option>
                                @foreach($pembelians as $pembelian)
                                    <option value="{{ $pembelian->kode_pembelian }}">
                                        {{ $pembelian->kode_pembelian }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Supplier</label>
                            <input type="text" id="nama_supplier_retur" class="form-control soft-readonly" readonly>
                            <input type="hidden" id="kode_supplier_retur">
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6">
                        <div class="form-group">
                            <label>Alamat Supplier</label>
                            <input type="text" id="alamat_retur" class="form-control soft-readonly" readonly>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group mb-0">
                            <label>Keterangan</label>
                            <textarea id="keterangan_retur" rows="3" class="form-control" placeholder="Masukkan keterangan retur pembelian"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="purchase-card">
        <div class="card-header">
            Detail Barang Retur Pembelian
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Kode Barang</label>
                        <select id="detail_kode_barang_retur_pembelian" class="form-control select-barang-custom">
                            <option value="">Pilih Kode Barang</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Nama Barang</label>
                        <select id="detail_nama_barang_retur_pembelian" class="form-control select-barang-custom">
                            <option value="">Pilih Nama Barang</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty Beli</label>
                        <input type="number" id="detail_qty_beli_retur_pembelian" class="form-control soft-readonly" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty Retur</label>
                        <input type="number" id="detail_qty_retur_pembelian" class="form-control" min="1" step="1" placeholder="Qty Retur">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" id="detail_harga_retur_pembelian" class="form-control soft-readonly" readonly>
                    </div>
                </div>

                <div class="col-lg-12 col-md-4 mt-2">
                    <button type="button" id="btnTambahDetailReturPembelian" class="btn btn-info btn-soft-primary">
                        Tambah Item
                    </button>
                </div>
            </div>

            <div class="detail-table-wrapper mt-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="detailTableReturPembelian">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="100">Qty Beli</th>
                                <th width="100">Qty Retur</th>
                                <th width="150">Harga</th>
                                <th width="170">Subtotal</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBodyReturPembelian">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada item retur pembelian.
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
                            <span class="summary-value" id="total_item_retur_pembelian_text">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="summary-label">Total Retur</span>
                            <span class="summary-value" id="total_retur_pembelian_text">0</span>
                        </div>

                        <input type="hidden" id="total_retur_pembelian" value="0">
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap" style="gap: 10px;">
                <button type="button" id="btnSaveReturPembelian" class="btn btn-success btn-soft-primary">
                    Save Data
                </button>

                <button type="button" id="btnResetReturPembelian" class="btn btn-primary btn-soft-primary">
                    New Data
                </button>

                <!-- <button type="button" id="btnViewReturPembelian" class="btn btn-secondary btn-soft-primary">
                    View Data
                </button> -->
            </div>
        </div>
    </div>
</div>

<script>
    window.returPembelianConfig = {
        storeUrl: "{{ route('ajax.transaksi.retur.pembelian.store') }}",
        reloadUrl: "{{ route('ajax.transaksi.retur.pembelian') }}",
        dataUrl: "{{ route('ajax.transaksi.retur.pembelian.data') }}",
        detailPembelianUrlBase: "{{ url('/ajax/transaksi/retur-pembelian/pembelian') }}",
        defaultDate: "{{ date('Y-m-d') }}",
        csrfToken: "{{ csrf_token() }}"
    };

    if (typeof initReturPembelian === 'function') {
        initReturPembelian(window.returPembelianConfig);
    }
</script>