<div class="container-fluid">
    <div class="purchase-card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
            <div>
                <h5 class="mb-0">Riwayat Transaksi Penjualan</h5>
                <small class="text-muted">
                    Pantau transaksi, pembayaran, dan status pesanan.
                </small>
            </div>

            <button type="button" class="btn btn-primary btn-sm" onclick="loadContent('{{ route('ajax.transaksi.penjualan') }}')">
                Tambah Transaksi
            </button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tableRiwayatPenjualan" width="100%">
                    <thead>
                        <tr>
                            <th>Kode Pesanan</th>
                            <th>Tanggal</th>
                            <th>Customer</th>
                            <th>Pengiriman</th>
                            <th>Metode Bayar</th>
                            <th>Status Pesanan</th>
                            <th>Status Bayar</th>
                            <th>Grand Total</th>
                            <th width="260">Action</th>
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
        showUrlBase: "{{ url('/ajax/transaksi/penjualan/show') }}",
        deleteUrlBase: "{{ url('/ajax/transaksi/penjualan/delete') }}",
        validasiPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/validasi-pembayaran') }}",
        tolakPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/tolak-pembayaran') }}",
        updateStatusUrlBase: "{{ url('/ajax/transaksi/penjualan/update-status') }}",
        midtransTokenUrlBase: "{{ url('/ajax/transaksi/penjualan/midtrans-token') }}",
        isAdmin: @json(in_array($user->kode_role, ['KRL001', 'KRL002'])),
        csrfToken: "{{ csrf_token() }}"
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
            return 'Rp. ' + formatNumber(value || 0);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getErrorMessage(xhr, defaultMessage) {
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }

                if (xhr.responseJSON.errors) {
                    let html = '<ul style="text-align:left; margin:0; padding-left:18px;">';

                    Object.values(xhr.responseJSON.errors).forEach(function (err) {
                        html += `<li>${escapeHtml(err[0])}</li>`;
                    });

                    html += '</ul>';
                    return html;
                }
            }

            return defaultMessage;
        }

        function badgeStatusPesanan(status) {
            const value = status || '-';

            if (value === 'Pending') {
                return `<span class="badge badge-warning">${value}</span>`;
            }

            if (value === 'Diproses') {
                return `<span class="badge badge-info">${value}</span>`;
            }

            if (value === 'Dikirim') {
                return `<span class="badge badge-primary">${value}</span>`;
            }

            if (value === 'Selesai') {
                return `<span class="badge badge-success">${value}</span>`;
            }

            if (value === 'Batal') {
                return `<span class="badge badge-danger">${value}</span>`;
            }

            return `<span class="badge badge-secondary">${value}</span>`;
        }

        function badgeStatusPembayaran(status) {
            const value = status || '-';

            if (value === 'Belum Dibayar') {
                return `<span class="badge badge-secondary">${value}</span>`;
            }

            if (value === 'Menunggu Validasi') {
                return `<span class="badge badge-warning">${value}</span>`;
            }

            if (value === 'Lunas') {
                return `<span class="badge badge-success">${value}</span>`;
            }

            if (value === 'Ditolak') {
                return `<span class="badge badge-danger">${value}</span>`;
            }

            if (value === 'Gagal Bayar') {
                return `<span class="badge badge-danger">${value}</span>`;
            }

            return `<span class="badge badge-secondary">${value}</span>`;
        }

        function buildDeleteDetailHtml(response) {
            const header = response.header || {};
            const details = response.details || [];

            let itemRows = '';

            if (details.length > 0) {
                details.forEach(function (item, index) {
                    itemRows += `
                        <tr>
                            <td style="padding:8px; border:1px solid #dee2e6;">${index + 1}</td>
                            <td style="padding:8px; border:1px solid #dee2e6;">${escapeHtml(item.kode_barang || '-')}</td>
                            <td style="padding:8px; border:1px solid #dee2e6;">${escapeHtml(item.nama_barang || '-')}</td>
                            <td style="padding:8px; border:1px solid #dee2e6; text-align:right;">${formatNumber(parseFloat(item.qty) || 0)}</td>
                            <td style="padding:8px; border:1px solid #dee2e6; text-align:right;">${formatRupiah(parseFloat(item.harga_satuan) || 0)}</td>
                            <td style="padding:8px; border:1px solid #dee2e6; text-align:right;">${formatRupiah(parseFloat(item.subtotal_pesanan) || 0)}</td>
                        </tr>
                    `;
                });
            } else {
                itemRows = `
                    <tr>
                        <td colspan="6" style="padding:10px; border:1px solid #dee2e6; text-align:center;">
                            Detail barang tidak tersedia.
                        </td>
                    </tr>
                `;
            }

            return `
                <div style="text-align:left;">
                    <div style="margin-bottom:14px;">
                        <table style="width:100%; font-size:13px;">
                            <tr>
                                <td style="width:150px; padding:3px 0;">Kode Pesanan</td>
                                <td style="padding:3px 0;">: <strong>${escapeHtml(header.kode_pesanan || '-')}</strong></td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Tanggal</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.tgl_pesanan || '-')}</td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Customer</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.nama_customer || '-')} (${escapeHtml(header.kode_customer || '-')})</td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Pengiriman</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.jenis_pengiriman || '-')}</td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Status Pesanan</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.status_pesanan || '-')}</td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Status Pembayaran</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.status_pembayaran || '-')}</td>
                            </tr>
                            <tr>
                                <td style="padding:3px 0;">Alamat Kirim</td>
                                <td style="padding:3px 0;">: ${escapeHtml(header.alamat_kirim_pesanan || '-')}</td>
                            </tr>
                        </table>
                    </div>

                    <div style="max-height:260px; overflow:auto; margin-bottom:14px;">
                        <table style="width:100%; border-collapse:collapse; font-size:12px;">
                            <thead>
                                <tr>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">No</th>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">Kode</th>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">Barang</th>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">Qty</th>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">Harga</th>
                                    <th style="padding:8px; border:1px solid #dee2e6; background:#f8f9fa;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemRows}
                            </tbody>
                        </table>
                    </div>

                    <table style="width:100%; font-size:13px;">
                        <tr>
                            <td style="padding:3px 0;">Total Barang</td>
                            <td style="padding:3px 0; text-align:right;">
                                <strong>${formatRupiah(parseFloat(header.total_detail_pesanan) || 0)}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0;">Ongkir</td>
                            <td style="padding:3px 0; text-align:right;">
                                <strong>${formatRupiah(parseFloat(header.ongkir_pesanan) || 0)}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0;">Grand Total</td>
                            <td style="padding:3px 0; text-align:right;">
                                <strong>${formatRupiah(parseFloat(header.grand_total_pesanan) || 0)}</strong>
                            </td>
                        </tr>
                    </table>

                    <div style="margin-top:14px; padding:10px; border-radius:8px; background:#fff3cd; color:#856404; font-size:13px;">
                        Data akan disembunyikan menggunakan soft delete, bukan dihapus permanen dari database.
                    </div>
                </div>
            `;
        }

        function deleteRiwayatPenjualan(kodePesanan) {
            $.ajax({
                url: `${config.deleteUrlBase}/${kodePesanan}`,
                type: 'POST',
                data: {
                    _token: config.csrfToken,
                    _method: 'DELETE'
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Menghapus data',
                        text: 'Mohon tunggu.',
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        html: getErrorMessage(xhr, 'Data gagal dihapus.')
                    });
                }
            });
        }

        function openDeleteConfirmation(kodePesanan) {
            $.ajax({
                url: `${config.showUrlBase}/${kodePesanan}`,
                type: 'GET',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Mengambil detail pesanan',
                        text: 'Mohon tunggu.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: async function (response) {
                    if (!response.success) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal mengambil detail',
                            text: response.message || 'Detail transaksi tidak ditemukan.'
                        });
                        return;
                    }

                    const result = await Swal.fire({
                        title: 'Hapus transaksi ini?',
                        html: buildDeleteDetailHtml(response),
                        icon: 'warning',
                        width: 900,
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#dc3545',
                        focusCancel: true
                    });

                    if (!result.isConfirmed) {
                        return;
                    }

                    deleteRiwayatPenjualan(kodePesanan);
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal mengambil detail',
                        html: getErrorMessage(xhr, 'Detail transaksi tidak berhasil dimuat.')
                    });
                }
            });
        }

        function openMidtransPayment(kodePesanan) {
            if (!kodePesanan) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kode pesanan tidak ditemukan',
                    text: 'Kode pesanan tidak tersedia untuk proses pembayaran.'
                });
                return;
            }

            if (typeof snap === 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Midtrans belum aktif',
                    text: 'Script Snap Midtrans belum dimuat di halaman.'
                });
                return;
            }

            $.ajax({
                url: `${config.midtransTokenUrlBase}/${kodePesanan}`,
                type: 'POST',
                data: {
                    _token: config.csrfToken
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Mempersiapkan pembayaran',
                        text: 'Mohon tunggu, sistem sedang membuat token pembayaran.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close();

                    if (!response.success || !response.snap_token) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal membuat pembayaran',
                            text: response.message || 'Snap token tidak tersedia.'
                        });
                        return;
                    }

                    snap.pay(response.snap_token, {
                        onSuccess: function () {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran berhasil',
                                text: 'Pembayaran berhasil diproses.'
                            });

                            tableRiwayatPenjualan.ajax.reload(null, false);
                        },
                        onPending: function () {
                            Swal.fire({
                                icon: 'info',
                                title: 'Pembayaran belum selesai',
                                text: 'Silakan selesaikan pembayaran Anda.'
                            });

                            tableRiwayatPenjualan.ajax.reload(null, false);
                        },
                        onError: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran gagal',
                                text: 'Pembayaran gagal diproses oleh Midtrans.'
                            });
                        },
                        onClose: function () {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran belum diselesaikan',
                                text: 'Anda menutup popup pembayaran sebelum proses selesai.'
                            });

                            tableRiwayatPenjualan.ajax.reload(null, false);
                        }
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal membuat pembayaran',
                        html: getErrorMessage(xhr, 'Token pembayaran Midtrans gagal dibuat.')
                    });
                }
            });
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
                {
                    data: null,
                    render: function (data) {
                        return `
                            <strong>${escapeHtml(data.nama_customer || '-')}</strong>
                            <br>
                            <small class="text-muted">${escapeHtml(data.kode_customer || '-')}</small>
                        `;
                    }
                },
                {
                    data: 'jenis_pengiriman',
                    render: function (data) {
                        return escapeHtml(data || '-');
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        const metode = escapeHtml(data.metode_pembayaran || '-');
                        const bank = data.bank_tujuan ? `<br><small class="text-muted">${escapeHtml(data.bank_tujuan)}</small>` : '';

                        return `${metode}${bank}`;
                    }
                },
                {
                    data: 'status_pesanan',
                    render: function (data) {
                        return badgeStatusPesanan(data);
                    }
                },
                {
                    data: 'status_pembayaran',
                    render: function (data) {
                        return badgeStatusPembayaran(data);
                    }
                },
                {
                    data: 'grand_total_pesanan',
                    render: function (data) {
                        return `<strong>${formatRupiah(parseFloat(data) || 0)}</strong>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        const statusPesanan = data.status_pesanan || 'Pending';
                        const statusPembayaran = data.status_pembayaran || 'Belum Dibayar';

                        let html = `<div class="d-flex flex-wrap" style="gap: 6px;">`;

                        html += `
                            <button type="button" class="btn btn-sm btn-secondary btn-detail-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                Detail
                            </button>
                        `;

                        if (config.isAdmin) {
                            html += `
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                    Delete
                                </button>
                            `;

                            if (statusPembayaran === 'Menunggu Validasi') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-success btn-validasi-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                        Validasi
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning btn-tolak-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                        Tolak
                                    </button>
                                `;
                            }

                            if (statusPembayaran === 'Lunas') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-info btn-status-riwayat"
                                        data-kode="${escapeHtml(data.kode_pesanan)}"
                                        data-status="${escapeHtml(statusPesanan)}">
                                        Ubah Status
                                    </button>
                                `;
                            }

                            html += `</div>`;
                            return html;
                        }

                        if (statusPesanan === 'Pending' && statusPembayaran === 'Belum Dibayar') {
                            html += `
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                    Delete
                                </button>
                            `;

                            if (data.metode_pembayaran === 'Midtrans') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-success btn-bayar-riwayat" data-kode="${escapeHtml(data.kode_pesanan)}">
                                        Bayar
                                    </button>
                                `;
                            }
                        } else if (statusPembayaran === 'Ditolak') {
                            html += `
                                <span class="badge badge-danger align-self-center">Pembayaran Ditolak</span>
                            `;
                        } else {
                            html += `<span class="badge badge-secondary align-self-center">Locked</span>`;
                        }

                        html += `</div>`;
                        return html;
                    }
                }
            ]
        });

        $(document).off('click', '.btn-bayar-riwayat').on('click', '.btn-bayar-riwayat', function () {
            const kodePesanan = $(this).data('kode');
            openMidtransPayment(kodePesanan);
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
                        title: 'Gagal membuka data',
                        text: 'Halaman transaksi penjualan belum termuat dengan benar.'
                    });
                }
            }, 800);
        });

        $(document).off('click', '.btn-delete-riwayat').on('click', '.btn-delete-riwayat', function () {
            const kodePesanan = $(this).data('kode');
            openDeleteConfirmation(kodePesanan);
        });

        $(document).off('click', '.btn-validasi-riwayat').on('click', '.btn-validasi-riwayat', function () {
            const kodePesanan = $(this).data('kode');

            Swal.fire({
                title: 'Validasi pembayaran?',
                text: `Pembayaran transaksi ${kodePesanan} akan disetujui dan status pesanan menjadi Diproses.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, validasi',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: `${config.validasiPembayaranUrlBase}/${kodePesanan}`,
                    type: 'POST',
                    data: {
                        _token: config.csrfToken
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Pembayaran berhasil divalidasi.'
                        });

                        tableRiwayatPenjualan.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal validasi',
                            html: getErrorMessage(xhr, 'Pembayaran gagal divalidasi.')
                        });
                    }
                });
            });
        });

        $(document).off('click', '.btn-tolak-riwayat').on('click', '.btn-tolak-riwayat', function () {
            const kodePesanan = $(this).data('kode');

            Swal.fire({
                title: 'Tolak pembayaran?',
                input: 'textarea',
                inputLabel: 'Catatan Penolakan',
                inputPlaceholder: 'Contoh: Pembayaran tidak valid.',
                inputAttributes: {
                    maxlength: 255
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: `${config.tolakPembayaranUrlBase}/${kodePesanan}`,
                    type: 'POST',
                    data: {
                        _token: config.csrfToken,
                        catatan_validasi: result.value || ''
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Pembayaran berhasil ditolak.'
                        });

                        tableRiwayatPenjualan.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menolak',
                            html: getErrorMessage(xhr, 'Pembayaran gagal ditolak.')
                        });
                    }
                });
            });
        });

        $(document).off('click', '.btn-status-riwayat').on('click', '.btn-status-riwayat', function () {
            const kodePesanan = $(this).data('kode');
            const statusSekarang = $(this).data('status') || 'Diproses';

            Swal.fire({
                title: 'Ubah Status Pesanan',
                input: 'select',
                inputOptions: {
                    'Diproses': 'Diproses',
                    'Dikirim': 'Dikirim',
                    'Selesai': 'Selesai',
                    'Batal': 'Batal'
                },
                inputValue: statusSekarang,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
            }).then(function (result) {
                if (!result.isConfirmed) {
                    return;
                }

                $.ajax({
                    url: `${config.updateStatusUrlBase}/${kodePesanan}`,
                    type: 'POST',
                    data: {
                        _token: config.csrfToken,
                        status_pesanan: result.value
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message || 'Status pesanan berhasil diperbarui.'
                        });

                        tableRiwayatPenjualan.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal update status',
                            html: getErrorMessage(xhr, 'Status pesanan gagal diperbarui.')
                        });
                    }
                });
            });
        });
    })();
</script>