window.initTransaksiPenjualan = function (config) {
    const barangList = config.barangList || [];
    let detailItems = [];
    let editIndex = null;
    let tablePenjualan = null;
    let formLocked = false;

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

    function setFormLockState(locked) {
        formLocked = locked;

        const disableSelector = [
            '#tgl_pesanan',
            '#kode_customer',
            '#jenis_pesanan',
            '#alamat_kirim_pesanan',
            '#catatan_pesanan',
            '#detail_kode_barang_penjualan',
            '#detail_nama_barang_penjualan',
            '#detail_qty_penjualan',
            '#btnTambahDetailPenjualan'
        ].join(',');

        $(disableSelector).prop('disabled', locked);

        if (config.isAdmin) {
            $('#status_pesanan').prop('disabled', false);
        } else {
            $('#status_pesanan').prop('disabled', true);
        }

        $('#btnSavePenjualan').prop('disabled', locked);
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

    function clearDetailForm() {
        $('#detail_kode_barang_penjualan').val('');
        $('#detail_nama_barang_penjualan').val('');
        $('#detail_qty_penjualan').val('');
        $('#detail_harga_satuan_penjualan').val('');
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

        $('#jenis_pesanan').val('');
        $('#status_pesanan').val('Pending');
        $('#alamat_kirim_pesanan').val('');
        $('#ongkir_pesanan').val(0);
        $('#catatan_pesanan').val('');
        $('#form_mode_penjualan').val('create');
        $('#edit_kode_pesanan').val('');
        $('#btnSavePenjualan').text('Save Data');

        applyLockByStatus('Pending');
    }

    function syncNamaByKode() {
        const kode = $('#detail_kode_barang_penjualan').val();
        const selected = getBarangByKode(kode);

        if (selected) {
            $('#detail_nama_barang_penjualan').val(selected.nama_barang);
            $('#detail_harga_satuan_penjualan').val(selected.harga_jual || 0);
        } else {
            $('#detail_nama_barang_penjualan').val('');
            $('#detail_harga_satuan_penjualan').val('');
        }
    }

    function syncKodeByNama() {
        const nama = $('#detail_nama_barang_penjualan').val();
        const selected = getBarangByNama(nama);

        if (selected) {
            $('#detail_kode_barang_penjualan').val(selected.kode_barang);
            $('#detail_harga_satuan_penjualan').val(selected.harga_jual || 0);
        } else {
            $('#detail_kode_barang_penjualan').val('');
            $('#detail_harga_satuan_penjualan').val('');
        }
    }

    function generateRandomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function setOngkirByJenis() {
        const jenis = $('#jenis_pesanan').val();
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
                ? $('#kode_customer').val()
                : (config.customerAktifKode || $('#kode_customer').val()),
            tgl_pesanan: $('#tgl_pesanan').val(),
            jenis_pesanan: $('#jenis_pesanan').val(),
            status_pesanan: $('#status_pesanan').val(),
            alamat_kirim_pesanan: $('#alamat_kirim_pesanan').val(),
            ongkir_pesanan: $('#ongkir_pesanan').val() || 0,
            catatan_pesanan: $('#catatan_pesanan').val()
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
        clearDetailForm();
        renderTable();
        refreshKodePreview();
    }

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
                    data: 'jenis_pesanan',
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        if (config.isAdmin) {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-penjualan" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-penjualan" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;
                        }

                        if ((data.status_pesanan || '').toLowerCase() === 'pending') {
                            return `
                                <button type="button" class="btn btn-sm btn-primary btn-edit-penjualan" data-kode="${data.kode_pesanan}">
                                    Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-danger btn-delete-penjualan" data-kode="${data.kode_pesanan}">
                                    Delete
                                </button>
                            `;
                        }

                        return `<span class="text-muted">Locked</span>`;
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

                $('#jenis_pesanan').val(response.header.jenis_pesanan);
                $('#status_pesanan').val(response.header.status_pesanan);
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
                        : `Transaksi ${kodePesanan} berhasil dimuat, tetapi sudah tidak dapat diedit oleh customer.`
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
        syncNamaByKode();
    });

    $(document).off('change', '#detail_nama_barang_penjualan').on('change', '#detail_nama_barang_penjualan', function () {
        syncKodeByNama();
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

        $('#detail_kode_barang_penjualan').val(item.kode_barang);
        $('#detail_nama_barang_penjualan').val(item.nama_barang);
        $('#detail_qty_penjualan').val(item.qty);
        $('#detail_harga_satuan_penjualan').val(item.harga_satuan);

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
        const jenisPesanan = $('#jenis_pesanan').val();
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

        if (!jenisPesanan) {
            Swal.fire({
                icon: 'warning',
                title: 'Jenis pesanan wajib dipilih',
                text: 'Jenis pesanan harus dipilih.'
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

    $(document).off('change', '#jenis_pesanan').on('change', '#jenis_pesanan', function () {
        setOngkirByJenis();
    });

    clearHeaderForm();
    clearDetailForm();
    renderTable();
};