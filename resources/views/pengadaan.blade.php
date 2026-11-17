@extends('layouts.app')
@section('judul', 'Pengadaan')
@section('isi')
<h1>Pengadaan</h1>
<x-pesan />

<form method="post" action="{{ url('/pengadaan') }}" class="crud">
    @csrf
    <h3>Pengadaan Baru</h3>

    <label>Vendor</label>
    <select name="vendor_idvendor" required>
        @foreach ($vendor as $v)
            <option value="{{ $v->idvendor }}">{{ $v->nama_vendor }}</option>
        @endforeach
    </select>

    <label>Dibuat Oleh</label>
    <select name="user_iduser" required>
        @foreach ($users as $u)
            <option value="{{ $u->iduser }}">{{ $u->username }} ({{ $u->role }})</option>
        @endforeach
    </select>

    <label>Item Barang</label>
    <table id="item">
        <thead>
            <tr><th>Barang</th><th>Harga Satuan</th><th>Jumlah</th><th></th></tr>
        </thead>
        <tbody></tbody>
    </table>
    <p><button type="button" id="tambah" class="btn btn-kecil btn-abu">+ Tambah Item</button></p>

    <p><button type="submit" class="btn">Simpan Pengadaan</button></p>
    <p class="catatan">Status awal: Pending. Harga terisi otomatis dari harga barang dan bisa diubah.</p>
</form>

<template id="baris">
    <tr>
        <td>
            <select name="item[__i__][idbarang]" class="pilih-barang" required>
                @foreach ($barang as $b)
                    <option value="{{ $b->idbarang }}" data-harga="{{ $b->harga }}">{{ $b->nama }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" name="item[__i__][harga]" class="harga" min="0" required style="width:130px"></td>
        <td><input type="number" name="item[__i__][jumlah]" min="1" required style="width:90px"></td>
        <td><button type="button" class="btn btn-kecil btn-merah hapus">Hapus</button></td>
    </tr>
</template>

<h2>Daftar Pengadaan</h2>
@forelse ($pengadaan as $p)
    <div class="kartu">
        <div class="kartu-judul">
            <strong>Pengadaan #{{ $p->idpengadaan }} — {{ $p->nama_vendor }}</strong>
            <x-badge :status="$p->status" />
        </div>
        <p class="catatan">{{ $p->tanggal }} · dibuat oleh {{ $p->dibuat_oleh }}</p>

        <table>
            <tr><th>Barang</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th></tr>
            @foreach ($items[$p->idpengadaan] ?? [] as $it)
                <tr>
                    <td>{{ $it->nama }}</td>
                    <td>Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</td>
                    <td>{{ $it->jumlah }}</td>
                    <td>Rp {{ number_format($it->sub_total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </table>

        <p>
            Subtotal Rp {{ number_format($p->subtotal_nilai, 0, ',', '.') }} ·
            PPN Rp {{ number_format($p->ppn, 0, ',', '.') }} ·
            <strong>Total Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</strong>
        </p>

        @if ($p->status === 'Pending' && !in_array($p->idpengadaan, $sudahDiterima))
            <form method="post" action="{{ url('/pengadaan/' . $p->idpengadaan) }}"
                  onsubmit="return confirm('Batalkan pengadaan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-kecil btn-merah">Batalkan Pengadaan</button>
            </form>
        @endif
    </div>
@empty
    <p>Belum ada pengadaan.</p>
@endforelse

<script>
    let n = 0;
    const tbody = document.querySelector('#item tbody');
    const tpl = document.getElementById('baris').innerHTML;

    function tambah() {
        tbody.insertAdjacentHTML('beforeend', tpl.replaceAll('__i__', n++));
        const tr = tbody.lastElementChild;
        const pilih = tr.querySelector('.pilih-barang');
        const harga = tr.querySelector('.harga');
        const isi = () => { harga.value = pilih.selectedOptions[0].dataset.harga; };
        pilih.addEventListener('change', isi);
        isi();
    }

    document.getElementById('tambah').addEventListener('click', tambah);
    tbody.addEventListener('click', (e) => {
        if (e.target.classList.contains('hapus') && tbody.rows.length > 1) {
            e.target.closest('tr').remove();
        }
    });
    tambah();
</script>
@endsection
