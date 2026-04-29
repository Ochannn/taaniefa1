        </div>
    </main>
</div>

<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="{{ asset('js/style.js') }}"></script>
<script src="{{ asset('js/master.js') }}"></script>
<script src="{{ asset('js/transaksi_pembelian.js') }}"></script>
<script src="{{ asset('js/transaksi_penjualan.js') }}"></script>
<script src="{{ asset('js/retur_pembelian.js') }}"></script>
<script src="{{ asset('js/retur_penjualan.js') }}"></script>

<script>
    window.toggleSidebarMenu = function (targetId, button) {
        const target = document.getElementById(targetId);

        if (!target) {
            return;
        }

        target.classList.toggle('show');

        if (button) {
            const arrow = button.querySelector('.sidebar-arrow');

            if (arrow) {
                arrow.classList.toggle('rotate');
            }
        }
    };

    window.safeRun = function (callback, name) {
        try {
            if (typeof callback === 'function') {
                callback();
            }
        } catch (error) {
            console.error('Error pada ' + name + ':', error);
        }
    };

    window.runPageInitializers = function () {
        window.safeRun(window.initMasterPage, 'initMasterPage');

        if (typeof window.initTransaksiPembelian === 'function' && typeof window.transaksiPembelianConfig !== 'undefined') {
            window.safeRun(function () {
                window.initTransaksiPembelian(window.transaksiPembelianConfig);
            }, 'initTransaksiPembelian');
        }

        if (typeof window.initTransaksiPenjualan === 'function' && typeof window.transaksiPenjualanConfig !== 'undefined') {
            window.safeRun(function () {
                window.initTransaksiPenjualan(window.transaksiPenjualanConfig);
            }, 'initTransaksiPenjualan');
        }

        if (typeof window.initReturPembelian === 'function' && typeof window.returPembelianConfig !== 'undefined') {
            window.safeRun(function () {
                window.initReturPembelian(window.returPembelianConfig);
            }, 'initReturPembelian');
        }

        if (typeof window.initReturPenjualan === 'function' && typeof window.returPenjualanConfig !== 'undefined') {
            window.safeRun(function () {
                window.initReturPenjualan(window.returPenjualanConfig);
            }, 'initReturPenjualan');
        }
    };

    window.loadContent = function (url) {
        const target = $('#main-content');

        if (!target.length) {
            window.location.href = url;
            return;
        }

        target.html(`
            <div style="padding: 32px;">
                <div class="content-card" style="padding: 24px;">
                    <strong>Memuat data...</strong>
                    <p style="margin: 6px 0 0; color: #7a8699;">Mohon tunggu sebentar.</p>
                </div>
            </div>
        `);

        $.ajax({
            url: url,
            type: 'GET',
            success: function (response) {
                target.html(response);

                window.runPageInitializers();

                $('#appSidebar').removeClass('show');
                $('#sidebarBackdrop').removeClass('show');
            },
            error: function (xhr) {
                let message = 'Terjadi kesalahan saat memuat halaman.';

                if (xhr.status === 404) {
                    message = 'Halaman tidak ditemukan. Periksa route menu.';
                } else if (xhr.status === 500) {
                    message = 'Terjadi kesalahan pada controller atau view.';
                } else if (xhr.status === 403) {
                    message = 'Anda tidak memiliki akses ke halaman ini.';
                } else if (xhr.status === 419) {
                    message = 'Sesi login berakhir. Silakan login ulang.';
                }

                target.html(`
                    <div style="padding: 32px;">
                        <div class="content-card" style="padding: 24px;">
                            <h4 style="margin: 0 0 8px; color: #172033;">Gagal Memuat Halaman</h4>
                            <p style="margin: 0; color: #7a8699;">${message}</p>
                        </div>
                    </div>
                `);

                console.error(xhr);
            }
        });
    };

    $(document).ready(function () {
        $('#mobileSidebarToggle').off('click').on('click', function () {
            $('#appSidebar').addClass('show');
            $('#sidebarBackdrop').addClass('show');
        });

        $('#sidebarBackdrop').off('click').on('click', function () {
            $('#appSidebar').removeClass('show');
            $('#sidebarBackdrop').removeClass('show');
        });

        $('#profileToggle').off('click').on('click', function (event) {
            event.stopPropagation();
            $('#profileMenu').toggleClass('show');
        });

        $('#profileMenu').off('click').on('click', function (event) {
            event.stopPropagation();
        });

        $(document).on('click', function () {
            $('#profileMenu').removeClass('show');
        });

        window.runPageInitializers();
    });
</script>

</body>
</html>