<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotifikasiController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifikasi = $this->queryNotifikasiUser($user)
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        $jumlahBelumDibaca = $this->queryNotifikasiUser($user)
            ->whereNull('dibaca_at')
            ->count();

        return response()->json([
            'success' => true,
            'jumlah_belum_dibaca' => $jumlahBelumDibaca,
            'data' => $notifikasi,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();

        $notifikasi = $this->queryNotifikasiUser($user)
            ->where('id', $id)
            ->first();

        if (!$notifikasi) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.'
            ], 404);
        }

        DB::table('notifikasi')
            ->where('id', $id)
            ->update([
                'dibaca_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi berhasil ditandai sebagai dibaca.',
            'url' => $notifikasi->url,
        ]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

        $ids = $this->queryNotifikasiUser($user)
            ->whereNull('dibaca_at')
            ->pluck('id');

        if ($ids->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Tidak ada notifikasi baru.',
            ]);
        }

        DB::table('notifikasi')
            ->whereIn('id', $ids)
            ->update([
                'dibaca_at' => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi berhasil ditandai sebagai dibaca.',
        ]);
    }

    private function queryNotifikasiUser($user)
    {
        return DB::table('notifikasi')
            ->where(function ($query) use ($user) {
                $query->where('kode_user', $user->kode_user)
                    ->orWhere('target_role', $user->kode_role)
                    ->orWhere('target_role', 'ALL');
            });
    }

    public static function buat($data)
    {
        return DB::table('notifikasi')->insert([
            'kode_user' => $data['kode_user'] ?? null,
            'target_role' => $data['target_role'] ?? null,
            'tipe' => $data['tipe'] ?? 'umum',
            'judul' => $data['judul'],
            'pesan' => $data['pesan'],
            'url' => $data['url'] ?? null,
            'ref_table' => $data['ref_table'] ?? null,
            'ref_kode' => $data['ref_kode'] ?? null,
            'dibaca_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}