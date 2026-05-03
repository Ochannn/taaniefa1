<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800 no-print">Laporan Stok Barang</h1>

    <div class="card shadow mb-4">
        <div class="card-body">

            <div id="printAreaStokBarang">
                <div class="print-header">
                    <h3>Laporan Stok Barang</h3>
                    <p>CV. Syavir Jaya Utama</p>
                    <p>Periode: Semua Data</p>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tableLaporanStokBarang" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Stok Awal</th>
                                <th>Stok Masuk</th>
                                <th>Stok Keluar</th>
                                <th>Stok Akhir</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->kode_barang }}</td>
                                    <td>{{ $item->nama_barang }}</td>
                                    <td>{{ $item->stok_awal }}</td>
                                    <td>{{ $item->stok_masuk }}</td>
                                    <td>{{ $item->stok_keluar }}</td>
                                    <td>{{ $item->stok_akhir }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th>{{ $data->sum('stok_awal') }}</th>
                                <th>{{ $data->sum('stok_masuk') }}</th>
                                <th>{{ $data->sum('stok_keluar') }}</th>
                                <th>{{ $data->sum('stok_akhir') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="mt-3 no-print">
                <button type="button" class="btn btn-primary btn-sm" onclick="printLaporanStokBarang()">
                    <i class="fas fa-print"></i> Print
                </button>

                <button type="button" class="btn btn-success btn-sm" onclick="exportLaporanStokBarangExcel()">
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

        #printAreaStokBarang,
        #printAreaStokBarang * {
            visibility: visible;
        }

        #printAreaStokBarang {
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

        .dt-container,
        .dataTables_wrapper {
            display: block !important;
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
        if ($.fn.DataTable.isDataTable('#tableLaporanStokBarang')) {
            $('#tableLaporanStokBarang').DataTable().destroy();
        }

        $('#tableLaporanStokBarang').DataTable();
    });

    function printLaporanStokBarang() {
        window.print();
    }

    function exportLaporanStokBarangExcel() {
        let table = document.getElementById('tableLaporanStokBarang').cloneNode(true);

        let html = `
            <html>
                <head>
                    <meta charset="UTF-8">
                </head>
                <body>
                    <h3>Laporan Stok Barang</h3>
                    <p>CV. Syavir Jaya Utama</p>
                    <p>Periode: Semua Data</p>

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
        link.download = 'laporan_stok_barang.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>