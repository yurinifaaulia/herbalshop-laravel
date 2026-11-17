<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaan = DB::table('v_pengadaan')->orderByDesc('idpengadaan')->get();

        $items = DB::table('detail_pengadaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->select('d.idpengadaan', 'b.nama', 'd.harga_satuan', 'd.jumlah', 'd.sub_total')
            ->get()
            ->groupBy('idpengadaan');

        // pengadaan yang sudah punya penerimaan tidak boleh dibatalkan
        $sudahDiterima = DB::table('penerimaan')->pluck('idpengadaan')->unique()->all();

        $vendor = DB::table('v_vendor')->where('status', 'Aktif')->get();
        $users  = DB::table('v_user')->get();
        $barang = DB::table('v_barang')->where('status', 'Aktif')->get();

        return view('pengadaan', compact('pengadaan', 'items', 'sudahDiterima', 'vendor', 'users', 'barang'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_idvendor'  => 'required|integer|exists:vendor,idvendor',
            'user_iduser'      => 'required|integer|exists:user,iduser',
            'item'             => 'required|array|min:1',
            'item.*.idbarang'  => 'required|integer|exists:barang,idbarang',
            'item.*.harga'     => 'required|integer|min:0',
            'item.*.jumlah'    => 'required|integer|min:1',
        ]);

        $ids = array_column($data['item'], 'idbarang');
        if (count($ids) !== count(array_unique($ids))) {
            return back()->withErrors(['item' => 'Satu barang tidak boleh muncul dua kali dalam satu pengadaan.']);
        }

        $statusVendor = DB::table('vendor')->where('idvendor', $data['vendor_idvendor'])->value('status');
        if ($statusVendor != '1') {
            return back()->withErrors(['vendor' => 'Vendor tidak aktif.']);
        }

        DB::transaction(function () use ($data) {
            $subtotal = 0;
            foreach ($data['item'] as $it) {
                $subtotal += $it['harga'] * $it['jumlah'];
            }
            $ppn = (int) DB::selectOne('SELECT f_hitung_ppn(?) AS ppn', [$subtotal])->ppn;

            $idpengadaan = DB::table('pengadaan')->insertGetId([
                'timestamp'       => now(),
                'user_iduser'     => $data['user_iduser'],
                'status'          => 'P',
                'vendor_idvendor' => $data['vendor_idvendor'],
                'subtotal_nilai'  => $subtotal,
                'ppn'             => $ppn,
                'total_nilai'     => $subtotal + $ppn,
            ]);

            DB::table('detail_pengadaan')->insert(array_map(fn ($it) => [
                'harga_satuan' => $it['harga'],
                'jumlah'       => $it['jumlah'],
                'sub_total'    => $it['harga'] * $it['jumlah'],
                'idbarang'     => $it['idbarang'],
                'idpengadaan'  => $idpengadaan,
            ], array_values($data['item'])));
        });

        return redirect('/pengadaan')->with('ok', 'Pengadaan disimpan dengan status Pending.');
    }

    public function destroy(int $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $status = DB::table('pengadaan')->where('idpengadaan', $id)->value('status');
                if ($status === 'T') {
                    throw new \RuntimeException('Pengadaan yang sudah tuntas tidak bisa dibatalkan.');
                }
                if (DB::table('penerimaan')->where('idpengadaan', $id)->exists()) {
                    throw new \RuntimeException('Pengadaan ini sudah punya penerimaan, tidak bisa dibatalkan.');
                }
                DB::table('detail_pengadaan')->where('idpengadaan', $id)->delete();
                DB::table('pengadaan')->where('idpengadaan', $id)->delete();
            });
        } catch (\Throwable $e) {
            return redirect('/pengadaan')->withErrors(['batal' => $e->getMessage()]);
        }

        return redirect('/pengadaan')->with('ok', 'Pengadaan dibatalkan.');
    }
}