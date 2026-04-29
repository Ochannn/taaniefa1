window.initTransaksiPenjualan = function (config) {
    const barangList = config.barangList || [];
    let detailItems = [];
    let editIndex = null;
    let tablePenjualan = null;
    let formLocked = false;
    let isSyncingBarangSelect = false;
    



    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID').format(value || 0);
    }

    function formatRupiah(value) {
        return 'Rp.' + formatNumber(value || 0);
    }

    function getBarangByKode(kode) {
        return barangList.find(item => item.kode_barang === kode);
    }

    function getBarangByNama(nama) {
        return barangList.find(item => item.nama_barang === nama);
    }

    function getOngkirValue() {
        return parseFloat($('#ongkir_pesanan').val()) || 0;
    }

    function formatBarangOption(option) {
        if (!option.id) {
            return option.text;
        }

        const el = $(option.element);
        const kode = el.data('kode') || '-';
        const nama = el.data('nama') || '-';
        const kapasitas = parseFloat(el.data('kapasitas')) || 0;
        const harga = parseFloat(el.data('harga')) || 0;

        return $(`
            <div class="barang-option-wrap">
                <div class="barang-option-kiri">
                    <span class="barang-option-title">${nama}</span>
                    <span class="barang-option-subtitle">${kode}</span>
                </div>
                <div class="barang-option-kanan">
                    <span class="barang-option-stok">Stok: ${formatNumber(kapasitas)}</span>
                    <span class="barang-option-harga">${formatRupiah(harga)}</span>
                </div>
            </div>
        `);
    }


    function initSelectBarang() {
        $('#detail_kategori_barang_penjualan').select2({
            width: '100%',
            placeholder: 'Semua Kategori',
            templateResult: formatKategoriOption,
            templateSelection: formatKategoriOption
        });

        $('#detail_kode_barang_penjualan').select2({
            width: '100%',
            placeholder: 'Pilih Kode Barang',
            templateResult: formatBarangOption,
            templateSelection: function (option) {
                if (!option.id) {
                    return option.text;
                }

                const el = $(option.element);
                return `${el.data('kode')} - ${el.data('nama')}`;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $('#detail_nama_barang_penjualan').select2({
            width: '100%',
            placeholder: 'Pilih Nama Barang',
            templateResult: formatBarangOption,
            templateSelection: function (option) {
                if (!option.id) {
                    return option.text;
                }

                const el = $(option.element);
                return `${el.data('nama')} - ${el.data('kode')}`;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });
    }

    $(document).off('change', '#detail_kategori_barang_penjualan').on('change', '#detail_kategori_barang_penjualan', function () {
        applyKategoriFilter();
    });

    function setFormLockState(locked) {
        formLocked = locked;

        const disableSelector = [
            '#tgl_pesanan',
            '#kode_customer',
            '#jenis_pengiriman',
            '#jenis_pemesanan',
            '#alamat_kirim_pesanan',
            '#catatan_pesanan',
            '#detail_kode_barang_penjualan',
            '#detail_nama_barang_penjualan',
            '#detail_qty_penjualan',
            '#btnTambahDetailPenjualan',
            '#btnSavePenjualan'
        ].join(',');

        $(disableSelector).prop('disabled', locked);

        if (locked) {
            $('#spesifikasi_tambahan').prop('readonly', true);
            $('#harga_estimasi').prop('readonly', true);
            $('#status_pesanan').prop('disabled', true);
            $('#btnLanjutkanCustom').prop('disabled', true);
            $('#btnTolakCustom').prop('disabled', true);
            $('.btn-edit-detail-penjualan').prop('disabled', true);
            $('.btn-delete-detail-penjualan').prop('disabled', true);
            return;
        }

        if (config.isAdmin) {
            $('#status_pesanan').prop('disabled', false);
            $('#harga_estimasi').prop('readonly', false);
        } else {
            $('#status_pesanan').prop('disabled', true);
            $('#harga_estimasi').prop('readonly', true);
        }

        $('#spesifikasi_tambahan').prop('readonly', !config.isAdmin ? false : false);
        $('#btnLanjutkanCustom').prop('disabled', false);
        $('#btnTolakCustom').prop('disabled', false);
    }
    function applyLockByStatus(statusPesanan) {
        if (config.isAdmin) {
            setFormLockState(false);
            return;
        }

        if ((statusPesanan || '').toLowerCase() === 'pending') {
            setFormLockState(false);
        } else {
            setFormLockState(true);
        }
    }

    function clearDetailForm(resetKategori = false) {
        isSyncingBarangSelect = true;

        if (resetKategori) {
            $('#detail_kategori_barang_penjualan').val('').trigger('change.select2');
        }

        $('#detail_kode_barang_penjualan').val('').trigger('change.select2');
        $('#detail_nama_barang_penjualan').val('').trigger('change.select2');
        $('#detail_qty_penjualan').val('');
        $('#detail_harga_satuan_penjualan').val('');

        isSyncingBarangSelect = false;
        editIndex = null;

        $('#btnTambahDetailPenjualan')
            .text('Tambah Item')
            .removeClass('btn-warning')
            .addClass('btn-info');
    }

    function clearHeaderForm() {
        $('#tgl_pesanan').val(config.defaultDate || '');

        if ($('#kode_customer').length) {
            if (config.requireCustomerSelection) {
                $('#kode_customer').val('');
            } else {
                $('#kode_customer').val(config.customerAktifKode || '');
            }
        }

        $('#jenis_pengiriman').val('');
        $('#jenis_pemesanan').val('');
        $('#status_pesanan').val('Pending');
        $('#alamat_kirim_pesanan').val('');
        $('#ongkir_pesanan').val(0);
        $('#catatan_pesanan').val('');
        $('#metode_pembayaran').val('');
        $('#bank_tujuan').val('');
        $('#wrap_bank_tujuan').hide();
        $('#info_transfer_bank').hide();
        $('#info_qris').hide();
        $('#info_cash').hide();
        $('#info_bank').text('-');
        $('#info_rekening').text('-');
        $('#info_atas_nama').text('-');

        $('#form_mode_penjualan').val('create');
        $('#edit_kode_pesanan').val('');
        $('#btnSavePenjualan').text('Save Data');

        applyLockByStatus('Pending');
    }

    function syncNamaByKode() {
        if (isSyncingBarangSelect) {
            return;
        }

        const kode = $('#detail_kode_barang_penjualan').val();
        const selected = getBarangByKode(kode);

        isSyncingBarangSelect = true;

        if (selected) {
            $('#detail_nama_barang_penjualan').val(selected.nama_barang).trigger('change.select2');
            $('#detail_harga_satuan_penjualan').val(selected.harga_jual || 0);
        } else {
            $('#detail_nama_barang_penjualan').val('').trigger('change.select2');
            $('#detail_harga_satuan_penjualan').val('');
        }

        isSyncingBarangSelect = false;
    }

    function syncKodeByNama() {
        if (isSyncingBarangSelect) {
            return;
        }

        const nama = $('#detail_nama_barang_penjualan').val();
        const selected = getBarangByNama(nama);

        isSyncingBarangSelect = true;

        if (selected) {
            $('#detail_kode_barang_penjualan').val(selected.kode_barang).trigger('change.select2');
            $('#detail_harga_satuan_penjualan').val(selected.harga_jual || 0);
        } else {
            $('#detail_kode_barang_penjualan').val('').trigger('change.select2');
            $('#detail_harga_satuan_penjualan').val('');
        }

        isSyncingBarangSelect = false;
    }

    function generateRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }


    function setOngkirByJenis() {
        const jenis = $('#jenis_pengiriman').val();
        let ongkir = 0;

        if (jenis === 'Reguler') {
            ongkir = generateRandomInt(25000, 30000);
        } else if (jenis === 'Express') {
            ongkir = generateRandomInt(30000, 50000);
        } else if (jenis === 'Preorder') {
            ongkir = 50000;
        }

        $('#ongkir_pesanan').val(ongkir);
        renderTable();
    }

    function initPaymentMethod() {
        $(document).off('change', '#metode_pembayaran').on('change', '#metode_pembayaran', function () {
            const metode = $(this).val();

            $('#wrap_bank_tujuan').hide();
            $('#info_transfer_bank').hide();
            $('#info_qris').hide();
            $('#info_cash').hide();

            $('#bank_tujuan').val('');
            $('#info_bank').text('-');
            $('#info_rekening').text('-');
            $('#info_atas_nama').text('-');

            if (metode === 'Transfer Bank') {
                $('#wrap_bank_tujuan').show();
            }

            if (metode === 'QRIS') {
                $('#info_qris').show();
            }

            if (metode === 'Cash') {
                $('#info_cash').show();
            }
        });

        $(document).off('change', '#bank_tujuan').on('change', '#bank_tujuan', function () {
            const selected = $(this).find(':selected');

            const bank = selected.data('bank') || '-';
            const rekening = selected.data('rekening') || '-';
            const atasNama = selected.data('atas-nama') || '-';

            if ($(this).val()) {
                $('#info_bank').text(bank);
                $('#info_rekening').text(rekening);
                $('#info_atas_nama').text(atasNama);
                $('#info_transfer_bank').show();
            } else {
                $('#info_transfer_bank').hide();
            }
        });
    }
    
    function renderTable() {
        let html = '';
        let totalBarang = 0;
        const ongkir = getOngkirValue();

        if (detailItems.length === 0) {
            html = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Belum ada item penjualan.
                    </td>
                </tr>
            `;
        } else {
            detailItems.forEach((item, index) => {
                item.subtotal_pesanan = Number(item.qty) * Number(item.harga_satuan);
                totalBarang += Number(item.subtotal_pesanan);

                let actionHtml = '-';

                if (!formLocked) {
                    actionHtml = `
                        <button type="button" class="btn btn-sm btn-primary btn-edit-detail-penjualan" data-index="${index}">
                            Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-danger btn-delete-detail-penjualan" data-index="${index}">
                            Delete
                        </button>
                    `;
                }

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.kode_barang}</td>
                        <td>${item.nama_barang}</td>
                        <td>${formatNumber(item.qty)}</td>
                        <td>${formatNumber(item.harga_satuan)}</td>
                        <td>${formatNumber(item.subtotal_pesanan)}</td>
                        <td>${actionHtml}</td>
                    </tr>
                `;
            });
        }

        const grandTotal = totalBarang + ongkir;

        $('#detailTableBodyPenjualan').html(html);
        $('#total_item_penjualan_text').text(detailItems.length);
        $('#total_barang_penjualan_text').text(formatNumber(totalBarang));
        $('#grand_total_penjualan_text').text(formatNumber(grandTotal));
        $('#total_barang_penjualan').val(totalBarang);
        $('#grand_total_penjualan').val(grandTotal);
    }

    function getFormDetail() {
        const kodeBarang = $('#detail_kode_barang_penjualan').val();
        const namaBarang = $('#detail_nama_barang_penjualan').val();
        const qty = parseFloat($('#detail_qty_penjualan').val()) || 0;
        const hargaSatuan = parseFloat($('#detail_harga_satuan_penjualan').val()) || 0;

        return {
            kode_barang: kodeBarang,
            nama_barang: namaBarang,
            qty: qty,
            harga_satuan: hargaSatuan,
            subtotal_pesanan: qty * hargaSatuan
        };
    }

    function validateDetail(itemData) {
        if (!itemData.kode_barang || !itemData.nama_barang) {
            Swal.fire({
                icon: 'warning',
                title: 'Data belum lengkap',
                text: 'Kode barang dan nama barang harus dipilih.'
            });
            return false;
        }

        const barang = getBarangByKode(itemData.kode_barang);

        if (!barang) {
            Swal.fire({
                icon: 'warning',
                title: 'Barang tidak ditemukan',
                text: 'Barang yang dipilih tidak valid.'
            });
            return false;
        }

        if (parseFloat(barang.kapasitas) <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Stok tidak tersedia',
                text: 'Barang dengan stok kosong tidak dapat dipilih.'
            });
            return false;
        }

        if (itemData.qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Qty tidak valid',
                text: 'Qty harus lebih dari 0.'
            });
            return false;
        }

        if (itemData.harga_satuan < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Harga tidak valid',
                text: 'Harga satuan tidak boleh negatif.'
            });
            return false;
        }

        return true;
    }

    function findDuplicateIndex(kodeBarang) {
        return detailItems.findIndex(item => item.kode_barang === kodeBarang);
    }

    function getErrorMessage(xhr, defaultMessage = 'Terjadi kesalahan pada proses.') {
        if (xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
                let html = '<ul style="text-align:left; margin:0; padding-left:18px;">';
                Object.values(xhr.responseJSON.errors).forEach(function (err) {
                    html += `<li>${err[0]}</li>`;
                });
                html += '</ul>';
                return html;
            }

            if (xhr.responseJSON.message) {
                return xhr.responseJSON.message;
            }
        }

        return defaultMessage;
    }

    function buildPlainPayload() {
        const payload = {
            _token: config.csrfToken,
            kode_customer: config.requireCustomerSelection
                ? ($('#kode_customer').val() || '')
                : (config.customerAktifKode || $('#kode_customer').val() || ''),
            tgl_pesanan: $('#tgl_pesanan').val() || '',
            jenis_pengiriman: $('#jenis_pengiriman').val() || '',
            jenis_pemesanan: $('#jenis_pemesanan').val() || 'Standart',
            status_pesanan: $('#status_pesanan').val() || 'Pending',
            alamat_kirim_pesanan: $('#alamat_kirim_pesanan').val() || '',
            ongkir_pesanan: $('#ongkir_pesanan').val() || 0,
            catatan_pesanan: $('#catatan_pesanan').val() || '',
            metode_pembayaran: $('#metode_pembayaran').val() || '',
            provinsi_tujuan: $('#provinsi_tujuan').val() || '',
            kota_tujuan: $('#kota_tujuan').val() || '',
            kurir: $('#kurir').val() || '',
            layanan_kurir: $('#layanan_kurir').val() || '',
            estimasi_pengiriman: $('#estimasi_pengiriman').val() || '',
            bank_tujuan: $('#bank_tujuan').val() || ''
            
        };

        detailItems.forEach((item, index) => {
            payload[`items[${index}][kode_barang]`] = item.kode_barang;
            payload[`items[${index}][nama_barang]`] = item.nama_barang;
            payload[`items[${index}][qty]`] = item.qty;
        });

        return payload;
    }

    function refreshKodePreview() {
        if (!config.reloadUrl) {
            return;
        }

        $.get(config.reloadUrl, function (html) {
            const preview = $('<div>').html(html).find('#kode_pesanan_preview').val();
            if (preview) {
                $('#kode_pesanan_preview').val(preview);
            }
        });
    }

    function resetFormPenjualan() {
        detailItems = [];
        editIndex = null;
        clearHeaderForm();
        clearDetailForm(true);
        applyKategoriFilter();
        renderTable();
        refreshKodePreview();
    }

    $(document).off('click', '.btn-update-status-pesanan').on('click', '.btn-update-status-pesanan', function () {
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

                    if (tablePenjualan) {
                        tablePenjualan.ajax.reload(null, false);
                    }
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

    $(document).off('click', '.btn-tolak-pembayaran').on('click', '.btn-tolak-pembayaran', function () {
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

                    if (tablePenjualan) {
                        tablePenjualan.ajax.reload(null, false);
                    }
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

    $(document).off('click', '.btn-validasi-pembayaran').on('click', '.btn-validasi-pembayaran', function () {
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

                    if (tablePenjualan) {
                        tablePenjualan.ajax.reload(null, false);
                    }
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

    function initDataTablePenjualan() {
        if ($.fn.DataTable.isDataTable('#tableViewPenjualan')) {
            tablePenjualan = $('#tableViewPenjualan').DataTable();
            tablePenjualan.ajax.reload(null, false);
            return;
        }

        tablePenjualan = $('#tableViewPenjualan').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: config.dataUrl,
                type: 'GET',
                dataSrc: 'data'
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
                    data: 'status_pesanan',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'status_pembayaran',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'grand_total_pesanan',
                    render: function (data) {
                        return formatRupiah(parseFloat(data) || 0);
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        const statusPembayaran = data.status_pembayaran || 'Belum Dibayar';
                        const statusPesanan = data.status_pesanan || 'Pending';

                        if (config.isAdmin) {
                            let html = `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-penjualan" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-penjualan" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;

                            if (data.bukti_pembayaran) {
                                html += `
                                    <a href="/storage/${data.bukti_pembayaran}" target="_blank" class="btn btn-sm btn-secondary">
                                        Lihat Bukti
                                    </a>
                                `;
                            }

                            if (statusPembayaran === 'Menunggu Validasi') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-success btn-validasi-pembayaran" data-kode="${data.kode_pesanan}">
                                        Validasi
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning btn-tolak-pembayaran" data-kode="${data.kode_pesanan}">
                                        Tolak
                                    </button>
                                `;
                            }

                            if (statusPembayaran === 'Lunas') {
                                html += `
                                    <button type="button" class="btn btn-sm btn-info btn-update-status-pesanan"
                                        data-kode="${data.kode_pesanan}"
                                        data-status="${statusPesanan}">
                                        Ubah Status
                                    </button>
                                `;
                            }

                            return html;
                        }

                        if (statusPesanan === 'Pending' && statusPembayaran === 'Belum Dibayar') {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-penjualan" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-penjualan" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                                <button type="button" class="btn btn-sm btn-success btn-upload-bukti-penjualan" data-kode="${data.kode_pesanan}">
                                    Upload Bukti
                                </button>
                            `;
                        }

                        if (statusPembayaran === 'Ditolak') {
                            return `
                                <button type="button" class="btn btn-sm btn-warning btn-upload-bukti-penjualan" data-kode="${data.kode_pesanan}">
                                    Upload Ulang
                                </button>
                                <span class="badge badge-secondary ml-1">Locked</span>
                            `;
                        }

                        return `<span class="badge badge-secondary">Locked</span>`;
                    }
                }
            ]
        });
    }

    function loadDataPenjualanUntukEdit(kodePesanan) {
        $.ajax({
            url: `${config.showUrlBase}/${kodePesanan}`,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data tidak ditemukan',
                        text: response.message || 'Data penjualan tidak ditemukan.'
                    });
                    return;
                }

                $('#form_mode_penjualan').val('edit');
                $('#edit_kode_pesanan').val(response.header.kode_pesanan);
                $('#kode_pesanan_preview').val(response.header.kode_pesanan);
                $('#tgl_pesanan').val(response.header.tgl_pesanan);

                if ($('#kode_customer').length) {
                    $('#kode_customer').val(response.header.kode_customer || '');
                }

                $('#jenis_pengiriman').val(response.header.jenis_pengiriman || '');
                $('#jenis_pemesanan').val(response.header.jenis_pemesanan || '');
                $('#status_pesanan').val(response.header.status_pesanan || 'Pending');
                $('#alamat_kirim_pesanan').val(response.header.alamat_kirim_pesanan || '');
                $('#ongkir_pesanan').val(response.header.ongkir_pesanan || 0);
                $('#catatan_pesanan').val(response.header.catatan_pesanan || '');
                $('#btnSavePenjualan').text('Update Data');

                detailItems = (response.details || []).map(item => {
                    return {
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang,
                        qty: parseFloat(item.qty) || 0,
                        harga_satuan: parseFloat(item.harga_satuan) || 0,
                        subtotal_pesanan: (parseFloat(item.qty) || 0) * (parseFloat(item.harga_satuan) || 0)
                    };
                });


                editIndex = null;
                clearDetailForm();
                applyLockByStatus(response.header.status_pesanan);
                renderTable();

                $('#modalViewPenjualan').modal('hide');

                $('html, body').animate({
                    scrollTop: 0
                }, 300);

                Swal.fire({
                    icon: 'success',
                    title: response.can_edit ? 'Mode edit aktif' : 'Data terkunci',
                    text: response.can_edit
                        ? `Transaksi ${kodePesanan} berhasil dimuat.`
                        : `Transaksi ${kodePesanan} berhasil dimuat, tetapi sudah tidak dapat diedit.`
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mengambil data',
                    html: getErrorMessage(xhr, 'Data penjualan gagal diambil.')
                });
            }
        });
    }

    window.loadDataPenjualanUntukEdit = loadDataPenjualanUntukEdit;

    function hapusDataPenjualan(kodePesanan) {
        Swal.fire({
            title: 'Hapus transaksi ini?',
            text: `Transaksi ${kodePesanan} akan dihapus dan stok akan dikembalikan.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
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
                        text: 'Mohon tunggu, transaksi sedang diproses.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil dihapus',
                        text: response.message || 'Data penjualan berhasil dihapus.'
                    });

                    if (tablePenjualan) {
                        tablePenjualan.ajax.reload(null, false);
                    }

                    if ($('#edit_kode_pesanan').val() === kodePesanan) {
                        resetFormPenjualan();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        html: getErrorMessage(xhr, 'Data penjualan gagal dihapus.')
                    });
                }
            });
        });
    }

    $(document).off('change', '#detail_kode_barang_penjualan').on('change', '#detail_kode_barang_penjualan', function () {
        if (!isSyncingBarangSelect) {
            syncNamaByKode();
        }
    });

    $(document).off('change', '#detail_nama_barang_penjualan').on('change', '#detail_nama_barang_penjualan', function () {
        if (!isSyncingBarangSelect) {
            syncKodeByNama();
        }
    });

    $(document).off('input', '#ongkir_pesanan').on('input', '#ongkir_pesanan', function () {
        renderTable();
    });

    $(document).off('click', '#btnTambahDetailPenjualan').on('click', '#btnTambahDetailPenjualan', async function () {
        if (formLocked) {
            Swal.fire({
                icon: 'warning',
                title: 'Data terkunci',
                text: 'Pesanan ini sudah tidak dapat diedit.'
            });
            return;
        }

        const itemData = getFormDetail();

        if (!validateDetail(itemData)) {
            return;
        }

        const duplicateIndex = findDuplicateIndex(itemData.kode_barang);

        if (editIndex === null) {
            if (duplicateIndex !== -1) {
                const result = await Swal.fire({
                    title: 'Kode barang sudah ada',
                    text: 'Apakah Anda yakin untuk memperbarui barang ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) {
                    return;
                }

                detailItems[duplicateIndex] = itemData;
                renderTable();
                clearDetailForm();

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Item barang berhasil diperbarui.',
                    timer: 1200,
                    showConfirmButton: false
                });
                return;
            }

            detailItems.push(itemData);
            renderTable();
            clearDetailForm();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'Item barang berhasil ditambahkan.',
                timer: 1200,
                showConfirmButton: false
            });
            return;
        }

        const duplicateOtherIndex = detailItems.findIndex((item, index) => {
            return item.kode_barang === itemData.kode_barang && index !== editIndex;
        });

        if (duplicateOtherIndex !== -1) {
            const result = await Swal.fire({
                title: 'Kode barang sudah ada',
                text: 'Data barang dengan kode ini sudah ada di daftar. Apakah Anda ingin menggantinya?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, ganti',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) {
                return;
            }

            detailItems[duplicateOtherIndex] = itemData;
            detailItems.splice(editIndex, 1);
        } else {
            detailItems[editIndex] = itemData;
        }

        renderTable();
        clearDetailForm();

        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Item barang berhasil diperbarui.',
            timer: 1200,
            showConfirmButton: false
        });
    });

    $(document).off('click', '.btn-edit-detail-penjualan').on('click', '.btn-edit-detail-penjualan', function () {
        if (formLocked) {
            return;
        }

        const index = $(this).data('index');
        const item = detailItems[index];

        if (!item) {
            return;
        }

        const barang = getBarangByKode(item.kode_barang);

        isSyncingBarangSelect = true;

        if (barang) {
            $('#detail_kategori_barang_penjualan').val(barang.kode_kategori || '').trigger('change.select2');
        }

        isSyncingBarangSelect = false;

        applyKategoriFilter();

        isSyncingBarangSelect = true;

        $('#detail_kode_barang_penjualan').val(item.kode_barang).trigger('change.select2');
        $('#detail_nama_barang_penjualan').val(item.nama_barang).trigger('change.select2');
        $('#detail_qty_penjualan').val(item.qty);
        $('#detail_harga_satuan_penjualan').val(item.harga_satuan);

        isSyncingBarangSelect = false;

        editIndex = index;

        $('#btnTambahDetailPenjualan')
            .text('Update Item')
            .removeClass('btn-info')
            .addClass('btn-warning');

        $('html, body').animate({
            scrollTop: $('#detail_kode_barang_penjualan').offset().top - 120
        }, 300);
    });

    $(document).off('click', '.btn-delete-detail-penjualan').on('click', '.btn-delete-detail-penjualan', async function () {
        if (formLocked) {
            return;
        }

        const index = $(this).data('index');

        const result = await Swal.fire({
            title: 'Hapus item ini?',
            text: 'Data detail barang akan dihapus dari daftar.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) {
            return;
        }

        detailItems.splice(index, 1);
        renderTable();
        clearDetailForm();

        Swal.fire({
            icon: 'success',
            title: 'Terhapus',
            text: 'Item detail berhasil dihapus.',
            timer: 1200,
            showConfirmButton: false
        });
    });

    $(document).off('click', '#btnResetPenjualan').on('click', '#btnResetPenjualan', function () {
        resetFormPenjualan();

        Swal.fire({
            icon: 'info',
            title: 'Form direset',
            text: 'Form transaksi penjualan telah dikosongkan.',
            timer: 1200,
            showConfirmButton: false
        });
    });

    $(document).off('click', '#btnViewPenjualan').on('click', '#btnViewPenjualan', function () {
        $('#modalViewPenjualan').modal('show');
        initDataTablePenjualan();
    });

    $(document).off('click', '.btn-edit-penjualan').on('click', '.btn-edit-penjualan', function () {
        const kodePesanan = $(this).data('kode');
        loadDataPenjualanUntukEdit(kodePesanan);
    });

    $(document).off('click', '.btn-delete-penjualan').on('click', '.btn-delete-penjualan', function () {
        const kodePesanan = $(this).data('kode');
        hapusDataPenjualan(kodePesanan);
    });

    $(document).off('click', '#btnSavePenjualan').on('click', '#btnSavePenjualan', function () {
        if (formLocked) {
            Swal.fire({
                icon: 'warning',
                title: 'Data terkunci',
                text: 'Pesanan ini sudah tidak dapat disimpan ulang oleh customer.'
            });
            return;
        }

        const tglPesanan = $('#tgl_pesanan').val();
        const jenisPengiriman = $('#jenis_pengiriman').val();
        const jenisPemesanan = $('#jenis_pemesanan').val();
        const statusPesanan = $('#status_pesanan').val();
        const mode = $('#form_mode_penjualan').val();
        const kodeEdit = $('#edit_kode_pesanan').val();
        const kodeCustomer = $('#kode_customer').val();


        if (!tglPesanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal wajib diisi',
                text: 'Tanggal pesanan harus diisi.'
            });
            return;
        }

        if (!jenisPengiriman) {
            Swal.fire({
                icon: 'warning',
                title: 'Jenis pengiriman wajib dipilih',
                text: 'Jenis pengiriman harus dipilih.'
            });
            return;
        }



        if (!statusPesanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Status pesanan wajib dipilih',
                text: 'Status pesanan harus dipilih.'
            });
            return;
        }

        if (config.requireCustomerSelection && $('#kode_customer').length && !kodeCustomer) {
            Swal.fire({
                icon: 'warning',
                title: 'Customer wajib dipilih',
                text: 'Silakan pilih customer terlebih dahulu.'
            });
            return;
        }

        if (detailItems.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Detail masih kosong',
                text: 'Minimal harus ada satu item detail penjualan.'
            });
            return;
        }

        if (!$('#metode_pembayaran').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Metode pembayaran belum dipilih',
                text: 'Silakan pilih metode pembayaran terlebih dahulu.'
            });
            return;
        }

        if ($('#metode_pembayaran').val() === 'Transfer Bank' && !$('#bank_tujuan').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Bank tujuan belum dipilih',
                text: 'Silakan pilih rekening bank tujuan.'
            });
            return;
        }

        const url = mode === 'edit'
            ? `${config.updateUrlBase}/${kodeEdit}`
            : config.storeUrl;

        const payload = buildPlainPayload();

        $.ajax({
            url: url,
            type: 'POST',
            data: payload,
            beforeSend: function () {
                Swal.fire({
                    title: mode === 'edit' ? 'Mengupdate data' : 'Menyimpan data',
                    text: 'Mohon tunggu, transaksi sedang diproses.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: mode === 'edit' ? 'Update berhasil' : 'Penjualan berhasil',
                    html: mode === 'edit'
                        ? `<div>Data transaksi berhasil diperbarui.</div><div class="mt-2"><strong>Kode Pesanan: ${response.kode_pesanan || kodeEdit}</strong></div>`
                        : `<div>Data telah tersimpan.</div><div class="mt-2"><strong>Kode Pesanan: ${response.kode_pesanan}</strong></div>`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    resetFormPenjualan();

                    if (tablePenjualan) {
                        tablePenjualan.ajax.reload(null, false);
                    }
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: mode === 'edit' ? 'Gagal mengupdate' : 'Gagal menyimpan',
                    html: getErrorMessage(xhr, 'Terjadi kesalahan saat memproses data.')
                });
            }
        });
    });

    $(document).off('click', '#btnLanjutkanCustom').on('click', '#btnLanjutkanCustom', async function () {
        const kodePesanan = $('#edit_kode_pesanan').val();

        if (!kodePesanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Kode pesanan tidak ditemukan',
                text: 'Silakan buka data pesanan terlebih dahulu.'
            });
            return;
        }

        const result = await Swal.fire({
            title: 'Lanjutkan pesanan custom?',
            text: 'Pesanan custom akan tetap tercatat dan status custom menjadi dilanjutkan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: `${config.approveCustomUrlBase}/${kodePesanan}`,
            type: 'POST',
            data: {
                _token: config.csrfToken
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Memproses pesanan',
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
                    text: response.message || 'Pesanan custom dilanjutkan.'
                }).then(() => {
                    $('#status_custom').val('lanjutkan');
                    $('#customApprovalSection').hide();
                });

                if (tablePenjualan) {
                    tablePenjualan.ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal memproses',
                    html: getErrorMessage(xhr, 'Pesanan tidak berhasil diproses.')
                });
            }
        });
    });

    $(document).off('click', '#btnTolakCustom').on('click', '#btnTolakCustom', async function () {
        const kodePesanan = $('#edit_kode_pesanan').val();

        if (!kodePesanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Kode pesanan tidak ditemukan',
                text: 'Silakan buka data pesanan terlebih dahulu.'
            });
            return;
        }

        const result = await Swal.fire({
            title: 'Tidak lanjutkan pesanan custom?',
            text: 'Pesanan tetap tercatat sebagai custom, tetapi status custom menjadi tidak dilanjutkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya',
            cancelButtonText: 'Batal'
        });

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: `${config.rejectCustomUrlBase}/${kodePesanan}`,
            type: 'POST',
            data: {
                _token: config.csrfToken
            },
            beforeSend: function () {
                Swal.fire({
                    title: 'Memproses perubahan',
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
                    text: response.message || 'Pesanan custom tidak dilanjutkan.'
                }).then(() => {
                    $('#status_custom').val('batal');
                    $('#customApprovalSection').hide();
                });

                if (tablePenjualan) {
                    tablePenjualan.ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal memproses',
                    html: getErrorMessage(xhr, 'Perubahan tidak berhasil diproses.')
                });
            }
        });
    });

    let customData = {
        jenis_pemesanan: '',
        spesifikasi_tambahan: '',
        harga_estimasi: '',
        custom_locked: false
    };

    function toggleCustomSection() {
        const jenisPemesanan = $('#jenis_pemesanan').val();

        if (jenisPemesanan === 'Custom') {
            $('#customPenjualanSection').show();
        } else {
            $('#customPenjualanSection').hide();
            $('#spesifikasi_tambahan').val('');
            $('#harga_estimasi').val('');
            $('#status_custom').val('');
            $('#customApprovalSection').hide();
        }

        applyCustomRoleState();
    }

    function applyCustomRoleState() {
        const isCustom = $('#jenis_pemesanan').val() === 'Custom';

        if (!isCustom) {
            $('#customApprovalSection').hide();
            return;
        }

        if (config.isAdmin) {
            $('#harga_estimasi').prop('readonly', false);
            $('#spesifikasi_tambahan').prop('readonly', false);
            $('#customApprovalSection').hide();
        } else {
            $('#harga_estimasi').prop('readonly', true);

            const hargaEstimasi = parseFloat($('#harga_estimasi').val()) || 0;
            const statusCustom = $('#status_custom').val();

            if (hargaEstimasi > 0 && !statusCustom) {
                $('#customApprovalSection').show();
            } else {
                $('#customApprovalSection').hide();
            }
        }
    }

    // $(document).off('change', '#jenis_pengiriman').on('change', '#jenis_pengiriman', function () {
    //     setOngkirByJenis();
    // });

    $(document).off('change', '#jenis_pemesanan').on('change', '#jenis_pemesanan', function () {
        toggleCustomSection();
    });;

    function formatKategoriOption(option) {
    if (!option.id) {
        return option.text;
    }

    return option.text;
}


    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildBarangOptionKode(barang) {
        const disabled = parseFloat(barang.kapasitas) <= 0 ? 'disabled' : '';

        return `
            <option
                value="${escapeHtml(barang.kode_barang)}"
                data-kode="${escapeHtml(barang.kode_barang)}"
                data-nama="${escapeHtml(barang.nama_barang)}"
                data-kategori="${escapeHtml(barang.kode_kategori)}"
                data-nama-kategori="${escapeHtml(barang.nama_kategori)}"
                data-kapasitas="${escapeHtml(barang.kapasitas)}"
                data-harga="${escapeHtml(barang.harga_jual)}"
                ${disabled}
            >
                ${escapeHtml(barang.kode_barang)}
            </option>
        `;
    }

    function buildBarangOptionNama(barang) {
        const disabled = parseFloat(barang.kapasitas) <= 0 ? 'disabled' : '';

        return `
            <option
                value="${escapeHtml(barang.nama_barang)}"
                data-kode="${escapeHtml(barang.kode_barang)}"
                data-nama="${escapeHtml(barang.nama_barang)}"
                data-kategori="${escapeHtml(barang.kode_kategori)}"
                data-nama-kategori="${escapeHtml(barang.nama_kategori)}"
                data-kapasitas="${escapeHtml(barang.kapasitas)}"
                data-harga="${escapeHtml(barang.harga_jual)}"
                ${disabled}
            >
                ${escapeHtml(barang.nama_barang)}
            </option>
        `;
    }

    function applyKategoriFilter() {
        const selectedKategori = $('#detail_kategori_barang_penjualan').val();

        const filteredBarang = barangList.filter(function (barang) {
            if (!selectedKategori) {
                return true;
            }

            return String(barang.kode_kategori) === String(selectedKategori);
        });

        let kodeOptions = '<option value="">Pilih Kode Barang</option>';
        let namaOptions = '<option value="">Pilih Nama Barang</option>';

        filteredBarang.forEach(function (barang) {
            kodeOptions += buildBarangOptionKode(barang);
            namaOptions += buildBarangOptionNama(barang);
        });

        isSyncingBarangSelect = true;

        $('#detail_kode_barang_penjualan')
            .html(kodeOptions)
            .val('')
            .trigger('change');

        $('#detail_nama_barang_penjualan')
            .html(namaOptions)
            .val('')
            .trigger('change');

        $('#detail_harga_satuan_penjualan').val('');

        isSyncingBarangSelect = false;
    }

    $(document).off('click', '.btn-upload-bukti-penjualan').on('click', '.btn-upload-bukti-penjualan', function () {
        const kodePesanan = $(this).data('kode');

        $('#upload_kode_pesanan').val(kodePesanan);
        $('#upload_kode_pesanan_text').text(kodePesanan);
        $('#bukti_pembayaran').val('');

        $('#modalUploadBuktiPenjualan').modal('show');
    });

    $(document).off('submit', '#formUploadBuktiPenjualan').on('submit', '#formUploadBuktiPenjualan', function (e) {
        e.preventDefault();

        const kodePesanan = $('#upload_kode_pesanan').val();
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
                $('#modalUploadBuktiPenjualan').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message || 'Bukti pembayaran berhasil diunggah.'
                });

                if (tablePenjualan) {
                    tablePenjualan.ajax.reload(null, false);
                }

                if ($('#edit_kode_pesanan').val() === kodePesanan) {
                    resetFormPenjualan();
                }
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

    function initRajaOngkir() {
        if ($('#rajaongkir_destination').hasClass('select2-hidden-accessible')) {
            $('#rajaongkir_destination').select2('destroy');
        }

        $('#rajaongkir_destination').select2({
            width: '100%',
            placeholder: 'Ketik tujuan, contoh: Darmo Surabaya',
            minimumInputLength: 3,
            ajax: {
                url: config.searchDestinationUrl,
                type: 'GET',
                dataType: 'json',
                delay: 500,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (response) {
                    const items = response.data || [];

                    return {
                        results: items.map(function (item) {
                            const label = item.label || [
                                item.subdistrict_name,
                                item.district_name,
                                item.city_name,
                                item.province_name,
                                item.zip_code
                            ].filter(Boolean).join(', ');

                            return {
                                id: item.id,
                                text: label,
                                raw: item
                            };
                        })
                    };
                }
            }
        });

        $('#rajaongkir_destination')
            .off('select2:select')
            .on('select2:select', function (e) {
                const item = e.params.data.raw || {};

                $('#provinsi_tujuan').val(item.province_name || '');
                $('#kota_tujuan').val(item.city_name || item.district_name || '');
                $('#layanan_kurir').html('<option value="">Cek ongkir terlebih dahulu</option>');
                $('#ongkir_pesanan').val(0);
                $('#estimasi_pengiriman').val('');
                $('#jenis_pengiriman').val('');

                renderTable();
            });

        $(document).off('change', '#kurir').on('change', '#kurir', function () {
            $('#layanan_kurir').html('<option value="">Cek ongkir terlebih dahulu</option>');
            $('#ongkir_pesanan').val(0);
            $('#estimasi_pengiriman').val('');
            $('#jenis_pengiriman').val('');
            renderTable();
        });

        $(document).off('click', '#btnCekOngkir').on('click', '#btnCekOngkir', function () {
            const destination = $('#rajaongkir_destination').val();
            const courier = $('#kurir').val();
            const weight = $('#berat_pengiriman').val();

            if (!destination) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tujuan belum dipilih',
                    text: 'Silakan pilih tujuan pengiriman terlebih dahulu.'
                });
                return;
            }

            if (!courier) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Kurir belum dipilih',
                    text: 'Silakan pilih kurir terlebih dahulu.'
                });
                return;
            }

            $.ajax({
                url: config.checkOngkirUrl,
                type: 'POST',
                data: {
                    _token: config.csrfToken,
                    destination: destination,
                    courier: courier,
                    weight: weight
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Mengecek ongkir',
                        text: 'Mengambil data dari RajaOngkir.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close();

                    const data = response.data || [];
                    let options = '<option value="">Pilih Layanan</option>';

                    if (data.length === 0) {
                        options = '<option value="">Layanan tidak tersedia</option>';
                    }

                    data.forEach(function (item) {
                        const service = item.service || item.name || item.code || '-';
                        const description = item.description || '';
                        const cost = parseFloat(item.cost || item.price || 0);
                        const etd = item.etd || item.estimated || '-';

                        options += `
                            <option value="${service}"
                                data-cost="${cost}"
                                data-etd="${etd}"
                                data-description="${description}">
                                ${service} - ${formatRupiah(cost)} - Estimasi ${etd}
                            </option>
                        `;
                    });

                    $('#layanan_kurir').html(options);
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal cek ongkir',
                        html: getErrorMessage(xhr, 'Ongkir gagal dicek.')
                    });
                }
            });
        });

        $(document).off('change', '#layanan_kurir').on('change', '#layanan_kurir', function () {
            const selected = $(this).find(':selected');

            const layanan = $(this).val() || '';
            const cost = parseFloat(selected.data('cost')) || 0;
            const etd = selected.data('etd') || '';

            $('#jenis_pengiriman').val(layanan);
            $('#ongkir_pesanan').val(cost);
            $('#estimasi_pengiriman').val(etd);

            renderTable();
        });
    }

    initSelectBarang();
    applyKategoriFilter();

    clearHeaderForm();
    clearDetailForm();

    renderTable();
    initPaymentMethod();
    initRajaOngkir();
};