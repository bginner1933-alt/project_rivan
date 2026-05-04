@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Update Password</h2>
    <form action="{{ route('password.reset-password') }}" method="POST">
        @csrf
        <input type="password" name="password" placeholder="Password baru" required>
        <input type="password" name="password_confirmation" placeholder="Konfirmasi password" required>
        <button type="submit">Update</button>
    </form>
</div>
@endsection