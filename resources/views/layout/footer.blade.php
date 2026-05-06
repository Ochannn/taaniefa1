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

    document.addEventListener('DOMContentLoaded', function () {
        const notificationToggle = document.getElementById('notificationToggle');
        const notificationMenu = document.getElementById('notificationMenu');
        const notificationList = document.getElementById('notificationList');
        const notificationBadge = document.getElementById('notificationBadge');
        const markAllReadBtn = document.getElementById('markAllReadBtn');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function escapeHtml(text) {
            if (!text) return '';

            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatDate(dateString) {
            if (!dateString) return '';

            const date = new Date(dateString);

            if (isNaN(date.getTime())) {
                return dateString;
            }

            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function updateBadge(total) {
            if (!notificationBadge) {
                return;
            }

            if (total > 0) {
                notificationBadge.style.display = 'flex';
                notificationBadge.textContent = total > 99 ? '99+' : total;
            } else {
                notificationBadge.style.display = 'none';
                notificationBadge.textContent = '0';
            }
        }

        function renderNotifikasi(items) {
            if (!notificationList) {
                return;
            }

            if (!items || items.length === 0) {
                notificationList.innerHTML = '<div class="notification-empty">Tidak ada notifikasi.</div>';
                return;
            }

            notificationList.innerHTML = items.map(item => {
                const unreadClass = item.dibaca_at ? '' : 'unread';
                const dot = item.dibaca_at ? '' : '<span class="notification-dot"></span>';

                return `
                    <button type="button"
                            class="notification-item ${unreadClass}"
                            data-id="${item.id}"
                            data-url="${item.url ? escapeHtml(item.url) : ''}">
                        <div class="notification-item-top">
                            <p class="notification-item-title">${escapeHtml(item.judul)}</p>
                            ${dot}
                        </div>
                        <p class="notification-item-message">${escapeHtml(item.pesan)}</p>
                        <div class="notification-item-time">${formatDate(item.created_at)}</div>
                    </button>
                `;
            }).join('');
        }

        function loadNotifikasi() {
            fetch('{{ route("ajax.notifikasi.index") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(response => {
                if (!response.success) {
                    return;
                }

                updateBadge(response.jumlah_belum_dibaca || 0);
                renderNotifikasi(response.data || []);
            })
            .catch(error => {
                console.error('Gagal mengambil notifikasi:', error);
            });
        }

        function markAsRead(id, callback) {
            fetch(`{{ url('/ajax/notifikasi') }}/${id}/dibaca`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(response => {
                loadNotifikasi();

                if (typeof callback === 'function') {
                    callback(response);
                }
            })
            .catch(error => {
                console.error('Gagal menandai notifikasi:', error);
            });
        }

        function markAllAsRead() {
            fetch('{{ route("ajax.notifikasi.baca_semua") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(() => {
                loadNotifikasi();
            })
            .catch(error => {
                console.error('Gagal menandai semua notifikasi:', error);
            });
        }

        if (notificationToggle && notificationMenu) {
            notificationToggle.addEventListener('click', function (event) {
                event.stopPropagation();
                notificationMenu.classList.toggle('show');

                if (notificationMenu.classList.contains('show')) {
                    loadNotifikasi();
                }
            });
        }

        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function (event) {
                event.stopPropagation();
                markAllAsRead();
            });
        }

        document.addEventListener('click', function (event) {
            const item = event.target.closest('.notification-item');

            if (item) {
                const id = item.getAttribute('data-id');
                const url = item.getAttribute('data-url');

                markAsRead(id, function () {
                    notificationMenu.classList.remove('show');

                    if (url) {
                        if (typeof loadContent === 'function') {
                            loadContent(url);
                        } else {
                            window.location.href = url;
                        }
                    }
                });

                return;
            }

            if (
                notificationMenu &&
                notificationToggle &&
                !notificationMenu.contains(event.target) &&
                !notificationToggle.contains(event.target)
            ) {
                notificationMenu.classList.remove('show');
            }
        });

        loadNotifikasi();
        setInterval(loadNotifikasi, 30000);
    });
</script>

</body>
</html>