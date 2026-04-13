window.initTransaksiPembelian = function (config) {
    const barangList = config.barangList || [];
    let detailItems = [];
    let editIndex = null;
    let tablePembelian = null;

    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID').format(value || 0);
    }

    function clearDetailForm() {
        $('#detail_kode_barang').val('');
        $('#detail_nama_barang').val('');
        $('#detail_qty').val('');
        $('#detail_harga').val('');
        editIndex = null;

        $('#btnTambahDetail')
            .text('Tambah Item')
            .removeClass('btn-warning')
            .addClass('btn-info');
    }

    function clearHeaderForm() {
        $('#tgl_pembelian').val(config.defaultDate || '');
        $('#kode_supplier').val('');
        $('#catatan_pembelian').val('');
        $('#form_mode').val('create');
        $('#edit_kode_pembelian').val('');
        $('#btnSavePembelian').text('Save Data');
    }

    function syncNamaByKode() {
        const kode = $('#detail_kode_barang').val();
        const selected = barangList.find(item => item.kode_barang === kode);

        if (selected) {
            $('#detail_nama_barang').val(selected.nama_barang);
        }
    }

    function syncKodeByNama() {
        const nama = $('#detail_nama_barang').val();
        const selected = barangList.find(item => item.nama_barang === nama);

        if (selected) {
            $('#detail_kode_barang').val(selected.kode_barang);
        }
    }

    function renderTable() {
        let html = '';
        let total = 0;

        if (detailItems.length === 0) {
            html = `
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        Belum ada item pembelian.
                    </td>
                </tr>
            `;
        } else {
            detailItems.forEach((item, index) => {
                item.subtotal_barang = Number(item.qty) * Number(item.harga_barang);
                total += Number(item.subtotal_barang);

                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.kode_barang}</td>
                        <td>${item.nama_barang}</td>
                        <td>${formatNumber(item.qty)}</td>
                        <td>${formatNumber(item.harga_barang)}</td>
                        <td>${formatNumber(item.subtotal_barang)}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary btn-edit-detail" data-index="${index}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete-detail" data-index="${index}">
                                Delete
                            </button>
                        </td>
                    </tr>
                `;
            });
        }

        $('#detailTableBody').html(html);
        $('#total_item_text').text(detailItems.length);
        $('#total_pembelian_text').text(formatNumber(total));
        $('#total_pembelian').val(total);
    }

    function getFormDetail() {
        const kodeBarang = $('#detail_kode_barang').val();
        const namaBarang = $('#detail_nama_barang').val();
        const qty = parseFloat($('#detail_qty').val()) || 0;
        const harga = parseFloat($('#detail_harga').val()) || 0;

        return {
            kode_barang: kodeBarang,
            nama_barang: namaBarang,
            qty: qty,
            harga_barang: harga,
            subtotal_barang: qty * harga
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

        if (itemData.qty <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Qty tidak valid',
                text: 'Qty harus lebih dari 0.'
            });
            return false;
        }

        if (itemData.harga_barang < 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Harga tidak valid',
                text: 'Harga tidak boleh negatif.'
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
            tgl_pembelian: $('#tgl_pembelian').val(),
            kode_supplier: $('#kode_supplier').val(),
            catatan_pembelian: $('#catatan_pembelian').val()
        };

        detailItems.forEach((item, index) => {
            payload[`items[${index}][kode_barang]`] = item.kode_barang;
            payload[`items[${index}][nama_barang]`] = item.nama_barang;
            payload[`items[${index}][qty]`] = item.qty;
            payload[`items[${index}][harga_barang]`] = item.harga_barang;
        });

        return payload;
    }

    function refreshKodePreview() {
        if (!config.reloadUrl) {
            return;
        }

        $.get(config.reloadUrl, function (html) {
            const preview = $('<div>').html(html).find('#kode_pembelian_preview').val();
            if (preview) {
                $('#kode_pembelian_preview').val(preview);
            }
        });
    }

    function resetFormPembelian() {
        detailItems = [];
        editIndex = null;
        clearHeaderForm();
        clearDetailForm();
        renderTable();
        refreshKodePreview();
    }

    function initDataTablePembelian() {
        if ($.fn.DataTable.isDataTable('#tableViewPembelian')) {
            tablePembelian = $('#tableViewPembelian').DataTable();
            tablePembelian.ajax.reload(null, false);
            return;
        }

        tablePembelian = $('#tableViewPembelian').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: config.dataUrl,
                type: 'GET',
                dataSrc: 'data'
            },
            columns: [
                { data: 'kode_pembelian' },
                { data: 'tgl_pembelian' },
                { data: 'kode_supplier' },
                {
                    data: 'nama_supplier',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: 'catatan_pembelian',
                    render: function (data) {
                        return data ? data : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                            <button type="button" class="btn btn-sm btn-primary btn-edit-pembelian" data-kode="${data.kode_pembelian}">
                                Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger btn-delete-pembelian" data-kode="${data.kode_pembelian}">
                                Delete
                            </button>
                        `;
                    }
                }
            ]
        });
    }

    function loadDataPembelianUntukEdit(kodePembelian) {
        $.ajax({
            url: `${config.showUrlBase}/${kodePembelian}`,
            type: 'GET',
            success: function (response) {
                if (!response.success) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Data tidak ditemukan',
                        text: response.message || 'Data pembelian tidak ditemukan.'
                    });
                    return;
                }

                $('#form_mode').val('edit');
                $('#edit_kode_pembelian').val(response.header.kode_pembelian);
                $('#kode_pembelian_preview').val(response.header.kode_pembelian);
                $('#tgl_pembelian').val(response.header.tgl_pembelian);
                $('#kode_supplier').val(response.header.kode_supplier);
                $('#catatan_pembelian').val(response.header.catatan_pembelian || '');
                $('#btnSavePembelian').text('Update Data');

                detailItems = (response.details || []).map(item => {
                    return {
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang,
                        qty: parseFloat(item.qty) || 0,
                        harga_barang: parseFloat(item.harga_barang) || 0,
                        subtotal_barang: (parseFloat(item.qty) || 0) * (parseFloat(item.harga_barang) || 0)
                    };
                });

                editIndex = null;
                clearDetailForm();
                renderTable();

                $('#modalViewPembelian').modal('hide');

                $('html, body').animate({
                    scrollTop: 0
                }, 300);

                Swal.fire({
                    icon: 'success',
                    title: 'Mode edit aktif',
                    text: `Transaksi ${kodePembelian} berhasil dimuat.`
                });
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal mengambil data',
                    html: getErrorMessage(xhr, 'Data pembelian gagal diambil.')
                });
            }
        });
    }

    function hapusDataPembelian(kodePembelian) {
        Swal.fire({
            title: 'Hapus transaksi ini?',
            text: `Transaksi ${kodePembelian} akan dihapus dan stok akan disesuaikan kembali.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            $.ajax({
                url: `${config.deleteUrlBase}/${kodePembelian}`,
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
                        text: response.message || 'Data pembelian berhasil dihapus.'
                    });

                    if (tablePembelian) {
                        tablePembelian.ajax.reload(null, false);
                    }

                    if ($('#edit_kode_pembelian').val() === kodePembelian) {
                        resetFormPembelian();
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal menghapus',
                        html: getErrorMessage(xhr, 'Data pembelian gagal dihapus.')
                    });
                }
            });
        });
    }

    $(document).off('change', '#detail_kode_barang').on('change', '#detail_kode_barang', function () {
        syncNamaByKode();
    });

    $(document).off('change', '#detail_nama_barang').on('change', '#detail_nama_barang', function () {
        syncKodeByNama();
    });

    $(document).off('click', '#btnTambahDetail').on('click', '#btnTambahDetail', async function () {
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

    $(document).off('click', '.btn-edit-detail').on('click', '.btn-edit-detail', function () {
        const index = $(this).data('index');
        const item = detailItems[index];

        if (!item) {
            return;
        }

        $('#detail_kode_barang').val(item.kode_barang);
        $('#detail_nama_barang').val(item.nama_barang);
        $('#detail_qty').val(item.qty);
        $('#detail_harga').val(item.harga_barang);

        editIndex = index;

        $('#btnTambahDetail')
            .text('Update Item')
            .removeClass('btn-info')
            .addClass('btn-warning');

        $('html, body').animate({
            scrollTop: $('#detail_kode_barang').offset().top - 120
        }, 300);
    });

    $(document).off('click', '.btn-delete-detail').on('click', '.btn-delete-detail', async function () {
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

    $(document).off('click', '#btnResetPembelian').on('click', '#btnResetPembelian', function () {
        resetFormPembelian();

        Swal.fire({
            icon: 'info',
            title: 'Form direset',
            text: 'Form transaksi pembelian telah dikosongkan.',
            timer: 1200,
            showConfirmButton: false
        });
    });

    $(document).off('click', '#btnViewPembelian').on('click', '#btnViewPembelian', function () {
        $('#modalViewPembelian').modal('show');
        initDataTablePembelian();
    });

    $(document).off('click', '.btn-edit-pembelian').on('click', '.btn-edit-pembelian', function () {
        const kodePembelian = $(this).data('kode');
        loadDataPembelianUntukEdit(kodePembelian);
    });

    $(document).off('click', '.btn-delete-pembelian').on('click', '.btn-delete-pembelian', function () {
        const kodePembelian = $(this).data('kode');
        hapusDataPembelian(kodePembelian);
    });

    $(document).off('click', '#btnSavePembelian').on('click', '#btnSavePembelian', function () {
        const tglPembelian = $('#tgl_pembelian').val();
        const kodeSupplier = $('#kode_supplier').val();
        const mode = $('#form_mode').val();
        const kodeEdit = $('#edit_kode_pembelian').val();

        if (!tglPembelian) {
            Swal.fire({
                icon: 'warning',
                title: 'Tanggal wajib diisi',
                text: 'Tanggal pembelian harus diisi.'
            });
            return;
        }

        if (!kodeSupplier) {
            Swal.fire({
                icon: 'warning',
                title: 'Supplier wajib dipilih',
                text: 'Supplier harus dipilih.'
            });
            return;
        }

        if (detailItems.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Detail masih kosong',
                text: 'Minimal harus ada satu item detail pembelian.'
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
                    title: mode === 'edit' ? 'Update berhasil' : 'Pembelian berhasil',
                    html: mode === 'edit'
                        ? `<div>Data transaksi berhasil diperbarui.</div><div class="mt-2"><strong>Kode Pembelian: ${response.kode_pembelian || kodeEdit}</strong></div>`
                        : `<div>Data telah tersimpan.</div><div class="mt-2"><strong>Kode Pembelian: ${response.kode_pembelian}</strong></div>`,
                    confirmButtonText: 'OK'
                }).then(() => {
                    resetFormPembelian();

                    if (tablePembelian) {
                        tablePembelian.ajax.reload(null, false);
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

    clearHeaderForm();
    clearDetailForm();
    renderTable();
};