<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800 no-print">Laporan Penjualan</h1>

    <div class="card shadow mb-4 no-print">
        <div class="card-body">
            <form id="formFilterLaporanPenjualan" method="GET" action="{{ route('ajax.laporan.penjualan') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input 
                            type="date" 
                            name="tanggal_awal" 
                            id="tanggal_awal" 
                            class="form-control"
                            value="{{ $tanggalAwal ?? '' }}"
                        >
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input 
                            type="date" 
                            name="tanggal_akhir" 
                            id="tanggal_akhir" 
                            class="form-control"
                            value="{{ $tanggalAkhir ?? '' }}"
                        >
                    </div>

                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>

                        <button type="button" class="btn btn-secondary" onclick="resetFilterLaporanPenjualan()">
                            <i class="fas fa-sync-alt"></i> Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">

            <div id="printArea">
                <div class="print-header">
                    <h3>Laporan Penjualan</h3>
                    <p>CV. Syavir Jaya Utama</p>

                    @if(!empty($tanggalAwal) && !empty($tanggalAkhir))
                        <p>Periode: {{ $tanggalAwal }} sampai {{ $tanggalAkhir }}</p>
                    @else
                        <p>Periode: Semua Data</p>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tableLaporanPenjualan" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pesanan</th>
                                <th>Tanggal</th>
                                <th>Nama Customer</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Qty</th>
                                <th>Harga Satuan</th>
                                <th>Subtotal Pesanan</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kode_pesanan }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->nama_customer }}</td>
                                    <td>{{ $item->kode_barang }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->subtotal_pesanan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="8" class="text-right">Total Penjualan</th>
                                <th>Rp {{ number_format($data->sum('subtotal_pesanan'), 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 no-print">
                <button type="button" class="btn btn-primary btn-sm" onclick="printLaporanPenjualan()">
                    <i class="fas fa-print"></i> Print
                </button>

                <button type="button" class="btn btn-success btn-sm" onclick="exportLaporanPenjualanExcel()">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    .print-header {
        display: none;
    }

    @media print {
        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 10px;
        }

        .print-header {
            display: block;
            text-align: center;
            margin-bottom: 12px;
        }

        .print-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }

        .print-header p {
            margin: 4px 0;
            font-size: 12px;
        }

        .no-print,
        .dt-length,
        .dt-search,
        .dt-info,
        .dt-paging,
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate,
        .pagination,
        .page-item,
        .page-link {
            display: none !important;
            visibility: hidden !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 10px;
        }

        table th,
        table td {
            border: 1px solid #000 !important;
            padding: 5px !important;
            color: #000 !important;
            vertical-align: middle !important;
        }

        table th {
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right !important;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }
    }
</style>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#tableLaporanPenjualan')) {
            $('#tableLaporanPenjualan').DataTable().destroy();
        }

        $('#tableLaporanPenjualan').DataTable();
    });

    $('#formFilterLaporanPenjualan').on('submit', function (e) {
        e.preventDefault();

        let tanggalAwal = $('#tanggal_awal').val();
        let tanggalAkhir = $('#tanggal_akhir').val();

        if ((tanggalAwal && !tanggalAkhir) || (!tanggalAwal && tanggalAkhir)) {
            alert('Tanggal awal dan tanggal akhir harus diisi keduanya.');
            return;
        }

        if (tanggalAwal && tanggalAkhir && tanggalAwal > tanggalAkhir) {
            alert('Tanggal awal tidak boleh lebih besar dari tanggal akhir.');
            return;
        }

        let url = "{{ route('ajax.laporan.penjualan') }}";

        if (tanggalAwal && tanggalAkhir) {
            url += '?tanggal_awal=' + encodeURIComponent(tanggalAwal) + '&tanggal_akhir=' + encodeURIComponent(tanggalAkhir);
        }

        loadContent(url);
    });

    function resetFilterLaporanPenjualan() {
        loadContent("{{ route('ajax.laporan.penjualan') }}");
    }

    function printLaporanPenjualan() {
        window.print();
    }

    function exportLaporanPenjualanExcel() {
        let table = document.getElementById('tableLaporanPenjualan').cloneNode(true);

        let periode = 'Semua Data';
        let tanggalAwal = "{{ $tanggalAwal ?? '' }}";
        let tanggalAkhir = "{{ $tanggalAkhir ?? '' }}";

        if (tanggalAwal !== '' && tanggalAkhir !== '') {
            periode = tanggalAwal + ' sampai ' + tanggalAkhir;
        }

        let html = `
            <html>
                <head>
                    <meta charset="UTF-8">
                </head>
                <body>
                    <h3>Laporan Penjualan</h3>
                    <p>CV. Syavir Jaya Utama</p>
                    <p>Periode: ${periode}</p>

                    <table border="1">
                        ${table.innerHTML}
                    </table>
                </body>
            </html>
        `;

        let blob = new Blob([html], {
            type: 'application/vnd.ms-excel'
        });

        let url = URL.createObjectURL(blob);
        let link = document.createElement('a');

        link.href = url;
        link.download = 'laporan_penjualan.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>