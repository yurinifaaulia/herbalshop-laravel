<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'HNI Inventory')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<nav>
    <a href="{{ url('/') }}">Beranda</a>
    <a href="{{ url('/role') }}">Role</a>
    <a href="{{ url('/satuan') }}">Satuan</a>
    <a href="{{ url('/user') }}">User</a>
    <a href="{{ url('/barang') }}">Barang</a>
    <a href="{{ url('/vendor') }}">Vendor</a>
    <a href="{{ url('/margin') }}">Margin</a>
    <a href="{{ url('/pengadaan') }}">Pengadaan</a>
    <a href="{{ url('/kartu-stok') }}">Kartu Stok</a>
</nav>
<main>
    @yield('isi')
</main>
<footer>
    <p>HNI Inventory - Tugas Praktikum Basis Data</p>
</footer>
</body>
</html>
