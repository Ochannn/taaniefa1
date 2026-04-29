function initMasterDataTable() {
    if (typeof $ === 'undefined') {
        return;
    }

    if (!$.fn.DataTable) {
        return;
    }

    if (!$('#myTable').length) {
        return;
    }

    if ($.fn.DataTable.isDataTable('#myTable')) {
        $('#myTable').DataTable().destroy();
    }

    $('#myTable').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data tersedia',
            zeroRecords: 'Data tidak ditemukan',
            paginate: {
                previous: 'Sebelumnya',
                next: 'Berikutnya'
            }
        }
    });
}

function getMasterErrorMessage(xhr, defaultMessage) {
    let message = defaultMessage;

    if (xhr.responseJSON && xhr.responseJSON.errors) {
        message = Object.values(xhr.responseJSON.errors)
            .map(function (item) {
                return item[0];
            })
            .join('\n');
    } else if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    }

    return message;
}

function reloadMasterContent(indexUrl) {
    if (typeof window.loadContent === 'function') {
        window.loadContent(indexUrl);
    } else {
        window.location.href = indexUrl;
    }
}

function initMasterCrud() {
    if (typeof $ === 'undefined') {
        return;
    }

    if (typeof window.masterConfig === 'undefined') {
        return;
    }

    let config = window.masterConfig;

    $(document).off('click', '.btn-open-add-modal').on('click', '.btn-open-add-modal', function () {
        if ($(config.addModalSelector).length) {
            $(config.addModalSelector).modal('show');
        }
    });

    $(document).off('submit', config.addFormSelector).on('submit', config.addFormSelector, function (e) {
        e.preventDefault();

        let $form = $(this);
        let $btnSubmit = $form.find('button[type="submit"]');
        let originalText = $btnSubmit.html();

        $btnSubmit.prop('disabled', true).html('Menyimpan...');

        $.ajax({
            url: config.storeUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                if (response.success) {
                    $(config.addModalSelector).modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil disimpan.',
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true
                    }).then(function () {
                        reloadMasterContent(config.indexUrl);
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: response.message || 'Data belum berhasil disimpan.'
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: getMasterErrorMessage(xhr, 'Terjadi kesalahan saat menyimpan data.')
                });
            },
            complete: function () {
                $btnSubmit.prop('disabled', false).html(originalText);
            }
        });
    });

    $(document).off('click', '.btn-edit').on('click', '.btn-edit', function () {
        let data = $(this).data();

        if ($(config.editKeySelector).length) {
            $(config.editKeySelector).val(data.id);
        }

        Object.keys(data).forEach(function (key) {
            if (key !== 'id') {
                let $field = $('#edit_' + key);

                if ($field.length) {
                    $field.val(data[key]).trigger('change');
                }
            }
        });

        if ($(config.editModalSelector).length) {
            $(config.editModalSelector).modal('show');
        }
    });

    $(document).off('submit', config.editFormSelector).on('submit', config.editFormSelector, function (e) {
        e.preventDefault();

        let id = $(config.editKeySelector).val();
        let $form = $(this);
        let $btnSubmit = $form.find('button[type="submit"]');
        let originalText = $btnSubmit.html();
        let updateUrl = config.updateUrl.replace(':id', id);

        $btnSubmit.prop('disabled', true).html('Memperbarui...');

        $.ajax({
            url: updateUrl,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                if (response.success) {
                    $(config.editModalSelector).modal('hide');

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message || 'Data berhasil diperbarui.',
                        showConfirmButton: false,
                        timer: 1800,
                        timerProgressBar: true
                    }).then(function () {
                        reloadMasterContent(config.indexUrl);
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian',
                        text: response.message || 'Data belum berhasil diperbarui.'
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: getMasterErrorMessage(xhr, 'Gagal memperbarui data.')
                });
            },
            complete: function () {
                $btnSubmit.prop('disabled', false).html(originalText);
            }
        });
    });

    $(document).off('click', '.btn-delete').on('click', '.btn-delete', function () {
        let id = $(this).data('id');
        let deleteUrl = config.deleteUrl.replace(':id', id);

        Swal.fire({
            title: 'Yakin ingin menghapus?',
            text: 'Data ' + id + ' akan dihapus.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'POST',
                    data: {
                        _token: config.csrfToken,
                        _method: 'DELETE'
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message || 'Data berhasil dihapus.',
                                showConfirmButton: false,
                                timer: 1800,
                                timerProgressBar: true
                            }).then(function () {
                                reloadMasterContent(config.indexUrl);
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Perhatian',
                                text: response.message || 'Data belum berhasil dihapus.'
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: getMasterErrorMessage(xhr, 'Data gagal dihapus.')
                        });
                    }
                });
            }
        });
    });

    $(document).off('hidden.bs.modal', config.addModalSelector).on('hidden.bs.modal', config.addModalSelector, function () {
        if ($(config.addFormSelector).length) {
            $(config.addFormSelector)[0].reset();
        }

        if (config.autoCodeSelector && config.nextKode) {
            $(config.autoCodeSelector).val(config.nextKode);
        }
    });

    $(document).off('hidden.bs.modal', config.editModalSelector).on('hidden.bs.modal', config.editModalSelector, function () {
        if ($(config.editFormSelector).length) {
            $(config.editFormSelector)[0].reset();
        }

        if ($(config.editKeySelector).length) {
            $(config.editKeySelector).val('');
        }
    });
}

window.initMasterPage = function () {
    initMasterDataTable();
    initMasterCrud();
};

$(document).ready(function () {
    window.initMasterPage();
});