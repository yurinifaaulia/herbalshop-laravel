<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class TabelViewController extends Controller
{
    private array $halaman = [
        'role'       => ['Data Role', 'v_role'],
        'satuan'     => ['Data Satuan', 'v_satuan'],
        'user'       => ['Data User', 'v_user'],
        'vendor'     => ['Data Vendor', 'v_vendor'],
        'margin'     => ['Margin Penjualan', 'v_margin_penjualan'],
        'rekap-vendor' => ['Rekap Pengadaan per Vendor', 'v_total_pengadaan_vendor'],
        'kartu-stok' => ['Kartu Stok', 'v_kartu_stok'],
    ];

    public function show(string $nama)
    {
        abort_unless(isset($this->halaman[$nama]), 404);

        [$judul, $view] = $this->halaman[$nama];
        $data = DB::table($view)->get();

        return view('tabel', compact('judul', 'data'));
    }
}
