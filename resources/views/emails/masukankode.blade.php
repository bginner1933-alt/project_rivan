@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Masukkan Kode</h2>
    <form action="{{ route('password.enter-code') }}" method="POST">
        @csrf
        <input type="text" name="code" placeholder="Masukkan kode dari email" required>
        <button type="submit">Verifikasi</button>
    </form>
</div>
@endsection