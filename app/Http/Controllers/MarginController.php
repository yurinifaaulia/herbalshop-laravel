<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarginController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('v_margin_penjualan')->orderBy('idmargin_penjualan')->get();
        $edit = $request->filled('edit')
            ? DB::table('margin_penjualan')->where('idmargin_penjualan', (int) $request->edit)->first()
            : null;
        $users = DB::table('user')->orderBy('username')->get();

        return view('margin', compact('data', 'edit', 'users'));
    }

    public function store(Request $request)
    {
        DB::table('margin_penjualan')->insert($this->validasi($request) + [
            'status'     => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/margin')->with('ok', 'Data berhasil ditambah.');
    }

    public function update(Request $request, int $id)
    {
        DB::table('margin_penjualan')->where('idmargin_penjualan', $id)
            ->update($this->validasi($request) + ['updated_at' => now()]);

        return redirect('/margin')->with('ok', 'Data berhasil diubah.');
    }

    public function toggle(int $id)
    {
        DB::table('margin_penjualan')->where('idmargin_penjualan', $id)
            ->update(['status' => DB::raw('1 - status'), 'updated_at' => now()]);

        return redirect('/margin')->with('ok', 'Status berhasil diubah.');
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'persen' => 'required|numeric|min:0|max:100',
            'iduser' => 'required|integer|exists:user,iduser',
        ]);
    }
}
