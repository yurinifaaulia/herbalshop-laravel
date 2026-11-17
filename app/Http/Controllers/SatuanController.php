<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('v_satuan')->orderBy('idsatuan')->get();
        $edit = $request->filled('edit')
            ? DB::table('satuan')->where('idsatuan', (int) $request->edit)->first()
            : null;

        return view('satuan', compact('data', 'edit'));
    }

    public function store(Request $request)
    {
        DB::table('satuan')->insert($this->validasi($request) + ['status' => 1]);

        return redirect('/satuan')->with('ok', 'Data berhasil ditambah.');
    }

    public function update(Request $request, int $id)
    {
        DB::table('satuan')->where('idsatuan', $id)->update($this->validasi($request));

        return redirect('/satuan')->with('ok', 'Data berhasil diubah.');
    }

    public function toggle(int $id)
    {
        DB::table('satuan')->where('idsatuan', $id)
            ->update(['status' => DB::raw('1 - status')]);

        return redirect('/satuan')->with('ok', 'Status berhasil diubah.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_satuan' => 'required|max:45',
        ]);
    }
}
