<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('v_vendor')->orderBy('idvendor')->get();
        $edit = $request->filled('edit')
            ? DB::table('vendor')->where('idvendor', (int) $request->edit)->first()
            : null;

        return view('vendor', compact('data', 'edit'));
    }

    public function store(Request $request)
    {
        DB::table('vendor')->insert($this->validasi($request) + ['status' => '1']);

        return redirect('/vendor')->with('ok', 'Data berhasil ditambah.');
    }

    public function update(Request $request, int $id)
    {
        DB::table('vendor')->where('idvendor', $id)->update($this->validasi($request));

        return redirect('/vendor')->with('ok', 'Data berhasil diubah.');
    }

    public function toggle(int $id)
    {
        DB::table('vendor')->where('idvendor', $id)
            ->update(['status' => DB::raw("IF(status = '1', '0', '1')")]);

        return redirect('/vendor')->with('ok', 'Status berhasil diubah.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_vendor' => 'required|max:100',
            'badan_hukum' => 'required|in:0,1',
        ]);
    }
}
