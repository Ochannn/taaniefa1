-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 08 Apr 2026 pada 23.49
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbaniefa`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `laporan_stok_barang`
--

CREATE TABLE `laporan_stok_barang` (
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `stok_awal` int(10) NOT NULL,
  `stok_masuk` int(10) NOT NULL,
  `stok_keluar` int(10) NOT NULL,
  `stok_akhir` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_barang`
--

CREATE TABLE `master_barang` (
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kode_kategori` varchar(10) NOT NULL,
  `kode_satuan` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `kapasitas` int(100) NOT NULL,
  `harga_jual` varchar(50) NOT NULL,
  `stok_minimum` int(100) NOT NULL,
  `deskripsi_barang` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_barang`
--

INSERT INTO `master_barang` (`kode_barang`, `nama_barang`, `kode_kategori`, `kode_satuan`, `kode_user`, `kapasitas`, `harga_jual`, `stok_minimum`, `deskripsi_barang`) VALUES
('KBR001', 'IBC 1000 Liter', 'KKR001', 'KS001', 'KUSR001', 13, '5000', 10, 'asdasd'),
('KBR002', 'IBC 2000 Liter', 'KKR001', 'KS001', 'KUSR001', 52, '300000', 5, 'sdfsfsdf');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_customer`
--

CREATE TABLE `master_customer` (
  `kode_customer` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_customer` varchar(100) NOT NULL,
  `nohp_customer` varchar(20) DEFAULT NULL,
  `alamat_customer` text DEFAULT NULL,
  `email_customer` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_jenis_palet`
--

CREATE TABLE `master_jenis_palet` (
  `kode_palet` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_palet` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_jenis_palet`
--

INSERT INTO `master_jenis_palet` (`kode_palet`, `kode_user`, `nama_palet`) VALUES
('KJP001', 'KUSR001', 'Kayu'),
('KJP002', 'KUSR001', 'Besi'),
('KJP003', 'KUSR001', 'Plastik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_karyawan`
--

CREATE TABLE `master_karyawan` (
  `kode_karyawan` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_karyawan` varchar(100) NOT NULL,
  `jabatan_karyawan` varchar(100) NOT NULL,
  `alamat_karyawan` varchar(100) NOT NULL,
  `nohp_karyawan` int(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_kategori_barang`
--

CREATE TABLE `master_kategori_barang` (
  `kode_kategori` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_kategori_barang`
--

INSERT INTO `master_kategori_barang` (`kode_kategori`, `kode_user`, `nama_kategori`) VALUES
('KKR001', 'KUSR001', 'Alat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_kualitas`
--

CREATE TABLE `master_kualitas` (
  `kode_kualitas` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_kualitas` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_kualitas`
--

INSERT INTO `master_kualitas` (`kode_kualitas`, `kode_user`, `nama_kualitas`) VALUES
('KK001', 'KUSR001', 'KW 1'),
('KK002', 'KUSR001', 'KW 2'),
('KK003', 'KUSR001', 'KW 3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_role`
--

CREATE TABLE `master_role` (
  `kode_role` varchar(10) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_role`
--

INSERT INTO `master_role` (`kode_role`, `nama_role`) VALUES
('KRL001', 'Admin'),
('KRL002', 'Karyawan'),
('KRL003', 'Customer'),
('KRL004', 'hacker');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_satuan`
--

CREATE TABLE `master_satuan` (
  `kode_satuan` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_satuan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_satuan`
--

INSERT INTO `master_satuan` (`kode_satuan`, `kode_user`, `nama_satuan`) VALUES
('KS001', 'KUSR001', 'unit');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_supplier`
--

CREATE TABLE `master_supplier` (
  `kode_supplier` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `nohp_supplier` int(25) NOT NULL,
  `alamat_supplier` varchar(100) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_supplier`
--

INSERT INTO `master_supplier` (`kode_supplier`, `kode_user`, `nama_supplier`, `nohp_supplier`, `alamat_supplier`, `keterangan`) VALUES
('KSP001', 'KUSR001', 'rio', 12341234, 'sdfsdf', 'asdsdsd');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_user`
--

CREATE TABLE `master_user` (
  `kode_user` varchar(10) NOT NULL,
  `kode_role` varchar(10) NOT NULL,
  `nama_user` varchar(100) NOT NULL,
  `email_user` varchar(100) NOT NULL,
  `pw_user` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `master_user`
--

INSERT INTO `master_user` (`kode_user`, `kode_role`, `nama_user`, `email_user`, `pw_user`) VALUES
('KUSR001', 'KRL001', 'admin', 'admin@gmail.com', '123456');

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('Lpu2979ugkLFSsnFfT0n9FYsEVqB9xkmB959fm11', NULL, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTGdvOUhTR2laMzM5amNzSFlyckZ5dVVycEZoQ0lsYlpNcXpHaDBSTiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czoyNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1775574969);

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_pembelian`
--

CREATE TABLE `transaksi_pembelian` (
  `kode_pembelian` varchar(10) NOT NULL,
  `tgl_pembelian` date NOT NULL,
  `kode_supplier` varchar(10) NOT NULL,
  `kode_user` varchar(10) NOT NULL,
  `total_pembelian` int(50) NOT NULL,
  `catatan_pembelian` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_pembelian_detail`
--

CREATE TABLE `transaksi_pembelian_detail` (
  `id` int(11) NOT NULL,
  `kode_pembelian` varchar(10) NOT NULL,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `qty` int(10) NOT NULL,
  `harga_barang` int(10) NOT NULL,
  `subtotal_barang` int(100) NOT NULL,
  `date_entry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_penjualan`
--

CREATE TABLE `transaksi_penjualan` (
  `kode_pesanan` varchar(10) NOT NULL,
  `tgl_pesanan` date NOT NULL,
  `kode_customer` varchar(10) NOT NULL,
  `jenis_pesanan` varchar(10) NOT NULL,
  `status_pesanan` varchar(10) NOT NULL,
  `alamat_kirim_pesanan` varchar(100) NOT NULL,
  `ongkir_pesanan` int(100) NOT NULL,
  `catatan_pesanan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_penjualan_custom`
--

CREATE TABLE `transaksi_penjualan_custom` (
  `kode_pesanan` varchar(10) NOT NULL,
  `kode_palet` varchar(10) NOT NULL,
  `kode_kualitas` varchar(10) NOT NULL,
  `warna` varchar(100) NOT NULL,
  `kapasitas_custom` int(10) NOT NULL,
  `spesifikasi_tambahan` varchar(100) NOT NULL,
  `harga_estimasi` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `transaksi_penjualan_detail`
--

CREATE TABLE `transaksi_penjualan_detail` (
  `id` int(11) NOT NULL,
  `kode_pesanan` varchar(10) NOT NULL,
  `kode_barang` varchar(10) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `qty` int(10) NOT NULL,
  `harga_satuan` int(10) NOT NULL,
  `subtotal_pesanan` int(10) NOT NULL,
  `date_entry` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `laporan_stok_barang`
--
ALTER TABLE `laporan_stok_barang`
  ADD KEY `kode_barang` (`kode_barang`);

--
-- Indeks untuk tabel `master_barang`
--
ALTER TABLE `master_barang`
  ADD PRIMARY KEY (`kode_barang`),
  ADD KEY `kode_kategori` (`kode_kategori`),
  ADD KEY `kode_satuan` (`kode_satuan`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_customer`
--
ALTER TABLE `master_customer`
  ADD PRIMARY KEY (`kode_customer`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_jenis_palet`
--
ALTER TABLE `master_jenis_palet`
  ADD PRIMARY KEY (`kode_palet`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_karyawan`
--
ALTER TABLE `master_karyawan`
  ADD PRIMARY KEY (`kode_karyawan`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_kategori_barang`
--
ALTER TABLE `master_kategori_barang`
  ADD PRIMARY KEY (`kode_kategori`),
  ADD KEY `kode_user` (`kode_user`),
  ADD KEY `kode_user_2` (`kode_user`);

--
-- Indeks untuk tabel `master_kualitas`
--
ALTER TABLE `master_kualitas`
  ADD PRIMARY KEY (`kode_kualitas`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_role`
--
ALTER TABLE `master_role`
  ADD PRIMARY KEY (`kode_role`);

--
-- Indeks untuk tabel `master_satuan`
--
ALTER TABLE `master_satuan`
  ADD PRIMARY KEY (`kode_satuan`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_supplier`
--
ALTER TABLE `master_supplier`
  ADD PRIMARY KEY (`kode_supplier`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD PRIMARY KEY (`kode_user`),
  ADD KEY `kode_role` (`kode_role`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `transaksi_pembelian`
--
ALTER TABLE `transaksi_pembelian`
  ADD PRIMARY KEY (`kode_pembelian`),
  ADD KEY `kode_supplier` (`kode_supplier`,`kode_user`),
  ADD KEY `kode_user` (`kode_user`);

--
-- Indeks untuk tabel `transaksi_pembelian_detail`
--
ALTER TABLE `transaksi_pembelian_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_barang` (`kode_barang`),
  ADD KEY `kode_pembelian` (`kode_pembelian`);

--
-- Indeks untuk tabel `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  ADD PRIMARY KEY (`kode_pesanan`),
  ADD KEY `kode_customer` (`kode_customer`);

--
-- Indeks untuk tabel `transaksi_penjualan_custom`
--
ALTER TABLE `transaksi_penjualan_custom`
  ADD PRIMARY KEY (`kode_pesanan`),
  ADD KEY `kode_palet` (`kode_palet`,`kode_kualitas`),
  ADD KEY `kode_kualitas` (`kode_kualitas`);

--
-- Indeks untuk tabel `transaksi_penjualan_detail`
--
ALTER TABLE `transaksi_penjualan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_barang` (`kode_barang`),
  ADD KEY `kode_pesanan` (`kode_pesanan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `transaksi_pembelian_detail`
--
ALTER TABLE `transaksi_pembelian_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `transaksi_penjualan_detail`
--
ALTER TABLE `transaksi_penjualan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `laporan_stok_barang`
--
ALTER TABLE `laporan_stok_barang`
  ADD CONSTRAINT `laporan_stok_barang_ibfk_1` FOREIGN KEY (`kode_barang`) REFERENCES `master_barang` (`kode_barang`);

--
-- Ketidakleluasaan untuk tabel `master_barang`
--
ALTER TABLE `master_barang`
  ADD CONSTRAINT `master_barang_ibfk_1` FOREIGN KEY (`kode_kategori`) REFERENCES `master_kategori_barang` (`kode_kategori`),
  ADD CONSTRAINT `master_barang_ibfk_2` FOREIGN KEY (`kode_satuan`) REFERENCES `master_satuan` (`kode_satuan`),
  ADD CONSTRAINT `master_barang_ibfk_3` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_customer`
--
ALTER TABLE `master_customer`
  ADD CONSTRAINT `master_customer_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_jenis_palet`
--
ALTER TABLE `master_jenis_palet`
  ADD CONSTRAINT `master_jenis_palet_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_karyawan`
--
ALTER TABLE `master_karyawan`
  ADD CONSTRAINT `master_karyawan_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_kategori_barang`
--
ALTER TABLE `master_kategori_barang`
  ADD CONSTRAINT `master_kategori_barang_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_kualitas`
--
ALTER TABLE `master_kualitas`
  ADD CONSTRAINT `master_kualitas_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_satuan`
--
ALTER TABLE `master_satuan`
  ADD CONSTRAINT `master_satuan_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_supplier`
--
ALTER TABLE `master_supplier`
  ADD CONSTRAINT `master_supplier_ibfk_1` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `master_user`
--
ALTER TABLE `master_user`
  ADD CONSTRAINT `master_user_ibfk_1` FOREIGN KEY (`kode_role`) REFERENCES `master_role` (`kode_role`);

--
-- Ketidakleluasaan untuk tabel `transaksi_pembelian`
--
ALTER TABLE `transaksi_pembelian`
  ADD CONSTRAINT `transaksi_pembelian_ibfk_1` FOREIGN KEY (`kode_supplier`) REFERENCES `master_supplier` (`kode_supplier`),
  ADD CONSTRAINT `transaksi_pembelian_ibfk_2` FOREIGN KEY (`kode_user`) REFERENCES `master_user` (`kode_user`);

--
-- Ketidakleluasaan untuk tabel `transaksi_pembelian_detail`
--
ALTER TABLE `transaksi_pembelian_detail`
  ADD CONSTRAINT `transaksi_pembelian_detail_ibfk_1` FOREIGN KEY (`kode_barang`) REFERENCES `master_barang` (`kode_barang`),
  ADD CONSTRAINT `transaksi_pembelian_detail_ibfk_2` FOREIGN KEY (`kode_pembelian`) REFERENCES `transaksi_pembelian` (`kode_pembelian`);

--
-- Ketidakleluasaan untuk tabel `transaksi_penjualan`
--
ALTER TABLE `transaksi_penjualan`
  ADD CONSTRAINT `transaksi_penjualan_ibfk_1` FOREIGN KEY (`kode_customer`) REFERENCES `master_customer` (`kode_customer`);

--
-- Ketidakleluasaan untuk tabel `transaksi_penjualan_custom`
--
ALTER TABLE `transaksi_penjualan_custom`
  ADD CONSTRAINT `transaksi_penjualan_custom_ibfk_1` FOREIGN KEY (`kode_palet`) REFERENCES `master_jenis_palet` (`kode_palet`),
  ADD CONSTRAINT `transaksi_penjualan_custom_ibfk_2` FOREIGN KEY (`kode_kualitas`) REFERENCES `master_kualitas` (`kode_kualitas`);

--
-- Ketidakleluasaan untuk tabel `transaksi_penjualan_detail`
--
ALTER TABLE `transaksi_penjualan_detail`
  ADD CONSTRAINT `transaksi_penjualan_detail_ibfk_1` FOREIGN KEY (`kode_barang`) REFERENCES `master_barang` (`kode_barang`),
  ADD CONSTRAINT `transaksi_penjualan_detail_ibfk_2` FOREIGN KEY (`kode_pesanan`) REFERENCES `transaksi_penjualan` (`kode_pesanan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
