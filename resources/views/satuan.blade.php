@extends('layouts.app')
@section('judul', 'Satuan')
@section('isi')
<h1>Data Satuan</h1>
<x-pesan />

<form method="post" class="crud"
      action="{{ $edit ? url('/satuan/' . $edit->idsatuan) : url('/satuan') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <h3>{{ $edit ? 'Ubah Satuan' : 'Tambah Satuan' }}</h3>

    <label>Nama Satuan</label>
    <input type="text" name="nama_satuan" maxlength="45" required
           value="{{ old('nama_satuan', $edit->nama_satuan ?? '') }}">

    <p>
        <button type="submit" class="btn">Simpan</button>
        @if ($edit) <a href="{{ url('/satuan') }}" class="btn btn-abu">Batal</a> @endif
    </p>
</form>

<table>
    <tr><th>ID</th><th>Nama Satuan</th><th>Status</th><th>Aksi</th></tr>
    @foreach ($data as $row)
        <tr>
            <td>{{ $row->idsatuan }}</td>
            <td>{{ $row->nama_satuan }}</td>
            <td><x-badge :status="$row->status" /></td>
            <td>
                <a href="{{ url('/satuan?edit=' . $row->idsatuan) }}" class="btn btn-kecil">Ubah</a>
                <form method="post" action="{{ url('/satuan/' . $row->idsatuan . '/status') }}"
                      style="display:inline" onsubmit="return confirm('Ubah status satuan ini?')">
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
