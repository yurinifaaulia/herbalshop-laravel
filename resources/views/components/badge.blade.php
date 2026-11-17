@props(['status'])
@php
    $kelas = match ($status) {
        'Aktif', 'Tuntas' => 'badge-aktif',
        'Pending'         => 'badge-pending',
        default           => 'badge-nonaktif',
    };
@endphp
<span class="badge {{ $kelas }}">{{ $status }}</span>
