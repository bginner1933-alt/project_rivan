@extends('layouts.app')

@section('title', 'Pusat Bantuan')

@section('content')
<style>
    :root {
        --primary-blue: #3b82f6;
        --deep-blue: #1e40af;
        --glass-bg: rgba(255, 255, 255, 0.95);
    }

    body { 
        background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        min-height: 100vh;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .help-container {
        margin-top: 60px;
        margin-bottom: 60px;
    }

    .help-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .nav-pills .nav-link {
        color: #475569;
        font-weight: 600;
        border-radius: 12px;
        padding: 12px 20px;
        transition: all 0.3s ease;
    }

    .nav-pills .nav-link.active {
        background-color: var(--deep-blue);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(30, 64, 175, 0.3);
    }

    .accordion-button:not(.collapsed) {
        color: var(--deep-blue);
        background-color: #eff6ff;
        font-weight: bold;
    }

    .step-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        border-left: 5px solid var(--primary-blue);
        height: 100%;
    }
</style>

<div class="container help-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="help-card">
                
                {{-- Tombol Kembali ke Home --}}
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-4 py-2 shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i> Kembali ke Home
                    </a>
                </div>

                <div class="text-center mb-5">
                    <h1 class="fw-bold text-dark">Pusat Bantuan</h1>
                    <p class="text-muted">Temukan jawaban, panduan, dan informasi lengkap mengenai layanan kami di sini.</p>
                </div>

                {{-- NAVIGASI TAB --}}
                <ul class="nav nav-pills justify-content-center gap-3 mb-5" id="helpTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="faq-tab" data-bs-toggle="pill" data-bs-target="#faq-content" type="button" role="tab" aria-controls="faq-content" aria-selected="true">
                            <i class="bi bi-question-circle me-2"></i> FAQ
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="cart-tab" data-bs-toggle="pill" data-bs-target="#cart-content" type="button" role="tab" aria-controls="cart-content" aria-selected="false">
                            <i class="bi bi-cart-check me-2"></i> Cara Belanja & Sewa
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="privacy-tab" data-bs-toggle="pill" data-bs-target="#privacy-content" type="button" role="tab" aria-controls="privacy-content" aria-selected="false">
                            <i class="bi bi-shield-lock me-2"></i> Kebijakan Privasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="terms-tab" data-bs-toggle="pill" data-bs-target="#terms-content" type="button" role="tab" aria-controls="terms-content" aria-selected="false">
                            <i class="bi bi-flag me-2"></i> Laporan Pengaduan
                        </button>
                    </li>
                </ul>

                {{-- KONTEN TAB --}}
                <div class="tab-content" id="helpTabsContent">
                    
                    {{-- TAB FAQ --}}
                    <div class="tab-pane fade show active" id="faq-content" role="tabpanel" aria-labelledby="faq-tab">
                        <h3 class="fw-bold mb-4">Pertanyaan yang Sering Diajukan (FAQ)</h3>
                        
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-3 shadow-sm rounded-3">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        1. Bagaimana cara mengetahui produk bisa disewa atau dibeli?
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        Pada halaman detail produk, Anda akan melihat opsi <strong>Pilih Mode</strong> (Beli atau Sewa). Jika produk hanya bisa dibeli, opsi sewa tidak akan muncul. Anda juga dapat melihat rincian harga pada harga utama yang akan berubah otomatis sesuai mode yang dipilih.
                                    </div>
                                </div>
                            </div>
                            
                            <div class="accordion-item border-0 mb-3 shadow-sm rounded-3">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        2. Apa saja satuan durasi sewa yang tersedia?
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        Untuk saat ini, kami mendukung durasi penyewaan dalam satuan <strong>Jam</strong>, <strong>Hari</strong>, <strong>Minggu</strong>, dan <strong>Bulan</strong>. Anda dapat memasukkan durasi sesuai kebutuhan Anda saat memilih mode sewa.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3 shadow-sm rounded-3">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        3. Bagaimana cara melakukan pelacakan pesanan?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        Setelah pesanan dikonfirmasi dan diproses, Anda dapat melihat nomor resi dan status pengiriman secara <em>real-time</em> melalui halaman Profil -> Riwayat Pesanan.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item border-0 mb-3 shadow-sm rounded-3">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button collapsed rounded-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        4. Apa yang terjadi jika terjadi kerusakan pada barang sewaan?
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-secondary">
                                        Kerusakan yang disebabkan oleh kelalaian pemakaian akan ditanggung oleh penyewa sesuai dengan syarat dan ketentuan yang berlaku. Harap hubungi tim dukungan kami melalui kontak yang tersedia untuk bantuan lebih lanjut.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB CARA BELANJA --}}
                    <div class="tab-pane fade" id="cart-content" role="tabpanel" aria-labelledby="cart-tab">
                        <h3 class="fw-bold mb-4">Panduan Cara Belanja dan Sewa</h3>
                        
                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="step-card">
                                    <div class="fs-2 text-primary mb-2"><i class="bi bi-search"></i></div>
                                    <h5 class="fw-bold text-dark">1. Cari Barang</h5>
                                    <p class="text-muted small mb-0">Jelajahi berbagai pilihan produk melalui katalog kami dan pilih produk yang Anda butuhkan.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="step-card">
                                    <div class="fs-2 text-primary mb-2"><i class="bi bi-toggles"></i></div>
                                    <h5 class="fw-bold text-dark">2. Pilih Mode</h5>
                                    <p class="text-muted small mb-0">Pada halaman produk, pilih "Beli" untuk membeli atau "Sewa" jika produk tersebut tersedia untuk disewakan.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="step-card">
                                    <div class="fs-2 text-primary mb-2"><i class="bi bi-basket3"></i></div>
                                    <h5 class="fw-bold text-dark">3. Tambah Keranjang</h5>
                                    <p class="text-muted small mb-0">Tentukan jumlah atau durasi sewa, lalu masukkan ke keranjang belanja Anda melalui tombol aksi.</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="step-card">
                                    <div class="fs-2 text-primary mb-2"><i class="bi bi-credit-card"></i></div>
                                    <h5 class="fw-bold text-dark">4. Checkout</h5>
                                    <p class="text-muted small mb-0">Lakukan pembayaran dengan metode yang aman dan pesanan akan segera kami proses.</p>
                                </div>
                            </div>
                        </div>

                        <hr class="my-5">

                        <div class="alert alert-info border-0 shadow-sm rounded-4 p-4" role="alert">
                            <div class="d-flex">
                                <div class="me-3 fs-4 text-primary"><i class="bi bi-info-circle-fill"></i></div>
                                <div>
                                    <h5 class="alert-heading fw-bold mb-1">Catatan Pembayaran</h5>
                                    <p class="mb-0 text-secondary">Pastikan Anda melakukan transfer sebelum batas waktu berakhir untuk menghindari pembatalan otomatis sistem. Simpan bukti transfer Anda jika diperlukan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB KEBIJAKAN PRIVASI --}}
                    <div class="tab-pane fade" id="privacy-content" role="tabpanel" aria-labelledby="privacy-tab">
                        <h3 class="fw-bold mb-4">Kebijakan Privasi</h3>
                        
                        <div class="text-secondary">
                            <p>Kami sangat menghargai privasi dan keamanan data pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi yang Anda berikan saat menggunakan layanan dan situs web kami.</p>
                            
                            <hr class="my-3">

                            <h5 class="fw-bold text-dark mt-4">1. Informasi yang Kami Kumpulkan</h5>
                            <ul class="text-secondary ps-3">
                                <li><strong>Informasi Akun:</strong> Nama, alamat email, nomor telepon, dan kata sandi.</li>
                                <li><strong>Data Transaksi:</strong> Informasi pesanan, riwayat belanja, dan alamat pengiriman Anda.</li>
                                <li><strong>Data Teknis:</strong> Alamat IP, jenis browser, dan waktu akses melalui penggunaan <em>cookies</em>.</li>
                            </ul>

                            <h5 class="fw-bold text-dark mt-4">2. Penggunaan Informasi</h5>
                            <p>Informasi yang kami kumpulkan semata-mata digunakan untuk:</p>
                            <ul class="text-secondary ps-3">
                                <li>Memproses dan mengirimkan pesanan atau barang sewaan Anda.</li>
                                <li>Meningkatkan kualitas pelayanan pelanggan dan produk kami.</li>
                                <li>Mengirimkan notifikasi terkait status pesanan dan promosi yang relevan.</li>
                            </ul>

                            <h5 class="fw-bold text-dark mt-4">3. Keamanan Data</h5>
                            <p>Kami menerapkan prosedur keamanan standar industri untuk memastikan bahwa data pribadi Anda terlindungi dari akses, pengubahan, atau pengungkapan yang tidak sah.</p>

                            <h5 class="fw-bold text-dark mt-4">4. Hubungi Kami</h5>
                            <p>Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi tim dukungan kami di <a href="mailto:support@example.com" class="text-decoration-none">support@example.com</a>.</p>
                        </div>
                    </div>

                    {{-- TAB LAPORAN PENGADUAN --}}
                    <div class="tab-pane fade" id="terms-content" role="tabpanel" aria-labelledby="terms-tab">
                        <h3 class="fw-bold mb-4">Laporan Pengaduan</h3>
                        <p class="text-secondary mb-4">Apakah Anda mengalami kendala terkait pesanan, barang sewaan, atau pelayanan kami? Silakan lengkapi formulir di bawah ini agar tim kami dapat segera membantu Anda.</p>

                        {{-- Alert Notifikasi --}}
                        @if (session('success'))
                            <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4" role="alert">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('pengaduan.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-dark">Nama Lengkap</label>
                                    <input type="text" class="form-control p-3 rounded-3 shadow-sm border-0" id="name" name="name" placeholder="Nama Anda" value="{{ old('name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="order_id" class="form-label fw-semibold text-dark">Nomor Pesanan (Opsional)</label>
                                    <input type="text" class="form-control p-3 rounded-3 shadow-sm border-0" id="order_id" name="order_id" placeholder="Contoh: INV-987654" value="{{ old('order_id') }}">
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="category" class="form-label fw-semibold text-dark">Kategori Masalah</label>
                                    <select class="form-select p-3 rounded-3 shadow-sm border-0" id="category" name="category" required>
                                        <option value="" selected disabled>Pilih Kategori Masalah...</option>
                                        <option value="rusak" {{ old('category') == 'rusak' ? 'selected' : '' }}>Kerusakan Produk / Barang</option>
                                        <option value="pengiriman" {{ old('category') == 'pengiriman' ? 'selected' : '' }}>Kendala Pengiriman / Kurir</option>
                                        <option value="layanan" {{ old('category') == 'layanan' ? 'selected' : '' }}>Layanan / Staf Kami</option>
                                        <option value="lainnya" {{ old('category') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="message" class="form-label fw-semibold text-dark">Deskripsi Keluhan</label>
                                    <textarea class="form-control p-3 rounded-3 shadow-sm border-0" id="message" name="message" rows="4" placeholder="Jelaskan secara detail kendala yang Anda hadapi..." required>{{ old('message') }}</textarea>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="attachment" class="form-label fw-semibold text-dark">Unggah Lampiran (Foto/Bukti, Opsional)</label>
                                    <input type="file" class="form-control p-3 rounded-3 shadow-sm border-0" id="attachment" name="attachment">
                                    <div class="form-text text-muted small mt-1">Maksimal ukuran file: 2 MB (Format: JPG, PNG, atau PDF).</div>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary px-4 py-3 rounded-pill shadow-sm">
                                        <i class="bi bi-send me-2"></i> Kirim Pengaduan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Otomatis membuka tab berdasarkan URL hash (misalnya #terms-content)
        var hash = window.location.hash;
        if (hash) {
            var triggerEl = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (triggerEl) {
                var tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        }

        // Tangani klik pada tab biasa
        var triggerTabList = [].slice.call(document.querySelectorAll('#helpTabs button'));
        triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function(event) {
                event.preventDefault();
                tabTrigger.show();
                var target = triggerEl.getAttribute('data-bs-target');
                history.pushState(null, null, target);
            });
        });
    });
</script>

@endsection