<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogAktivitasHelper
{
    public static function simpan($idReference, $referenceTable, $action, $rawOriginal = null, $rawChanges = null, $rawNew = null)
    {
        $user = Auth::user();

        DB::table('log_aktivitas')->insert([
            'id_reference'    => $idReference,
            'reference_table' => $referenceTable,
            'action'          => $action,
            'raw_original'    => $rawOriginal ? json_encode($rawOriginal, JSON_UNESCAPED_UNICODE) : null,
            'raw_changes'     => $rawChanges ? json_encode($rawChanges, JSON_UNESCAPED_UNICODE) : null,
            'raw_new'         => $rawNew ? json_encode($rawNew, JSON_UNESCAPED_UNICODE) : null,
            'kode_user'       => $user->kode_user ?? null,
            'nama_user'       => $user->nama_user ?? null,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'created_at'      => now(),
        ]);
    }
}