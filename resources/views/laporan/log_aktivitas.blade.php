<div class="container-fluid">
    <h1 class="h3 mb-3 text-gray-800 no-print">Log Aktivitas</h1>

    <div class="card shadow mb-4 no-print">
        <div class="card-body">
            <form id="formFilterLogAktivitas" method="GET" action="{{ route('ajax.laporan.log.aktivitas') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_awal">Tanggal Awal</label>
                        <input type="date" name="tanggal_awal" id="tanggal_awal" class="form-control" value="{{ $tanggalAwal ?? '' }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="tanggal_akhir">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir ?? '' }}">
                    </div>

                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            <i class="fas fa-search"></i> Tampilkan
                        </button>

                        <button type="button" class="btn btn-secondary" onclick="resetFilterLogAktivitas()">
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
                    <h3>Log Aktivitas</h3>
                    <p>CV. Syavir Jaya Utama</p>

                    @if(!empty($tanggalAwal) && !empty($tanggalAkhir))
                        <p>Periode: {{ $tanggalAwal }} sampai {{ $tanggalAkhir }}</p>
                    @else
                        <p>Periode: Semua Data</p>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="tableLogAktivitas" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Reference Table</th>
                                <th>ID Reference</th>
                                <th>Raw Original</th>
                                <th>Raw Changes</th>
                                <th>Raw New</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($data as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->created_at }}</td>
                                    <td>{{ $item->nama_user ?? '-' }}</td>
                                    <td>{{ $item->action }}</td>
                                    <td>{{ $item->reference_table }}</td>
                                    <td>{{ $item->id_reference }}</td>
                                    <td><pre style="white-space: pre-wrap; max-width: 280px;">{{ $item->raw_original }}</pre></td>
                                    <td><pre style="white-space: pre-wrap; max-width: 280px;">{{ $item->raw_changes }}</pre></td>
                                    <td><pre style="white-space: pre-wrap; max-width: 280px;">{{ $item->raw_new }}</pre></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-3 no-print">
                <button type="button" class="btn btn-primary btn-sm" onclick="printLogAktivitas()">
                    <i class="fas fa-print"></i> Print
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
            font-size: 8px;
        }

        table th,
        table td {
            border: 1px solid #000 !important;
            padding: 4px !important;
            color: #000 !important;
            vertical-align: top !important;
        }

        pre {
            white-space: pre-wrap !important;
            font-size: 7px !important;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }
    }
</style>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable.isDataTable('#tableLogAktivitas')) {
            $('#tableLogAktivitas').DataTable().destroy();
        }

        $('#tableLogAktivitas').DataTable();
    });

    $('#formFilterLogAktivitas').on('submit', function (e) {
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

        let url = "{{ route('ajax.laporan.log.aktivitas') }}";

        if (tanggalAwal && tanggalAkhir) {
            url += '?tanggal_awal=' + encodeURIComponent(tanggalAwal) + '&tanggal_akhir=' + encodeURIComponent(tanggalAkhir);
        }

        loadContent(url);
    });

    function resetFilterLogAktivitas() {
        loadContent("{{ route('ajax.laporan.log.aktivitas') }}");
    }

    function printLogAktivitas() {
        window.print();
    }
</script>