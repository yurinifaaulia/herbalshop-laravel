@extends('layouts.app')
@section('judul', 'User')
@section('isi')
<h1>Data User</h1>
<x-pesan />

<form method="post" class="crud"
      action="{{ $edit ? url('/user/' . $edit->iduser) : url('/user') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <h3>{{ $edit ? 'Ubah User' : 'Tambah User' }}</h3>

    <label>Username</label>
    <input type="text" name="username" maxlength="45" required
           value="{{ old('username', $edit->username ?? '') }}">

    <label>Password {{ $edit ? '(kosongkan jika tidak diubah)' : '' }}</label>
    <input type="password" name="password" maxlength="50" {{ $edit ? '' : 'required' }}>

    <label>Role</label>
    <select name="idrole" required>
        @foreach ($role as $r)
            <option value="{{ $r->idrole }}"
                {{ old('idrole', $edit->idrole ?? '') == $r->idrole ? 'selected' : '' }}>
                {{ $r->nama_role }}
            </option>
        @endforeach
    </select>

    <p>
        <button type="submit" class="btn">Simpan</button>
        @if ($edit) <a href="{{ url('/user') }}" class="btn btn-abu">Batal</a> @endif
    </p>
</form>

<table>
    <tr><th>ID</th><th>Username</th><th>Role</th><th>Aksi</th></tr>
    @foreach ($data as $row)
        <tr>
            <td>{{ $row->iduser }}</td>
            <td>{{ $row->username }}</td>
            <td>{{ $row->role }}</td>
            <td>
                <a href="{{ url('/user?edit=' . $row->iduser) }}" class="btn btn-kecil">Ubah</a>
                <form method="post" action="{{ url('/user/' . $row->iduser) }}"
                      style="display:inline" onsubmit="return confirm('Hapus user ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-kecil btn-merah">Hapus</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
