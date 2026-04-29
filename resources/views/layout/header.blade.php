<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CV. Syavir Jaya Utama</title>

    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/transaksi.css') }}" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #075eea;
            --primary-blue-dark: #053b96;
            --primary-blue-soft: #eaf2ff;
            --secondary-blue: #0ea5e9;
            --text-dark: #172033;
            --text-muted: #7a8699;
            --border-soft: #e8edf5;
            --card-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
            --card-shadow-hover: 0 18px 45px rgba(15, 23, 42, 0.14);
            --radius-lg: 20px;
        }

        table.dataTable th,
        table.dataTable td {
            text-align: left !important;
        }

        body {
            background: #f5f8fc;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
            min-height: 100vh;
            background: #f5f8fc;
        }

        .app-sidebar {
            background: linear-gradient(180deg, #052c6f 0%, #073f9f 55%, #062b70 100%);
            color: #ffffff;
            padding: 24px 18px;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            padding: 4px 8px;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12);
        }

        .brand-title {
            font-size: 17px;
            font-weight: 800;
            line-height: 1.1;
            margin: 0;
            color: #ffffff;
        }

        .brand-subtitle {
            font-size: 12px;
            color: rgba(255,255,255,0.74);
            margin: 3px 0 0;
        }

        .sidebar-menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar-link,
        .sidebar-collapse-toggle {
            border: 0;
            width: 100%;
            background: transparent;
            color: rgba(255,255,255,0.82);
            border-radius: 14px;
            padding: 13px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 600;
            text-align: left;
            cursor: pointer;
            transition: all 0.22s ease;
            text-decoration: none;
        }

        .sidebar-link:hover,
        .sidebar-link.active,
        .sidebar-collapse-toggle:hover {
            color: #ffffff;
            text-decoration: none;
            background: linear-gradient(135deg, #0b7cff, #075eea);
            box-shadow: 0 12px 24px rgba(5, 94, 234, 0.32);
        }

        .sidebar-link i,
        .sidebar-collapse-toggle i {
            width: 22px;
            font-size: 15px;
            text-align: center;
        }

        .sidebar-arrow {
            margin-left: auto;
            width: auto !important;
            font-size: 11px !important;
            opacity: 0.72;
        }

        .sidebar-submenu {
            display: none;
            margin: 4px 0 6px 36px;
            padding-left: 10px;
            border-left: 1px solid rgba(255,255,255,0.18);
        }

        .sidebar-submenu.show {
            display: block;
        }

        .sidebar-submenu a {
            display: block;
            color: rgba(255,255,255,0.74);
            font-size: 13px;
            font-weight: 600;
            padding: 9px 10px;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .sidebar-submenu a:hover {
            color: #ffffff;
            background: rgba(255,255,255,0.12);
            text-decoration: none;
        }

        .sidebar-help {
            margin-top: 34px;
            padding: 18px;
            border-radius: 18px;
            background: rgba(255,255,255,0.11);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.09);
        }

        .sidebar-help h6 {
            color: #ffffff;
            font-weight: 800;
            margin-bottom: 6px;
        }

        .sidebar-help p {
            color: rgba(255,255,255,0.72);
            font-size: 12px;
            margin-bottom: 14px;
        }

        .btn-support {
            width: 100%;
            border: 0;
            background: #0b7cff;
            color: #ffffff;
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 700;
        }

        .app-main {
            min-width: 0;
        }

        .app-topbar {
            min-height: 76px;
            background: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
            flex: 1;
        }

        .mobile-sidebar-toggle {
            display: none;
            border: 0;
            background: var(--primary-blue-soft);
            color: var(--primary-blue);
            width: 42px;
            height: 42px;
            border-radius: 13px;
        }

        .search-box {
            width: min(420px, 100%);
            height: 44px;
            border-radius: 13px;
            border: 1px solid var(--border-soft);
            background: #f8fbff;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 14px;
            color: var(--text-muted);
        }

        .search-box input {
            border: 0;
            outline: none;
            width: 100%;
            background: transparent;
            font-size: 13px;
            color: var(--text-dark);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .notification-btn {
            width: 42px;
            height: 42px;
            border: 1px solid var(--border-soft);
            background: #ffffff;
            border-radius: 14px;
            color: var(--primary-blue);
            position: relative;
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            background: #ef4444;
            color: #ffffff;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 10px;
            border-radius: 16px;
            background: #f8fbff;
            border: 1px solid var(--border-soft);
            cursor: pointer;
        }

        .profile-avatar {
            width: 38px;
            height: 38px;
            border-radius: 13px;
            background: linear-gradient(135deg, #075eea, #0ea5e9);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            text-transform: uppercase;
        }

        .profile-name {
            font-size: 13px;
            font-weight: 800;
            margin: 0;
            line-height: 1.1;
            color: var(--text-dark);
        }

        .profile-role {
            font-size: 11px;
            color: var(--text-muted);
            margin: 2px 0 0;
        }

        .profile-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            width: 210px;
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow: var(--card-shadow-hover);
            border-radius: 16px;
            padding: 8px;
            display: none;
            z-index: 1200;
        }

        .profile-menu.show {
            display: block;
        }

        .profile-menu a,
        .profile-menu button {
            width: 100%;
            border: 0;
            background: transparent;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            color: var(--text-dark);
            font-size: 13px;
            font-weight: 700;
            text-align: left;
            text-decoration: none;
            cursor: pointer;
        }

        .profile-menu a:hover,
        .profile-menu button:hover {
            background: var(--primary-blue-soft);
            color: var(--primary-blue);
            text-decoration: none;
        }

        .profile-menu .logout-button {
            color: #dc2626;
        }

        .profile-menu .logout-button:hover {
            background: #fff1f2;
            color: #dc2626;
        }

        .content-area {
            min-height: calc(100vh - 76px);
        }

        .hero-section {
            background: linear-gradient(135deg, #075eea 0%, #0473ff 52%, #0ea5e9 100%);
            color: #ffffff;
            padding: 34px 28px 86px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: "";
            position: absolute;
            width: 340px;
            height: 340px;
            border-radius: 50%;
            right: -120px;
            top: -160px;
            background: rgba(255,255,255,0.12);
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }

        .hero-title {
            font-size: 30px;
            font-weight: 850;
            margin: 0 0 8px;
            color: #ffffff;
        }

        .hero-subtitle {
            margin: 0;
            color: rgba(255,255,255,0.84);
            font-size: 15px;
        }

        .date-pill {
            border: 1px solid rgba(255,255,255,0.22);
            background: rgba(255,255,255,0.16);
            color: #ffffff;
            border-radius: 15px;
            padding: 12px 15px;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 9px;
            white-space: nowrap;
        }

        .dashboard-content {
            padding: 0 28px 32px;
            margin-top: -58px;
            position: relative;
            z-index: 3;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 22px;
        }

        .summary-card,
        .content-card,
        .menu-card,
        .info-card {
            background: #ffffff;
            border: 1px solid var(--border-soft);
            box-shadow: var(--card-shadow);
            border-radius: var(--radius-lg);
        }

        .summary-card {
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            min-height: 118px;
        }

        .summary-icon,
        .menu-icon,
        .info-icon {
            flex: 0 0 auto;
            width: 54px;
            height: 54px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .tone-blue { background: #eaf2ff; color: #075eea; }
        .tone-green { background: #eafbf2; color: #16a34a; }
        .tone-orange { background: #fff3e6; color: #f97316; }
        .tone-cyan { background: #e6faff; color: #0891b2; }
        .tone-purple { background: #f1edff; color: #6d5dfc; }
        .tone-indigo { background: #eef2ff; color: #4f46e5; }
        .tone-red { background: #fff1f2; color: #e11d48; }

        .summary-label {
            margin: 0 0 5px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 700;
        }

        .summary-value {
            margin: 0;
            font-size: 26px;
            font-weight: 850;
            color: var(--text-dark);
            line-height: 1.1;
        }

        .summary-desc {
            margin: 6px 0 0;
            color: var(--text-muted);
            font-size: 12px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(320px, 0.65fr);
            gap: 20px;
            margin-bottom: 22px;
        }

        .content-card {
            padding: 22px;
            min-width: 0;
        }

        .card-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 18px;
        }

        .card-title {
            margin: 0;
            font-size: 18px;
            font-weight: 850;
            color: var(--text-dark);
        }

        .card-subtitle {
            margin: 5px 0 0;
            color: var(--text-muted);
            font-size: 13px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .menu-card {
            border-radius: 18px;
            padding: 18px;
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            transition: all 0.22s ease;
            min-height: 112px;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--card-shadow-hover);
            border-color: rgba(7, 94, 234, 0.28);
        }

        .menu-title {
            margin: 0 0 5px;
            font-size: 15px;
            font-weight: 850;
            color: var(--text-dark);
        }

        .menu-desc {
            margin: 0;
            color: var(--text-muted);
            font-size: 12.5px;
            line-height: 1.45;
        }

        .chart-box {
            height: 210px;
            display: flex;
            align-items: end;
            gap: 10px;
            padding: 20px 4px 4px;
            border-bottom: 1px solid var(--border-soft);
        }

        .chart-bar {
            flex: 1;
            min-width: 18px;
            border-radius: 999px 999px 8px 8px;
            background: linear-gradient(180deg, #0b7cff, #b8dcff);
        }

        .chart-summary {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
        }

        .chart-summary strong {
            display: block;
            font-size: 20px;
            color: var(--text-dark);
        }

        .chart-summary span {
            font-size: 12px;
            color: var(--text-muted);
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 800;
            background: #eafbf2;
            color: #16a34a;
        }

        .transaction-grid,
        .report-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .report-card {
            padding: 18px;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid var(--border-soft);
            box-shadow: var(--card-shadow);
        }

        .report-card h6 {
            font-size: 15px;
            font-weight: 850;
            margin: 12px 0 6px;
            color: var(--text-dark);
        }

        .report-card p {
            font-size: 12.5px;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.45;
        }

        .info-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 22px;
        }

        .info-card {
            padding: 17px;
            display: flex;
            gap: 13px;
            align-items: flex-start;
        }

        .info-card h6 {
            margin: 0 0 5px;
            font-size: 13px;
            font-weight: 850;
            color: var(--text-dark);
        }

        .info-card p {
            margin: 0;
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.45;
        }

        .sidebar-backdrop {
            display: none;
        }

        .master-page {
            padding: 28px;
        }
        .master-card {
            padding: 24px;
        }
        .master-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }
        .master-table-wrapper {
            border-radius: 14px;
            overflow-x: auto;
        }
        .master-table {
            background: #ffffff;
            margin-bottom: 0 !important;
        }
        .master-table thead th {
            background: #f8fbff;
            color: #172033;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
            vertical-align: middle;
        }
        .master-table tbody td {
            color: #334155;
            font-size: 13px;
            vertical-align: middle;
            white-space: nowrap;
        }
        .master-table .btn {
            margin: 2px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
        }
        .modal-content {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
        }
        .modal-header {
            border-bottom: 0;
        }
        .modal-footer {
            border-top: 1px solid #e8edf5;
        }
        .form-control {
            border-radius: 10px;
        }
        .sidebar-arrow {
            transition: transform 0.2s ease;
        }
        .sidebar-arrow.rotate {
            transform: rotate(180deg);
        }

        @media (max-width: 1199px) {
            .app-shell {
                grid-template-columns: 92px minmax(0, 1fr);
            }

            .app-sidebar {
                padding: 22px 12px;
            }

            .brand-wrap {
                justify-content: center;
                padding: 0;
            }

            .brand-text,
            .sidebar-link span,
            .sidebar-collapse-toggle span,
            .sidebar-collapse-toggle .sidebar-arrow,
            .sidebar-help {
                display: none;
            }

            .sidebar-link,
            .sidebar-collapse-toggle {
                justify-content: center;
                padding: 14px 8px;
            }

            .sidebar-link i,
            .sidebar-collapse-toggle i {
                width: auto;
                font-size: 17px;
            }

            .sidebar-submenu {
                margin: 6px 0;
                padding: 8px;
                border-left: 0;
                background: rgba(255,255,255,0.1);
                border-radius: 12px;
            }

            .sidebar-submenu a {
                font-size: 11px;
                padding: 8px;
                text-align: center;
            }

            .summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .dashboard-grid {
                grid-template-columns: 1fr;
            }

            .transaction-grid,
            .report-grid,
            .info-strip {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .app-shell {
                display: block;
            }

            .app-sidebar {
                position: fixed;
                left: -285px;
                width: 270px;
                transition: left 0.25s ease;
            }

            .app-sidebar.show {
                left: 0;
            }

            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 998;
            }

            .sidebar-backdrop.show {
                display: block;
            }

            .mobile-sidebar-toggle {
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            .brand-text,
            .sidebar-link span,
            .sidebar-collapse-toggle span,
            .sidebar-collapse-toggle .sidebar-arrow,
            .sidebar-help {
                display: block;
            }

            .sidebar-link,
            .sidebar-collapse-toggle {
                justify-content: flex-start;
                padding: 13px 14px;
            }

            .sidebar-link i,
            .sidebar-collapse-toggle i {
                width: 22px;
                font-size: 15px;
            }

            .app-topbar {
                min-height: auto;
                padding: 14px 16px;
            }

            .search-box {
                display: none;
            }

            .profile-pill {
                padding: 0;
                border: 0;
                background: transparent;
            }

            .profile-text {
                display: none;
            }

            .hero-section {
                padding: 26px 16px 78px;
            }

            .hero-content {
                display: block;
            }

            .hero-title {
                font-size: 24px;
            }

            .hero-subtitle {
                font-size: 13px;
            }

            .date-pill {
                margin-top: 16px;
                width: max-content;
                padding: 10px 12px;
            }

            .dashboard-content {
                padding: 0 16px 26px;
                margin-top: -54px;
            }

            .summary-grid,
            .menu-grid,
            .transaction-grid,
            .report-grid,
            .info-strip {
                grid-template-columns: 1fr;
            }

            .summary-card {
                min-height: auto;
            }

            .content-card {
                padding: 18px;
            }

            .card-heading {
                display: block;
            }

            .chart-box {
                height: 170px;
            }

            @media (max-width: 767px) {
                .master-page {
                    padding: 18px 16px;
                }

                .master-card {
                    padding: 18px;
                }

                .master-top {
                    display: block;
                }

                .master-top .btn {
                    width: 100%;
                    margin-top: 14px;
                }
            }

            .payment-info-box {
                margin-top: 12px;
                padding: 16px;
                border-radius: 14px;
                background: #f8fbff;
                border: 1px solid #e8edf5;
            }

            .payment-info-box h6 {
                margin: 0 0 8px;
                color: #172033;
                font-weight: 800;
            }

            #tableRiwayatPenjualan .btn {
                margin: 2px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 700;
            }

            #tableRiwayatPenjualan td {
                vertical-align: middle;
            }

            #tableRiwayatPenjualan .badge {
                padding: 7px 10px;
                border-radius: 999px;
                font-size: 11px;
            }


        }
    </style>
</head>

<body>
@php
    $user = Auth::user();
@endphp

<div class="app-shell">
    <aside class="app-sidebar" id="appSidebar">
        <div class="brand-wrap">
            <div class="brand-icon">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="brand-text">
                <h4 class="brand-title">CV. Syavir Jaya Utama</h4>
                <p class="brand-subtitle">Panel Manajemen</p>
            </div>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('home') }}" class="sidebar-link active">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            @if($user && $user->isAdmin())
            <button type="button" class="sidebar-collapse-toggle" data-target="masterMenu" onclick="toggleSidebarMenu('masterMenu', this)">
                <i class="fas fa-database"></i>
                <span>Master Data</span>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </button>

                <div class="sidebar-submenu" id="masterMenu">
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'barang']) }}'); return false;">Master Barang</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'kategori']) }}'); return false;">Master Kategori</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'satuan']) }}'); return false;">Master Satuan</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'rekening-pembayaran']) }}'); return false;">
                        Master Rekening Pembayaran
                    </a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'role']) }}'); return false;">Master Role</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'user']) }}'); return false;">Master User</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'supplier']) }}'); return false;">Master Supplier</a>
                    <a href="#" onclick="loadContent('{{ route('ajax.master.index', ['module' => 'karyawan']) }}'); return false;">Master Karyawan</a>
                </div>
            @endif

            @if($user && ($user->isAdmin() || $user->isKaryawan() || $user->isCustomer()))
                <button type="button" class="sidebar-collapse-toggle" data-target="transaksiMenu" onclick="toggleSidebarMenu('transaksiMenu', this)">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Transaksi</span>
                    <i class="fas fa-chevron-down sidebar-arrow"></i>
                </button>

                <div class="sidebar-submenu" id="transaksiMenu">
                    @if($user->isAdmin() || $user->isKaryawan())
                        <a href="#" onclick="loadContent('{{ route('ajax.transaksi.pembelian') }}'); return false;">Transaksi Pembelian</a>
                        <a href="#" onclick="loadContent('{{ route('ajax.transaksi.retur.pembelian') }}'); return false;">Retur Pembelian</a>
                    @endif

                    @if($user->isAdmin() || $user->isKaryawan() || $user->isCustomer())
                        <a href="#" onclick="loadContent('{{ route('ajax.transaksi.penjualan') }}'); return false;">Transaksi Penjualan</a>
                        <a href="#" onclick="loadContent('{{ route('ajax.transaksi.retur.penjualan') }}'); return false;">Retur Penjualan</a>
                        <a href="#" onclick="loadContent('{{ route('ajax.transaksi.riwayat.penjualan') }}'); return false;">Riwayat Transaksi</a>
                    @endif
                </div>
            @endif

            @if($user && ($user->isAdmin() || $user->isKaryawan()))
            <button type="button" class="sidebar-collapse-toggle" data-target="laporanMenu" onclick="toggleSidebarMenu('laporanMenu', this)">
                <i class="fas fa-chart-line"></i>
                <span>Laporan</span>
                <i class="fas fa-chevron-down sidebar-arrow"></i>
            </button>

                <div class="sidebar-submenu" id="laporanMenu">
                    <a href="#" onclick="loadContent('{{ route('ajax.laporan.stok') }}'); return false;">Laporan Stok Barang</a>
                </div>
            @endif
        </div>

        <div class="sidebar-help">
            <h6>Butuh Bantuan?</h6>
            <p>Gunakan menu sesuai hak akses untuk mengelola data sistem.</p>
            <button type="button" class="btn-support">Hubungi Admin</button>
        </div>
    </aside>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <main class="app-main">
        <nav class="app-topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-sidebar-toggle" id="mobileSidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari data, transaksi, laporan...">
                </div>
            </div>

            <div class="topbar-actions">
                <button type="button" class="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>

                <div class="profile-dropdown">
                    <div class="profile-pill" id="profileToggle">
                        <div class="profile-avatar">
                            {{ substr($user->nama_user ?? 'U', 0, 1) }}
                        </div>

                    <div class="profile-text">
                        <p class="profile-name">{{ $user->nama_user ?? 'User' }}</p>

                        <p class="profile-role">
                            @if($user && $user->isAdmin())
                                Administrator
                            @elseif($user && $user->isKaryawan())
                                Karyawan
                            @elseif($user && $user->isCustomer())
                                Customer
                            @else
                                Pengguna
                            @endif
                        </p>
                    </div>

                        <i class="fas fa-chevron-down" style="font-size: 11px; color: #7a8699;"></i>
                    </div>

                    <div class="profile-menu" id="profileMenu">
                        <a href="#" onclick="loadContent('{{ route('customer.profile') }}'); return false;">
                            <i class="fas fa-user"></i>
                            Profile
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="logout-button">
                                <i class="fas fa-sign-out-alt"></i>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div id="main-content" class="content-area">