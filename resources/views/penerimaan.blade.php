@extends('layouts.app')
@section('judul', 'Penerimaan')
@section('isi')
<h1>Penerimaan Barang</h1>
<x-pesan />

<h2>Menunggu Penerimaan</h2>
@forelse ($menunggu as $p)
    <div class="kartu">
        <div class="kartu-judul">
            <strong>Pengadaan #{{ $p->idpengadaan }} — {{ $p->nama_vendor }}</strong>
            <x-badge :status="$p->status" />
        </div>
        <p class="catatan">{{ $p->tanggal }} · dibuat oleh {{ $p->dibuat_oleh }}</p>

        <form method="post" action="{{ url('/penerimaan') }}">
            @csrf
            <input type="hidden" name="idpengadaan" value="{{ $p->idpengadaan }}">

            <table>
                <tr>
                    <th>Barang</th><th>Dipesan</th><th>Sudah Diterima</th><th>Sisa</th><th>Terima Sekarang</th>
                </tr>
                @foreach ($p->items as $it)
                    <tr>
                        <td>{{ $it->nama }}</td>
                        <td>{{ $it->jumlah }}</td>
                        <td>{{ $it->diterima }}</td>
                        <td>{{ $it->sisa }}</td>
                        <td>
                            @if ($it->sisa > 0)
                                <input type="number" name="terima[{{ $it->idbarang }}]"
                                       min="0" max="{{ $it->sisa }}" value="{{ $it->sisa }}" style="width:90px">
                            @else
                                Lengkap
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>

            @unless ($p->lengkap)
                <label>Penerima</label>
                <select name="iduser" required>
                    @foreach ($users as $u)
                        <option value="{{ $u->iduser }}">{{ $u->username }} ({{ $u->role }})</option>
                    @endforeach
                </select>
                <button type="submit" class="btn">Terima Barang</button>
            @endunless
        </form>

        @if ($p->lengkap)
            <form method="post" action="{{ url('/penerimaan/' . $p->idpengadaan . '/tuntas') }}"
                  onsubmit="return confirm('Tuntaskan penerimaan dan pengadaan ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn">Tuntaskan Penerimaan</button>
            </form>
        @else
            <p class="catatan">Bisa dituntaskan setelah semua barang diterima.</p>
        @endif
    </div>
@empty
    <p>Tidak ada pengadaan yang menunggu penerimaan.</p>
@endforelse

<h2>Riwayat Penerimaan</h2>
<table>
    <tr>
        <th>ID</th><th>Tanggal</th><th>Pengadaan</th><th>Vendor</th><th>Penerima</th><th>Barang Diterima</th><th>Status</th>
    </tr>
    @foreach ($riwayat as $r)
        <tr>
            <td>{{ $r->idpenerimaan }}</td>
            <td>{{ $r->tanggal }}</td>
            <td>#{{ $r->idpengadaan }}</td>
            <td>{{ $r->nama_vendor }}</td>
            <td>{{ $r->penerima }}</td>
            <td>
                @foreach ($detail[$r->idpenerimaan] ?? [] as $d)
                    {{ $d->nama }} × {{ $d->jumlah_terima }}<br>
                @endforeach
            </td>
            <td><x-badge :status="$r->status" /></td>
        </tr>
    @endforeach
</table>
@endsection
