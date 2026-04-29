<div class="container-fluid">
    <div id="retur-penjualan-alert"></div>

    <input type="hidden" id="form_mode_retur_penjualan" value="create">

    <div class="purchase-card">
        <div class="card-header">
            Form Retur Penjualan
        </div>

        <div class="card-body">
            <form id="formReturPenjualan">
                @csrf

                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Retur Penjualan</label>
                            <input type="text" id="kode_rpenjualan_preview" class="form-control soft-readonly" value="{{ $kodePreview }}" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Tanggal Retur</label>
                            <input type="date" id="tgl_penjualan_retur" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Penjualan</label>
                            <select id="kode_pesanan_retur" class="form-control">
                                <option value="">Pilih Kode Penjualan</option>
                                @foreach($penjualans as $penjualan)
                                    <option value="{{ $penjualan->kode_pesanan }}">
                                        {{ $penjualan->kode_pesanan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Customer</label>
                            <input type="text" id="nama_customer_retur" class="form-control soft-readonly" readonly>
                            <input type="hidden" id="kode_customer_retur">
                        </div>
                    </div>

                    <div class="col-lg-8 col-md-6">
                        <div class="form-group">
                            <label>Alamat Customer</label>
                            <input type="text" id="alamat_retur_penjualan" class="form-control soft-readonly" readonly>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group mb-0">
                            <label>Keterangan</label>
                            <textarea id="keterangan_retur_penjualan" rows="3" class="form-control" placeholder="Masukkan keterangan retur penjualan"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="purchase-card">
        <div class="card-header">
            Detail Barang Retur Penjualan
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Kode Barang</label>
                        <select id="detail_kode_barang_retur_penjualan" class="form-control select-barang-custom">
                            <option value="">Pilih Kode Barang</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Nama Barang</label>
                        <select id="detail_nama_barang_retur_penjualan" class="form-control select-barang-custom">
                            <option value="">Pilih Nama Barang</option>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty Jual</label>
                        <input type="number" id="detail_qty_jual_retur_penjualan" class="form-control soft-readonly" readonly>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty Retur</label>
                        <input type="number" id="detail_qty_retur_penjualan" class="form-control" min="1" step="1" placeholder="Qty Retur">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" id="detail_harga_retur_penjualan" class="form-control soft-readonly" readonly>
                    </div>
                </div>

                <div class="col-lg-12 col-md-4 mt-2">
                    <button type="button" id="btnTambahDetailReturPenjualan" class="btn btn-info btn-soft-primary">
                        Tambah Item
                    </button>
                </div>
            </div>

            <div class="detail-table-wrapper mt-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="detailTableReturPenjualan">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="100">Qty Jual</th>
                                <th width="100">Qty Retur</th>
                                <th width="150">Harga</th>
                                <th width="170">Subtotal</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBodyReturPenjualan">
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Belum ada item retur penjualan.
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
                            <span class="summary-value" id="total_item_retur_penjualan_text">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="summary-label">Total Retur</span>
                            <span class="summary-value" id="total_retur_penjualan_text">0</span>
                        </div>

                        <input type="hidden" id="total_retur_penjualan" value="0">
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap" style="gap: 10px;">
                <button type="button" id="btnSaveReturPenjualan" class="btn btn-success btn-soft-primary">
                    Save Data
                </button>

                <button type="button" id="btnResetReturPenjualan" class="btn btn-primary btn-soft-primary">
                    New Data
                </button>

                <!-- <button type="button" id="btnViewReturPenjualan" class="btn btn-secondary btn-soft-primary">
                    View Data
                </button> -->
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewReturPenjualan" tabindex="-1" role="dialog" aria-labelledby="modalViewReturPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewReturPenjualanLabel">Data Retur Penjualan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableViewReturPenjualan" width="100%">
                            <thead>
                                <tr>
                                    <th>Kode Retur</th>
                                    <th>Tanggal</th>
                                    <th>Kode Penjualan</th>
                                    <th>Customer</th>
                                    <th>Alamat</th>
                                    <th>Keterangan</th>
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
    window.returPenjualanConfig = {
        storeUrl: "{{ route('ajax.transaksi.retur.penjualan.store') }}",
        reloadUrl: "{{ route('ajax.transaksi.retur.penjualan') }}",
        dataUrl: "{{ route('ajax.transaksi.retur.penjualan.data') }}",
        detailPenjualanUrlBase: "{{ url('/ajax/transaksi/retur-penjualan/penjualan') }}",
        defaultDate: "{{ date('Y-m-d') }}",
        csrfToken: "{{ csrf_token() }}"
    };

    if (typeof initReturPenjualan === 'function') {
        initReturPenjualan(window.returPenjualanConfig);
    }
</script>