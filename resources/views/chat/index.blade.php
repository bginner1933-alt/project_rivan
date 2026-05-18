@extends('layouts.app') <!-- Sesuaikan dengan nama layout utamamu (misal: layouts.app atau admin.layouts) -->

@section('content')
<div class="container py-4">
    <div class="card shadow-sm" style="height: 600px;">
        <div class="row g-0 h-100">
            
            <!-- KOLOM KIRI: DAFTAR KONTAK -->
            <div class="col-md-4 border-end h-100 flex-column d-flex">
                <div class="p-3 border-bottom bg-light">
                    <h5 class="mb-0 font-weight-bold text-secondary">Kontak Obrolan</h5>
                </div>
                <div class="list-group list-group-flush overflow-auto flex-grow-1" style="max-height: 530px;">
                    @foreach($users as $user)
                        <a href="{{ route('chat.show', $user->id) }}" 
                           class="list-group-item list-group-item-action p-3 border-0 border-bottom {{ isset($receiver) && $receiver->id == $user->id ? 'active bg-primary text-white' : '' }}">
                            <div class="d-flex align-items-center">
                                <!-- Avatar Singkat (Inisial Nama) -->
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3 {{ isset($receiver) && $receiver->id == $user->id ? 'bg-white text-primary' : '' }}" 
                                     style="width: 40px; height: 40px; font-weight: bold;">
                                     <img 
                                        src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                        alt="{{ $user->name }}" 
                                        class="rounded-circle"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $user->name }}</h6>
                                    <small class="{{ isset($receiver) && $receiver->id == $user->id ? 'text-white-50' : 'text-muted' }}">Klik untuk chat</small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- KOLOM KANAN: RUANG CHAT -->
            <div class="col-md-8 h-100 d-flex flex-column bg-light">
                @if($receiver)
                    <!-- Header Chat -->
                    <div class="p-3 bg-white border-bottom shadow-sm d-flex align-items-center">
                        {{-- <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                            <img 
                                src="{{ $receiver->avatar ? asset('storage/' . $receiver->avatar) : asset('images/default-avatar.png') }}" 
                                alt="{{ $receiver->name }}" 
                                class="rounded-circle"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div> --}}
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-weight: bold;">
                            {{ strtoupper(substr($receiver->name, 0, 1)) }}
                        </div>
                        <h6 class="mb-0 fw-bold text-dark">{{ $receiver->name }}</h6>
                    </div>

                    <!-- Isi Box Chat (Scrollable) -->
                    <div class="flex-grow-1 p-3 overflow-auto d-flex flex-column" style="max-height: 460px; background-color: #f8f9fa;">
                        <div class="mt-auto space-y-3"> <!-- Menjaga chat tetap rapat ke bawah -->
                            <div class="text-center mb-3">
                                Hari ini, {{ \Carbon\Carbon::now()->format('d M Y') }}
                            </div>
                            @foreach($chats as $chat)
                                <div class="d-flex mb-3 {{ $chat->sender_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
                                    <div class="card border-0 p-3 shadow-sm {{ $chat->sender_id == auth()->id() ? 'bg-primary text-white text-end' : 'bg-white text-dark' }}" 
                                         style="max-width: 70%; border-radius: 15px; {{ $chat->sender_id == auth()->id() ? 'border-top-right-radius: 0px;' : 'border-top-left-radius: 0px;' }}">
                                        <p class="mb-1 text-start" style="font-size: 0.95rem; line-height: 1.4;">{{ $chat->message }}</p>
                                        <small class="d-block text-end {{ $chat->sender_id == auth()->id() ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.75rem;">
                                            {{ $chat->created_at->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Form Input Kirim Chat -->
                    <div class="p-3 bg-white border-top">
                        <form action="{{ route('chat.send', $receiver->id) }}" method="POST">
                            @csrf
                            <div class="input-group">
                                <input type="text" name="message" class="form-control border-secondary-subtle py-2" placeholder="Tulis pesan Anda di sini..." required autocomplete="off">
                                <button class="btn btn-primary px-4" type="submit">
                                    <i class="bi bi-send-fill me-1"></i> Kirim
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <!-- Jika Belum Memilih Kontak -->
                    <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center text-muted">
                        <!-- Icon Chat Kosong -->
                        <div class="mb-3 text-secondary opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-chat-square-dots" viewBox="0 0 16 16">
                                <path d="M14 1a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1h-2.5a2 2 0 0 0-1.6 1.4L9 14.3a.5.5 0 0 1-.8 0l-1.9-2.9A2 2 0 0 0 4.7 10H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2.5a1 1 0 0 1 .8.4l1.9 2.9a1.5 1.5 0 0 0 2.4 0l1.9-2.9a1 1 0 0 1 .8-.4H14a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                                <path d="M5 6a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m4 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold">Belum Ada Obrolan Terpilih</h5>
                        <p class="text-center px-4 small">Silakan pilih salah satu kontak di sebelah kiri untuk memulai obrolan baru.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection