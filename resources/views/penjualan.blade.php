@extends('layouts.app')
@section('judul', 'Penjualan')
@section('isi')
<h1>Penjualan</h1>
<x-pesan />

<form method="post" action="{{ url('/penjualan') }}" class="crud">
    @csrf
    <h3>Transaksi Baru</h3>

    <label>Barang</label>
    <select name="idbarang" required>
        @foreach ($barang as $b)
            <option value="{{ $b->idbarang }}" {{ old('idbarang') == $b->idbarang ? 'selected' : '' }}>
                {{ $b->nama }} (Rp {{ number_format($b->harga, 0, ',', '.') }})
            </option>
        @endforeach
    </select>

    <label>Jumlah</label>
    <input type="number" name="jumlah" min="1" required value="{{ old('jumlah') }}">

    <label>Kasir</label>
    <select name="iduser" required>
        @foreach ($kasir as $k)
            <option value="{{ $k->iduser }}" {{ old('iduser') == $k->iduser ? 'selected' : '' }}>
                {{ $k->username }}
            </option>
        @endforeach
    </select>

    <p><button type="submit" class="btn">Jual</button></p>
</form>

<h2>Riwayat Penjualan</h2>
<table>
    <tr>
        <th>ID</th><th>Waktu</th><th>Kasir</th><th>Barang</th><th>Harga</th>
        <th>Jumlah</th><th>Subtotal</th><th>PPN</th><th>Total</th>
    </tr>
    @foreach ($riwayat as $r)
        <tr>
            <td>{{ $r->idpenjualan }}</td>
            <td>{{ $r->created_at }}</td>
            <td>{{ $r->username }}</td>
            <td>{{ $r->nama }}</td>
            <td>Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
            <td>{{ $r->jumlah }}</td>
            <td>Rp {{ number_format($r->subtotal, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->ppn, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($r->total_nilai, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
@endsection
