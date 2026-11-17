@extends('layouts.app')
@section('judul', $judul)
@section('isi')
<h1>{{ $judul }}</h1>

<table>
    @if ($data->isNotEmpty())
        <tr>
            @foreach ($data->first() as $kolom => $isi)
                <th>{{ $kolom }}</th>
            @endforeach
        </tr>
        @foreach ($data as $baris)
            <tr>
                @foreach ($baris as $kolom => $isi)
                    <td>
                        @if ($kolom === 'status')
                            <span class="badge {{ $isi === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">{{ $isi }}</span>
                        @else
                            {{ $isi }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
    @else
        <tr><td>Belum ada data.</td></tr>
    @endif
</table>
@endsection
