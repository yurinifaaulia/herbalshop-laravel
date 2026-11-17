<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = DB::table('v_user')->orderBy('iduser')->get();
        $edit = $request->filled('edit')
            ? DB::table('user')->where('iduser', (int) $request->edit)->first()
            : null;
        $role = DB::table('role')->get();

        return view('user', compact('data', 'edit', 'role'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'max:45', Rule::unique('user', 'username')],
            'password' => 'required|min:4|max:50',
            'idrole'   => 'required|integer|exists:role,idrole',
        ]);
        $data['password'] = Hash::make($data['password']);

        DB::table('user')->insert($data);

        return redirect('/user')->with('ok', 'Data berhasil ditambah.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'username' => ['required', 'max:45', Rule::unique('user', 'username')->ignore($id, 'iduser')],
            'password' => 'nullable|min:4|max:50',
            'idrole'   => 'required|integer|exists:role,idrole',
        ]);

        // password kosong = tidak diubah
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        DB::table('user')->where('iduser', $id)->update($data);

        return redirect('/user')->with('ok', 'Data berhasil diubah.');
    }

    public function destroy(int $id)
    {
        try {
            DB::table('user')->where('iduser', $id)->delete();
        } catch (QueryException $e) {
            return redirect('/user')->withErrors([
                'hapus' => 'User tidak bisa dihapus karena sudah dipakai di data transaksi.',
            ]);
        }

        return redirect('/user')->with('ok', 'User berhasil dihapus.');
    }
}
