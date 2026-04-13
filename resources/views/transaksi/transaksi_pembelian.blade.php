<div class="container-fluid">
    <div id="purchase-alert"></div>

    <input type="hidden" id="form_mode" value="create">
    <input type="hidden" id="edit_kode_pembelian" value="">

    <div class="purchase-card">
        <div class="card-header">
            Form Transaksi Pembelian
        </div>
        <div class="card-body">
            <form id="formPembelian">
                @csrf
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Kode Beli</label>
                            <input type="text" id="kode_pembelian_preview" class="form-control soft-readonly" value="{{ $kodePreview }}" readonly>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="form-group">
                            <label>Tanggal Pembelian</label>
                            <input type="date" id="tgl_pembelian" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <div class="form-group">
                            <label>Supplier</label>
                            <select id="kode_supplier" class="form-control">
                                <option value="">Pilih Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->kode_supplier }}">
                                        {{ $supplier->kode_supplier }} - {{ $supplier->nama_supplier }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group mb-0">
                            <label>Catatan Pembelian</label>
                            <textarea id="catatan_pembelian" rows="3" class="form-control" placeholder="Masukkan catatan pembelian jika diperlukan"></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="purchase-card">
        <div class="card-header">
            Detail Barang Pembelian
        </div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Kode Barang</label>
                        <select id="detail_kode_barang" class="form-control">
                            <option value="">Pilih Kode Barang</option>
                            @foreach($barangs as $barang)
                                <option
                                    value="{{ $barang->kode_barang }}"
                                    data-kode="{{ $barang->kode_barang }}"
                                    data-nama="{{ $barang->nama_barang }}"
                                    data-kapasitas="{{ $barang->kapasitas }}"
                                >
                                    {{ $barang->kode_barang }} - {{ $barang->kapasitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="form-group">
                        <label>Pilih Nama Barang</label>
                        <select id="detail_nama_barang" class="form-control">
                            <option value="">Pilih Nama Barang</option>
                            @foreach($barangs as $barang)
                                <option
                                    value="{{ $barang->nama_barang }}"
                                    data-kode="{{ $barang->kode_barang }}"
                                    data-nama="{{ $barang->nama_barang }}"
                                    data-kapasitas="{{ $barang->kapasitas }}"
                                >
                                    {{ $barang->nama_barang }} - {{ $barang->kapasitas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Qty</label>
                        <input type="number" id="detail_qty" class="form-control" min="1" step="1" placeholder="Qty">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <div class="form-group">
                        <label>Harga</label>
                        <input type="number" id="detail_harga" class="form-control" min="0" step="0.01" placeholder="Harga">
                    </div>
                </div>

                <div class="col-lg-2 col-md-4">
                    <button type="button" id="btnTambahDetail" class="btn btn-info btn-block btn-soft-primary">
                        Tambah Item
                    </button>
                </div>
            </div>

            <div class="detail-table-wrapper mt-3">
                <div class="table-responsive">
                    <table class="table table-hover" id="detailTable">
                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th width="100">Qty</th>
                                <th width="150">Harga</th>
                                <th width="170">Subtotal</th>
                                <th width="140">Action</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada item pembelian.
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
                            <span class="summary-value" id="total_item_text">0</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="summary-label">Total Pembelian</span>
                            <span class="summary-value" id="total_pembelian_text">0</span>
                        </div>
                        <input type="hidden" id="total_pembelian" value="0">
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap" style="gap: 10px;">
                <button type="button" id="btnSavePembelian" class="btn btn-success btn-soft-primary">
                    Save Data
                </button>
                <button type="button" id="btnResetPembelian" class="btn btn-primary btn-soft-primary">
                    New Data
                </button>
                <button type="button" id="btnViewPembelian" class="btn btn-secondary btn-soft-primary">
                    View Data
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalViewPembelian" tabindex="-1" role="dialog" aria-labelledby="modalViewPembelianLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalViewPembelianLabel">Data Transaksi Pembelian</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tableViewPembelian" width="100%">
                            <thead>
                                <tr>
                                    <th>Kode Beli</th>
                                    <th>Tanggal</th>
                                    <th>Kode Supplier</th>
                                    <th>Supplier</th>
                                    <th>Keterangan</th>
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
    window.transaksiPembelianConfig = {
        barangList: @json($barangs),
        storeUrl: "{{ route('ajax.transaksi.pembelian.store') }}",
        reloadUrl: "{{ route('ajax.transaksi.pembelian') }}",
        dataUrl: "{{ route('ajax.transaksi.pembelian.data') }}",
        showUrlBase: "{{ url('/ajax/transaksi/pembelian/show') }}",
        updateUrlBase: "{{ url('/ajax/transaksi/pembelian/update') }}",
        deleteUrlBase: "{{ url('/ajax/transaksi/pembelian/delete') }}",
        defaultDate: "{{ date('Y-m-d') }}",
        csrfToken: "{{ csrf_token() }}"
    };

    if (typeof initTransaksiPembelian === 'function') {
        initTransaksiPembelian(window.transaksiPembelianConfig);
    }
</script>