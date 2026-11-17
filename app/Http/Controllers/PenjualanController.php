<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenjualanController extends Controller
{
    public function index()
    {
        $barang = DB::table('v_barang')->where('status', 'Aktif')->get();
        $kasir  = DB::table('v_user')->where('role', 'Kasir')->get();

        $riwayat = DB::table('penjualan as p')
            ->join('detail_penjualan as d', 'd.penjualan_idpenjualan', '=', 'p.idpenjualan')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->join('user as u', 'u.iduser', '=', 'p.iduser')
            ->select('p.idpenjualan', 'p.created_at', 'u.username', 'b.nama',
                     'd.harga_satuan', 'd.jumlah', 'd.subtotal', 'p.ppn', 'p.total_nilai')
            ->orderByDesc('p.idpenjualan')
            ->get();

        return view('penjualan', compact('barang', 'kasir', 'riwayat'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idbarang' => 'required|integer|exists:barang,idbarang',
            'jumlah'   => 'required|integer|min:1',
            'iduser'   => 'required|integer|exists:user,iduser',
        ]);

        try {
            DB::transaction(function () use ($data) {
                // margin aktif terbaru (kolom wajib di tabel penjualan)
                $margin = DB::table('margin_penjualan')
                    ->where('status', 1)
                    ->orderByDesc('created_at')
                    ->orderByDesc('idmargin_penjualan')
                    ->first();
                if (!$margin) {
                    throw new \RuntimeException('Belum ada margin aktif. Aktifkan satu margin dulu di menu Margin.');
                }

                $harga = DB::table('barang')->where('idbarang', $data['idbarang'])->value('harga');

                $idpenjualan = DB::table('penjualan')->insertGetId([
                    'created_at'         => now(),
                    'subtotal_nilai'     => 0,
                    'ppn'                => 0,
                    'total_nilai'        => 0,
                    'iduser'             => $data['iduser'],
                    'idmargin_penjualan' => $margin->idmargin_penjualan,
                ]);

                // subtotal sengaja 0: diisi otomatis oleh TRIGGER
                DB::table('detail_penjualan')->insert([
                    'harga_satuan'          => $harga,
                    'jumlah'                => $data['jumlah'],
                    'subtotal'              => 0,
                    'penjualan_idpenjualan' => $idpenjualan,
                    'idbarang'              => $data['idbarang'],
                ]);

                // STORED PROCEDURE: jumlahkan subtotal, hitung PPN (FUNCTION), isi total
                DB::statement('CALL sp_hitung_total_penjualan(?)', [$idpenjualan]);
            });
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['penjualan' => $e->getMessage()]);
        }

        return redirect('/penjualan')->with('ok', 'Penjualan berhasil disimpan.');
    }
}
