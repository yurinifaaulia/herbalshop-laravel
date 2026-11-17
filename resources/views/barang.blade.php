@extends('layouts.app')
@section('judul', 'Barang')
@section('isi')
<h1>Data Barang</h1>

@if (session('ok'))
    <p class="pesan-ok">{{ session('ok') }}</p>
@endif
@if ($errors->any())
    <p class="pesan-err">Gagal: {{ $errors->first() }}</p>
@endif

<form method="post" class="crud"
      action="{{ $edit ? url('/barang/' . $edit->idbarang) : url('/barang') }}">
    @csrf
    @if ($edit) @method('PUT') @endif

    <h3>{{ $edit ? 'Ubah Barang' : 'Tambah Barang' }}</h3>

    <label>Jenis (1 huruf, contoh: O / S / M)</label>
    <input type="text" name="jenis" maxlength="1" required
           value="{{ old('jenis', $edit->jenis ?? '') }}">

    <label>Nama Barang</label>
    <input type="text" name="nama" maxlength="45" required
           value="{{ old('nama', $edit->nama ?? '') }}">

    <label>Satuan</label>
    <select name="idsatuan" required>
        @foreach ($satuan as $s)
            <option value="{{ $s->idsatuan }}"
                {{ old('idsatuan', $edit->idsatuan ?? '') == $s->idsatuan ? 'selected' : '' }}>
                {{ $s->nama_satuan }}
            </option>
        @endforeach
    </select>

    <label>Harga</label>
    <input type="number" name="harga" min="0" required
           value="{{ old('harga', $edit->harga ?? '') }}">

    <p>
        <button type="submit" class="btn">Simpan</button>
        @if ($edit) <a href="{{ url('/barang') }}" class="btn btn-abu">Batal</a> @endif
    </p>
</form>

<table>
    <tr>
        <th>ID</th><th>Nama Barang</th><th>Satuan</th><th>Harga</th><th>Status</th><th>Aksi</th>
    </tr>
    @foreach ($data as $row)
        <tr>
            <td>{{ $row->idbarang }}</td>
            <td>{{ $row->nama }}</td>
            <td>{{ $row->satuan }}</td>
            <td>Rp {{ number_format($row->harga, 0, ',', '.') }}</td>
            <td>
                <span class="badge {{ $row->status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                    {{ $row->status }}
                </span>
            </td>
            <td>
                <a href="{{ url('/barang?edit=' . $row->idbarang) }}" class="btn btn-kecil">Ubah</a>
                <form method="post" action="{{ url('/barang/' . $row->idbarang . '/status') }}"
                      style="display:inline"
                      onsubmit="return confirm('Ubah status barang ini?')">
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
