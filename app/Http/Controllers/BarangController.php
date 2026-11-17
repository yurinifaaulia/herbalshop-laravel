<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('v_barang')->orderBy('idbarang')->get();

        $edit = $request->filled('edit')
            ? DB::table('barang')->where('idbarang', (int) $request->edit)->first()
            : null;

        $satuan = DB::table('satuan')
            ->where('status', 1)
            ->when($edit, fn ($q) => $q->orWhere('idsatuan', $edit->idsatuan))
            ->get();

        return view('barang', compact('data', 'edit', 'satuan'));
    }

    public function store(Request $request)
    {
        $data = $this->validasi($request);
        DB::table('barang')->insert($data + ['status' => 1]);

        return redirect('/barang')->with('ok', 'Data berhasil ditambah.');
    }

    public function update(Request $request, int $id)
    {
        $data = $this->validasi($request);
        DB::table('barang')->where('idbarang', $id)->update($data);

        return redirect('/barang')->with('ok', 'Data berhasil diubah.');
    }

    // pengganti hapus: aktif <-> nonaktif
    public function toggle(int $id)
    {
        DB::table('barang')->where('idbarang', $id)
            ->update(['status' => DB::raw('1 - status')]);

        return redirect('/barang')->with('ok', 'Status berhasil diubah.');
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'jenis'    => 'required|size:1',
            'nama'     => 'required|max:45',
            'idsatuan' => 'required|integer|exists:satuan,idsatuan',
            'harga'    => 'required|integer|min:0',
        ]);
        $data['jenis'] = strtoupper($data['jenis']);

        return $data;
    }
}
