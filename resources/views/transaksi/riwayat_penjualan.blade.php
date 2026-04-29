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

    <div class="modal fade" id="modalUploadBuktiRiwayat" tabindex="-1" role="dialog" aria-labelledby="modalUploadBuktiRiwayatLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="formUploadBuktiRiwayat" enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="upload_riwayat_kode_pesanan" name="kode_pesanan">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalUploadBuktiRiwayatLabel">Upload Bukti Pembayaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            Unggah bukti pembayaran untuk transaksi <strong id="upload_riwayat_kode_pesanan_text">-</strong>.
                        </p>

                        <div class="form-group">
                            <label>Bukti Pembayaran</label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_riwayat" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
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
    window.riwayatPenjualanConfig = {
        dataUrl: "{{ route('ajax.transaksi.penjualan.data') }}",
        transaksiPenjualanUrl: "{{ route('ajax.transaksi.penjualan') }}",
        deleteUrlBase: "{{ url('/ajax/transaksi/penjualan/delete') }}",
        uploadBuktiUrlBase: "{{ url('/ajax/transaksi/penjualan/upload-bukti') }}",
        validasiPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/validasi-pembayaran') }}",
        tolakPembayaranUrlBase: "{{ url('/ajax/transaksi/penjualan/tolak-pembayaran') }}",
        updateStatusUrlBase: "{{ url('/ajax/transaksi/penjualan/update-status') }}",
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

        function getErrorMessage(xhr, defaultMessage) {
            if (xhr.responseJSON) {
                if (xhr.responseJSON.message) {
                    return xhr.responseJSON.message;
                }

                if (xhr.responseJSON.errors) {
                    let html = '<ul style="text-align:left; margin:0; padding-left:18px;">';
                    Object.values(xhr.responseJSON.errors).forEach(function (err) {
                        html += `<li>${err[0]}</li>`;
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

            return `<span class="badge badge-secondary">${value}</span>`;
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
                            <strong>${data.nama_customer || '-'}</strong>
                            <br>
                            <small class="text-muted">${data.kode_customer || '-'}</small>
                        `;
                    }
                },
                {
                    data: 'jenis_pengiriman',
                    render: function (data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    render: function (data) {
                        const metode = data.metode_pembayaran || '-';
                        const bank = data.bank_tujuan ? `<br><small class="text-muted">${data.bank_tujuan}</small>` : '';
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
                            <button type="button" class="btn btn-sm btn-secondary btn-detail-riwayat" data-kode="${data.kode_pesanan}">
                                Detail
                            </button>
                        `;

                        if (data.bukti_pembayaran) {
                            html += `
                                <a href="/storage/${data.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-dark">
                                    Lihat Bukti
                                </a>
                            `;
                        }

                        if (config.isAdmin) {
                            html += `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-riwayat" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;

                            if (statusPembayaran === 'Menunggu Validasi') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-success btn-validasi-riwayat" data-kode="${data.kode_pesanan}">
                                        Validasi
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning btn-tolak-riwayat" data-kode="${data.kode_pesanan}">
                                        Tolak
                                    </button>
                                `;
                            }

                            if (statusPembayaran === 'Lunas') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-info btn-status-riwayat"
                                        data-kode="${data.kode_pesanan}"
                                        data-status="${statusPesanan}">
                                        Ubah Status
                                    </button>
                                `;
                            }

                            html += `</div>`;
                            return html;
                        }

                        if (statusPesanan === 'Pending' && statusPembayaran === 'Belum Dibayar') {
                            html += `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-riwayat" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-riwayat" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                                <button type="button" class="btn btn-sm btn-success btn-upload-bukti-riwayat" data-kode="${data.kode_pesanan}">
                                    Upload Bukti
                                </button>
                            `;
                        } else if (statusPembayaran === 'Ditolak') {
                            html += `
                                <button type="button" class="btn btn-sm btn-warning btn-upload-bukti-riwayat" data-kode="${data.kode_pesanan}">
                                    Upload Ulang
                                </button>
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

        $(document).off('click', '.btn-edit-riwayat, .btn-detail-riwayat').on('click', '.btn-edit-riwayat, .btn-detail-riwayat', function () {
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
                    _token: config.csrfToken,
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        html: getErrorMessage(xhr, 'Data gagal dihapus.')
                    });
                }
            });
        });

        $(document).off('click', '.btn-upload-bukti-riwayat').on('click', '.btn-upload-bukti-riwayat', function () {
            const kodePesanan = $(this).data('kode');

            $('#upload_riwayat_kode_pesanan').val(kodePesanan);
            $('#upload_riwayat_kode_pesanan_text').text(kodePesanan);
            $('#bukti_pembayaran_riwayat').val('');

            $('#modalUploadBuktiRiwayat').modal('show');
        });

        $(document).off('submit', '#formUploadBuktiRiwayat').on('submit', '#formUploadBuktiRiwayat', function (e) {
            e.preventDefault();

            const kodePesanan = $('#upload_riwayat_kode_pesanan').val();
            const formData = new FormData(this);

            $.ajax({
                url: `${config.uploadBuktiUrlBase}/${kodePesanan}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    Swal.fire({
                        title: 'Mengunggah bukti',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    $('#modalUploadBuktiRiwayat').modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Bukti pembayaran berhasil diunggah.'
                    });

                    tableRiwayatPenjualan.ajax.reload(null, false);
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal upload',
                        html: getErrorMessage(xhr, 'Bukti pembayaran gagal diunggah.')
                    });
                }
            });
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
                inputPlaceholder: 'Contoh: Bukti pembayaran tidak jelas atau nominal tidak sesuai.',
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