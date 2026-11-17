@extends('layouts.app')
@section('judul', 'Margin')
@section('isi')
<h1>Margin Penjualan</h1>
<x-pesan />

<form method="post" class="crud"
      action="{{ $edit ? url('/margin/' . $edit->idmargin_penjualan) : url('/margin') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <h3>{{ $edit ? 'Ubah Margin' : 'Tambah Margin' }}</h3>

    <label>Persen Margin (%)</label>
    <input type="number" name="persen" step="0.01" min="0" max="100" required
           value="{{ old('persen', $edit->persen ?? '') }}">

    <label>Dibuat Oleh</label>
    <select name="iduser" required>
        @foreach ($users as $u)
            <option value="{{ $u->iduser }}"
                {{ old('iduser', $edit->iduser ?? '') == $u->iduser ? 'selected' : '' }}>
                {{ $u->username }}
            </option>
        @endforeach
    </select>

    <p>
        <button type="submit" class="btn">Simpan</button>
        @if ($edit) <a href="{{ url('/margin') }}" class="btn btn-abu">Batal</a> @endif
    </p>
</form>

<table>
    <tr><th>ID</th><th>Persen</th><th>Status</th><th>Dibuat Oleh</th><th>Dibuat</th><th>Diubah</th><th>Aksi</th></tr>
    @foreach ($data as $row)
        <tr>
            <td>{{ $row->idmargin_penjualan }}</td>
            <td>{{ $row->persen }}%</td>
            <td><x-badge :status="$row->status" /></td>
            <td>{{ $row->dibuat_oleh }}</td>
            <td>{{ $row->created_at }}</td>
            <td>{{ $row->updated_at }}</td>
            <td>
                <a href="{{ url('/margin?edit=' . $row->idmargin_penjualan) }}" class="btn btn-kecil">Ubah</a>
                <form method="post" action="{{ url('/margin/' . $row->idmargin_penjualan . '/status') }}"
                      style="display:inline" onsubmit="return confirm('Ubah status margin ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-kecil {{ $row->status === 'Aktif' ? 'btn-merah' : '' }}">
                        {{ $row->status === 'Aktif' ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
@endsection
