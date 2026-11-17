@extends('layouts.app')
@section('judul', 'Vendor')
@section('isi')
<h1>Data Vendor</h1>
<x-pesan />

<form method="post" class="crud"
      action="{{ $edit ? url('/vendor/' . $edit->idvendor) : url('/vendor') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <h3>{{ $edit ? 'Ubah Vendor' : 'Tambah Vendor' }}</h3>

    <label>Nama Vendor</label>
    <input type="text" name="nama_vendor" maxlength="100" required
           value="{{ old('nama_vendor', $edit->nama_vendor ?? '') }}">

    <label>Badan Hukum</label>
    <select name="badan_hukum">
        <option value="1" {{ old('badan_hukum', $edit->badan_hukum ?? '1') == '1' ? 'selected' : '' }}>Ya</option>
        <option value="0" {{ old('badan_hukum', $edit->badan_hukum ?? '1') == '0' ? 'selected' : '' }}>Tidak</option>
    </select>

    <p>
        <button type="submit" class="btn">Simpan</button>
        @if ($edit) <a href="{{ url('/vendor') }}" class="btn btn-abu">Batal</a> @endif
    </p>
</form>

<table>
    <tr><th>ID</th><th>Nama Vendor</th><th>Badan Hukum</th><th>Status</th><th>Aksi</th></tr>
    @foreach ($data as $row)
        <tr>
            <td>{{ $row->idvendor }}</td>
            <td>{{ $row->nama_vendor }}</td>
            <td>{{ $row->badan_hukum == '1' ? 'Ya' : 'Tidak' }}</td>
            <td><x-badge :status="$row->status" /></td>
            <td>
                <a href="{{ url('/vendor?edit=' . $row->idvendor) }}" class="btn btn-kecil">Ubah</a>
                <form method="post" action="{{ url('/vendor/' . $row->idvendor . '/status') }}"
                      style="display:inline" onsubmit="return confirm('Ubah status vendor ini?')">
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
