@props(['status'])
<span class="badge {{ $status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">{{ $status }}</span>