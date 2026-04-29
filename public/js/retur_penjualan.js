window.initReturPenjualan = function (config) {
    let penjualanDetails = [];
    let detailItems = [];
    let editIndex = null;
    let tableReturPenjualan = null;
    let isSyncingBarangSelect = false;

    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID').format(value || 0);
    }

    function formatRupiah(value) {
        return 'Rp.' + formatNumber(value || 0);
    }

    function getBarangByKode(kode) {
        return penjualanDetails.find(item => item.kode_barang === kode);
    }

    function getBarangByNama(nama) {
        return penjualanDetails.find(item => item.nama_barang === nama);
    }

    function getQtySudahRetur(item) {
        if (!item) {
            return 0;
        }

        return parseFloat(item.qty_sudah_retur ?? 0) || 0;
    }

    function getSisaRetur(item) {
        if (!item) {
            return 0;
        }

        if (item.sisa_retur !== undefined && item.sisa_retur !== null && item.sisa_retur !== '') {
            return parseFloat(item.sisa_retur) || 0;
        }

        const qtyJual = parseFloat(item.qty ?? 0) || 0;
        const qtySudahRetur = getQtySudahRetur(item);

        return qtyJual - qtySudahRetur;
    }

    function formatBarangOption(option) {
        if (!option.id) {
            return option.text;
        }

        const el = $(option.element);
        const kode = el.data('kode') || '-';
        const nama = el.data('nama') || '-';
        const qty = parseFloat(el.data('qty')) || 0;
        const qtySudahRetur = parseFloat(el.data('qty-sudah-retur')) || 0;
        const sisaRetur = parseFloat(el.data('sisa-retur')) || 0;
        const harga = parseFloat(el.data('harga')) || 0;

        return $(`
            <div class="barang-option-wrap">
                <div class="barang-option-kiri">
                    <span class="barang-option-title">${nama}</span>
                    <span class="barang-option-subtitle">${kode}</span>
                </div>
                <div class="barang-option-kanan">
                    <span class="barang-option-stok">Qty Jual: ${formatNumber(qty)}</span>
                    <span class="barang-option-stok">Sudah Retur: ${formatNumber(qtySudahRetur)}</span>
                    <span class="barang-option-stok">Sisa: ${formatNumber(sisaRetur)}</span>
                    <span class="barang-option-harga">${formatRupiah(harga)}</span>
                </div>
            </div>
        `);
    }

    function initSelect() {
        $('#kode_pesanan_retur').select2({
            width: '100%',
            placeholder: 'Pilih Kode Penjualan'
        });

        $('#detail_kode_barang_retur_penjualan').select2({
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

        $('#detail_nama_barang_retur_penjualan').select2({
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

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function buildOptionKode(item) {
        const qtySudahRetur = getQtySudahRetur(item);
        const sisaRetur = getSisaRetur(item);

        return `
            <option
                value="${escapeHtml(item.kode_barang)}"
                data-kode="${escapeHtml(item.kode_barang)}"
                data-nama="${escapeHtml(item.nama_barang)}"
                data-qty="${escapeHtml(item.qty)}"
                data-qty-sudah-retur="${escapeHtml(qtySudahRetur)}"
                data-sisa-retur="${escapeHtml(sisaRetur)}"
                data-harga="${escapeHtml(item.harga_satuan)}"
            >
                ${escapeHtml(item.kode_barang)}
            </option>
        `;
    }

    function buildOptionNama(item) {
        const qtySudahRetur = getQtySudahRetur(item);
        const sisaRetur = getSisaRetur(item);

        return `
            <option
                value="${escapeHtml(item.nama_barang)}"
                data-kode="${escapeHtml(item.kode_barang)}"
                data-nama="${escapeHtml(item.nama_barang)}"
                data-qty="${escapeHtml(item.qty)}"
                data-qty-sudah-retur="${escapeHtml(qtySudahRetur)}"
                data-sisa-retur="${escapeHtml(sisaRetur)}"
                data-harga="${escapeHtml(item.harga_satuan)}"
            >
                ${escapeHtml(item.nama_barang)}
            </option>
        `;
    }

    function renderBarangOptions() {
        let kodeOptions = '<option value="">Pilih Kode Barang</option>';
        let namaOptions = '<option value="">Pilih Nama Barang</option>';

        penjualanDetails.forEach(function (item) {
            kodeOptions += buildOptionKode(item);
            namaOptions += buildOptionNama(item);
        });

        isSyncingBarangSelect = true;

        $('#detail_kode_barang_retur_penjualan')
            .html(kodeOptions)
            .val('')
            .trigger('change');

        $('#detail_nama_barang_retur_penjualan')
            .html(namaOptions)
            .val('')
            .trigger('change');

        $('#detail_qty_jual_retur_penjualan').val('');
        $('#detail_qty_retur_penjualan').val('');
        $('#detail_harga_retur_penjualan').val('');

        isSyncingBarangSelect = false;
    }

    function clearDetailForm() {
        isSyncingBarangSelect = true;

        $('#detail_kode_barang_retur_penjualan').val('').trigger('change');
        $('#detail_nama_barang_retur_penjualan').val('').trigger('change');
        $('#detail_qty_jual_retur_penjualan').val('');
        $('#detail_qty_retur_penjualan')
            .val('')
            .removeAttr('max')
            .prop('disabled', false);
        $('#detail_harga_retur_penjualan').val('');

        isSyncingBarangSelect = false;
        editIndex = null;

        $('#btnTambahDetailReturPenjualan')
            .text('Tambah Item')
            .removeClass('btn-warning')
            .addClass('btn-info');
    }

    function syncNamaByKode() {
        if (isSyncingBarangSelect) {
            return;
        }

        const kode = $('#detail_kode_barang_retur_penjualan').val();
        const selected = getBarangByKode(kode);

        isSyncingBarangSelect = true;

        if (selected) {
            const sisaRetur = getSisaRetur(selected);

            $('#detail_nama_barang_retur_penjualan')
                .val(selected.nama_barang)
                .trigger('change');

            $('#detail_qty_jual_retur_penjualan').val(selected.qty);
            $('#detail_harga_retur_penjualan').val(selected.harga_satuan);

            $('#detail_qty_retur_penjualan')
                .val('')
                .attr('max', sisaRetur)
                .prop('disabled', sisaRetur <= 0);

            if (sisaRetur <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang sudah diretur seluruhnya',
                    text: 'Barang ini telah diretur semuanya dan tidak dapat diretur kembali.'
                });
            }
        } else {
            $('#detail_nama_barang_retur_penjualan').val('').trigger('change');
            $('#detail_qty_jual_retur_penjualan').val('');
            $('#detail_qty_retur_penjualan').val('').removeAttr('max').prop('disabled', false);
            $('#detail_harga_retur_penjualan').val('');
        }

        isSyncingBarangSelect = false;
    }

    function syncKodeByNama() {
        if (isSyncingBarangSelect) {
            return;
        }

        const nama = $('#detail_nama_barang_retur_penjualan').val();
        const selected = getBarangByNama(nama);

        isSyncingBarangSelect = true;

        if (selected) {
            const sisaRetur = getSisaRetur(selected);

            $('#detail_kode_barang_retur_penjualan')
                .val(selected.kode_barang)
                .trigger('change');

            $('#detail_qty_jual_retur_penjualan').val(selected.qty);
            $('#detail_harga_retur_penjualan').val(selected.harga_satuan);

            $('#detail_qty_retur_penjualan')
                .val('')
                .attr('max', sisaRetur)
                .prop('disabled', sisaRetur <= 0);

            if (sisaRetur <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Barang sudah diretur seluruhnya',
                    text: 'Barang ini telah diretur semuanya dan tidak dapat diretur kembali.'
                });
            }
        } else {
            $('#detail_kode_barang_retur_penjualan').val('').trigger('change');
            $('#detail_qty_jual_retur_penjualan').val('');
            $('#detail_qty_retur_penjualan').val('').removeAttr('max').prop('disabled', false);
            $('#detail_harga_retur_penjualan').val('');
        }

        isSyncingBarangSelect = false;
    }

    function renderTable() {
        let html = '';
        let total = 0;

        if (detailItems.length === 0) {
            html = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        Belum ada item retur penjualan.
                    </td>
                </tr>
            `;
        } else {
            detailItems.forEach(function (item, index) {
                item.subtotal_retur = Number(item.qty_retur) * Number(item.harga_satuan);
                total += Number(item.subtotal_retur);

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.kode_barang}</td>
                        <td>${item.nama_barang}</td>
                        <td>${formatNumber(item.qty_jual)}</td>
                        <td>${formatNumber(item.qty_retur)}</td>
                        <td>${formatNumber(item.harga_satuan)}</td>
                        <td>${formatNumber(item.subtotal_retur)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary btn-edit-detail-retur-penjualan" data-index="${index}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete-detail-retur-penjualan" data-index="${index}">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#detailTableBodyReturPenjualan').html(html);
        $('#total_item_retur_penjualan_text').text(detailItems.length);
        $('#total_retur_penjualan_text').text(formatNumber(total));
        $('#total_retur_penjualan').val(total);
    }

    function getFormDetail() {
        const kodeBarang = $('#detail_kode_barang_retur_penjualan').val();
        const namaBarang = $('#detail_nama_barang_retur_penjualan').val();
        const qtyJual = parseFloat($('#detail_qty_jual_retur_penjualan').val()) || 0;
        const qtyRetur = parseFloat($('#detail_qty_retur_penjualan').val()) || 0;
        const hargaSatuan = parseFloat($('#detail_harga_retur_penjualan').val()) || 0;

        const barang = getBarangByKode(kodeBarang);
        const qtySudahRetur = getQtySudahRetur(barang);
        const sisaRetur = getSisaRetur(barang);

        return {
            kode_barang: kodeBarang,
            nama_barang: namaBarang,
            qty_jual: qtyJual,
            qty_sudah_retur: qtySudahRetur,
            sisa_retur: sisaRetur,
            qty_retur: qtyRetur,
            harga_satuan: hargaSatuan,
            subtotal_retur: qtyRetur * hargaSatuan
        };
    }

    function validateDetail(item) {
        if (!item.kode_barang || !item.nama_barang) {
            Swal.fire({
                icon: 'warning',
                title: 'Barang belum dipilih',
                text: 'Pilih kode barang atau nama barang terlebih dahulu.'
            });
            return false;
        }

        if (item.sisa_retur <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Barang sudah diretur seluruhnya',
                text: 'Barang ini telah diretur semuanya dan tidak dapat diretur kembali.'
            });
            return false;
        }

        if (item.qty_retur <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Qty retur tidak valid',
                text: 'Qty retur harus lebih dari 0.'
            });
            return false;
        }

        if (item.qty_retur > item.sisa_retur) {
            Swal.fire({
                icon: 'warning',
                title: 'Qty retur melebihi sisa',
                text: `Qty retur tidak boleh melebihi sisa retur. Sisa yang dapat diretur: ${formatNumber(item.sisa_retur)}.`
            });
            return false;
        }

        return true;
    }

    function findDuplicateIndex(kodeBarang) {
        return detailItems.findIndex(item => item.kode_barang === kodeBarang);
    }

    function buildPayload() {
        const payload = {
            _token: config.csrfToken,
            tgl_penjualan: $('#tgl_penjualan_retur').val(),
            kode_customer: $('#kode_customer_retur').val(),
            alamat: $('#alamat_retur_penjualan').val(),
            kode_pesanan: $('#kode_pesanan_retur').val(),
            keterangan: $('#keterangan_retur_penjualan').val()
        };

        detailItems.forEach(function (item, index) {
            payload[`items[${index}][kode_barang]`] = item.kode_barang;
            payload[`items[${index}][nama_barang]`] = item.nama_barang;
            payload[`items[${index}][qty_retur]`] = item.qty_retur;
        });

        return payload;
    }

    function loadPenjualan(kodePesanan) {
        if (!kodePesanan) {
            penjualanDetails = [];
            detailItems = [];
            $('#nama_customer_retur').val('');
            $('#kode_customer_retur').val('');
            $('#alamat_retur_penjualan').val('');
            renderBarangOptions();
            renderTable();
            return;
        }

        $.ajax({
            url: `${config.detailPenjualanUrlBase}/${kodePesanan}`,
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

                $('#kode_customer_retur').val(response.header.kode_customer || '');
                $('#nama_customer_retur').val(response.header.nama_customer || '');
                $('#alamat_retur_penjualan').val(response.header.alamat || '');

                penjualanDetails = response.details || [];
                detailItems = [];

                renderBarangOptions();
                renderTable();
                clearDetailForm();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mengambil data',
                    text: 'Data penjualan gagal diambil.'
                });
            }
        });
    }

    function resetForm() {
        penjualanDetails = [];
        detailItems = [];
        editIndex = null;

        $('#tgl_penjualan_retur').val(config.defaultDate || '');
        $('#kode_pesanan_retur').val('').trigger('change');
        $('#nama_customer_retur').val('');
        $('#kode_customer_retur').val('');
        $('#alamat_retur_penjualan').val('');
        $('#keterangan_retur_penjualan').val('');

        renderBarangOptions();
        renderTable();
        clearDetailForm();
    }

    function initDataTableReturPenjualan() {
        if ($.fn.DataTable.isDataTable('#tableViewReturPenjualan')) {
            tableReturPenjualan = $('#tableViewReturPenjualan').DataTable();
            tableReturPenjualan.ajax.reload(null, false);
            return;
        }

        tableReturPenjualan = $('#tableViewReturPenjualan').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: config.dataUrl,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'kode_rpenjualan' },
                { data: 'tgl_penjualan' },
                { data: 'kode_pesanan' },
                {
                    data: 'nama_customer',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'alamat',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'keterangan',
                    render: function (data) {
                        return data ? data : '-';
                    }
                }
            ]
        });
    }

    $(document).off('change', '#kode_pesanan_retur').on('change', '#kode_pesanan_retur', function () {
        loadPenjualan($(this).val());
    });

    $(document).off('change', '#detail_kode_barang_retur_penjualan').on('change', '#detail_kode_barang_retur_penjualan', function () {
        syncNamaByKode();
    });

    $(document).off('change', '#detail_nama_barang_retur_penjualan').on('change', '#detail_nama_barang_retur_penjualan', function () {
        syncKodeByNama();
    });

    $(document).off('click', '#btnTambahDetailReturPenjualan').on('click', '#btnTambahDetailReturPenjualan', async function () {
        const itemData = getFormDetail();

        if (!validateDetail(itemData)) {
            return;
        }

        const duplicateIndex = findDuplicateIndex(itemData.kode_barang);

        if (editIndex === null) {
            if (duplicateIndex !== -1) {
                const result = await Swal.fire({
                    title: 'Barang sudah ada',
                    text: 'Barang ini sudah ada di daftar retur. Apakah ingin diperbarui?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, perbarui',
                    cancelButtonText: 'Batal'
                });

                if (!result.isConfirmed) {
                    return;
                }

                detailItems[duplicateIndex] = itemData;
            } else {
                detailItems.push(itemData);
            }
        } else {
            detailItems[editIndex] = itemData;
        }

        renderTable();
        clearDetailForm();
    });

    $(document).off('click', '.btn-edit-detail-retur-penjualan').on('click', '.btn-edit-detail-retur-penjualan', function () {
        const index = $(this).data('index');
        const item = detailItems[index];

        if (!item) {
            return;
        }

        isSyncingBarangSelect = true;

        $('#detail_kode_barang_retur_penjualan').val(item.kode_barang).trigger('change');
        $('#detail_nama_barang_retur_penjualan').val(item.nama_barang).trigger('change');
        $('#detail_qty_jual_retur_penjualan').val(item.qty_jual);
        $('#detail_qty_retur_penjualan').val(item.qty_retur);
        $('#detail_harga_retur_penjualan').val(item.harga_satuan);

        isSyncingBarangSelect = false;

        editIndex = index;

        $('#btnTambahDetailReturPenjualan')
            .text('Update Item')
            .removeClass('btn-info')
            .addClass('btn-warning');
    });

    $(document).off('click', '.btn-delete-detail-retur-penjualan').on('click', '.btn-delete-detail-retur-penjualan', async function () {
        const index = $(this).data('index');

        const result = await Swal.fire({
            title: 'Hapus item ini?',
            text: 'Data detail retur akan dihapus dari daftar.',
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
    });

    $(document).off('click', '#btnResetReturPenjualan').on('click', '#btnResetReturPenjualan', function () {
        resetForm();
    });

    $(document).off('click', '#btnViewReturPenjualan').on('click', '#btnViewReturPenjualan', function () {
        $('#modalViewReturPenjualan').modal('show');
        initDataTableReturPenjualan();
    });

    $(document).off('click', '#btnSaveReturPenjualan').on('click', '#btnSaveReturPenjualan', function () {
        if (!$('#tgl_penjualan_retur').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal wajib diisi',
                text: 'Tanggal retur harus diisi.'
            });
            return;
        }

        if (!$('#kode_pesanan_retur').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Kode penjualan wajib dipilih',
                text: 'Pilih transaksi penjualan yang akan diretur.'
            });
            return;
        }

        if (detailItems.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Detail masih kosong',
                text: 'Minimal harus ada satu barang yang diretur.'
            });
            return;
        }

        $.ajax({
            url: config.storeUrl,
            type: 'POST',
            data: buildPayload(),
            beforeSend: function () {
                Swal.fire({
                    title: 'Menyimpan retur penjualan',
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
                    title: 'Berhasil',
                    html: `<div>Retur penjualan berhasil disimpan.</div><div class="mt-2"><strong>Kode Retur: ${response.kode_rpenjualan}</strong></div>`
                }).then(() => {
                    const kodePesanan = $('#kode_pesanan_retur').val();

                    if (kodePesanan) {
                        loadPenjualan(kodePesanan);
                    } else {
                        resetForm();
                    }

                    if (tableReturPenjualan) {
                        tableReturPenjualan.ajax.reload(null, false);
                    }
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal menyimpan',
                    text: xhr.responseJSON?.message || 'Retur penjualan gagal disimpan.'
                });
            }
        });
    });

    initSelect();
    renderBarangOptions();
    resetForm();
};