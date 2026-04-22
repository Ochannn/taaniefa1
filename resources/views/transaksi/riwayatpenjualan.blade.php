<div class="container-fluid">
    <div class="purchase-card">
        <div class="card-header">
            Riwayat Transaksi Penjualan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableRiwayatPenjualan" width="100%">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Tanggal</th>
                            <th>Kode Customer</th>
                            <th>Customer</th>
                            <th>Jenis Pengiriman</th>
                            <th>Jenis Pemesanan</th>
                            <th>Harga Custom</th>
                            <th>Status Custom</th>
                            <th>Status Pesanan</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    window.riwayatPenjualanConfig = {
        dataUrl: "{{ route('ajax.transaksi.penjualan.data') }}",
        transaksiPenjualanUrl: "{{ route('ajax.transaksi.penjualan') }}",
        deleteUrlBase: "{{ url('/ajax/transaksi/penjualan/delete') }}",
        isAdmin: @json($user->isAdmin())
    };

    (function () {
        const config = window.riwayatPenjualanConfig;

        if ($.fn.DataTable.isDataTable('#tableRiwayatPenjualan')) {
            $('#tableRiwayatPenjualan').DataTable().destroy();
        }

        function formatNumber(value) {
            return new Intl.NumberFormat('id-ID').format(value || 0);
        }

        function formatRupiah(value) {
            return 'Rp.' + formatNumber(value || 0);
        }

        const tableRiwayatPenjualan = $('#tableRiwayatPenjualan').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: config.dataUrl,
                type: 'GET',
                dataSrc: 'data',
                error: function (xhr) {
                    console.log(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengambil data',
                        text: 'Data riwayat transaksi tidak berhasil dimuat.'
                    });
                }
            },
            columns: [
                { data: 'kode_pesanan' },
                { data: 'tgl_pesanan' },
                { data: 'kode_customer' },
                {
                    data: 'nama_customer',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'jenis_pengiriman',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'jenis_pemesanan',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'harga_estimasi',
                    render: function (data, type, row) {
                        if ((row.jenis_pemesanan || '') !== 'Custom') {
                            return '-';
                        }

                        const nilai = parseFloat(data) || 0;
                        return nilai > 0 ? formatRupiah(nilai) : '0';
                    }
                },
                {
                    data: 'status_custom',
                    render: function (data, type, row) {
                        if ((row.jenis_pemesanan || '') !== 'Custom') {
                            return '-';
                        }

                        const hargaEstimasi = parseFloat(row.harga_estimasi) || 0;

                        if (data === 'lanjutkan') {
                            return 'Disetujui customer';
                        }

                        if (data === 'batal') {
                            return 'Ditolak customer';
                        }

                        if (hargaEstimasi > 0) {
                            return 'Menunggu keputusan customer';
                        }

                        return 'Menunggu keputusan admin';
                    }
                },
                {
                    data: 'status_pesanan',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        const status = (data.status_pesanan || '').toLowerCase();

                        if (config.isAdmin) {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-riwayat" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;
                        }

                        if (status === 'pending') {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-riwayat" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;
                        }

                        return `
                            <button type="button" class="btn btn-sm btn-secondary btn-detail-riwayat" data-kode="${data.kode_pesanan}">
                                Detail
                            </button>
                        `;
                    }
                }
            ]
        });

        $(document).off('click', '.btn-edit-riwayat').on('click', '.btn-edit-riwayat', function () {
            const kodePesanan = $(this).data('kode');

            loadContent(config.transaksiPenjualanUrl);

            setTimeout(function () {
                if (typeof window.loadDataPenjualanUntukEdit === 'function') {
                    window.loadDataPenjualanUntukEdit(kodePesanan);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal membuka edit',
                        text: 'Halaman edit belum termuat dengan benar.'
                    });
                }
            }, 800);
        });

        $(document).off('click', '.btn-detail-riwayat').on('click', '.btn-detail-riwayat', function () {
            const kodePesanan = $(this).data('kode');

            loadContent(config.transaksiPenjualanUrl);

            setTimeout(function () {
                if (typeof window.loadDataPenjualanUntukEdit === 'function') {
                    window.loadDataPenjualanUntukEdit(kodePesanan);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal membuka detail',
                        text: 'Halaman detail belum termuat dengan benar.'
                    });
                }
            }, 800);
        });

        $(document).off('click', '.btn-delete-riwayat').on('click', '.btn-delete-riwayat', async function () {
            const kodePesanan = $(this).data('kode');

            const result = await Swal.fire({
                title: 'Hapus transaksi ini?',
                text: `Transaksi ${kodePesanan} akan dihapus.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: `${config.deleteUrlBase}/${kodePesanan}`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'DELETE'
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Menghapus data',
                        text: 'Mohon tunggu...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil dihapus.'
                    });

                    tableRiwayatPenjualan.ajax.reload(null, false);
                },
                error: function (xhr) {
                    let pesan = 'Data gagal dihapus.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        pesan = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        text: pesan
                    });
                }
            });
        });
    })();
</script>