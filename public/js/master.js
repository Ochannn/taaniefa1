function initDataTable() {
    if ($('#myTable').length) {
        if ($.fn.DataTable.isDataTable('#myTable')) {
            $('#myTable').DataTable().destroy();
        }

        $('#myTable').DataTable({
            paging: true,
            searching: true,
            ordering: true
        });
    }
}

function initMasterCrud() {
    if (typeof window.masterConfig === 'undefined') {
        return;
    }

    let config = window.masterConfig;

    $(document).off('submit', config.addFormSelector).on('submit', config.addFormSelector, function (e) {
        e.preventDefault();

        let $form = $(this);
        let $btnSubmit = $form.find('button[type="submit"]');

        $btnSubmit.prop('disabled', true).text('Menyimpan...');

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
                        timer: 2200,
                        timerProgressBar: true
                    }).then(function () {
                        loadContent(config.indexUrl);
                    });
                }
            },
            error: function (xhr) {
                let pesan = 'Terjadi kesalahan saat menyimpan data.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    pesan = Object.values(xhr.responseJSON.errors)
                        .map(function (item) {
                            return item[0];
                        })
                        .join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: pesan
                });
            },
            complete: function () {
                $btnSubmit.prop('disabled', false).text('Save Data');
            }
        });
    });

    $(document).off('click', '.btn-edit').on('click', '.btn-edit', function () {
        let data = $(this).data();

        $('#edit_kode_lama').val(data.id);

        Object.keys(data).forEach(function (key) {
            if (key !== 'id') {
                let $field = $('#edit_' + key);
                if ($field.length) {
                    $field.val(data[key]).trigger('change');
                }
            }
        });

        $('#modalEditData').modal('show');
    });

    $(document).off('submit', config.editFormSelector).on('submit', config.editFormSelector, function (e) {
        e.preventDefault();

        let id = $(config.editKeySelector).val();
        let $form = $(this);
        let $btnSubmit = $form.find('button[type="submit"]');
        let updateUrl = config.updateUrl.replace(':id', id);

        $btnSubmit.prop('disabled', true).text('Memperbarui...');

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
                        timer: 2200,
                        timerProgressBar: true
                    }).then(function () {
                        loadContent(config.indexUrl);
                    });
                }
            },
            error: function (xhr) {
                let pesan = 'Gagal memperbarui data.';

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    pesan = Object.values(xhr.responseJSON.errors)
                        .map(function (item) {
                            return item[0];
                        })
                        .join('\n');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: pesan
                });
            },
            complete: function () {
                $btnSubmit.prop('disabled', false).text('Update Data');
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
            cancelButtonText: 'Batal'
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
                                timer: 2200,
                                timerProgressBar: true
                            }).then(function () {
                                loadContent(config.indexUrl);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Data gagal dihapus.'
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

        $(config.editKeySelector).val('');
    });
}

function loadContent(url) {
    $.get(url, function (response) {
        $('#main-content').html(response);
        initDataTable();
        initMasterCrud();
    });
}

$(document).ready(function () {
    initDataTable();
    initMasterCrud();
});