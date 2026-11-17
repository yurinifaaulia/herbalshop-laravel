@if (session('ok'))
    <p class="pesan-ok">{{ session('ok') }}</p>
@endif
@if ($errors->any())
    <p class="pesan-err">Gagal: {{ $errors->first() }}</p>
@endif
