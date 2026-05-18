{{-- ================================================
     FILE: resources/views/partials/footer.blade.php
     PREMIUM BLUE E-COMMERCE FOOTER
================================================ --}}

<footer class="footer-modern text-light pt-5 mt-5">
    <div class="container">

        <div class="row g-5">

            {{-- BRAND --}}
            <div class="col-lg-4 col-md-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="footer-logo me-2">
                        <i class="bi bi-bag-heart-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">WangiShop</h5>
                </div>

                <p class="text-white-50">
                    Wangi Shop adalah toko modern dengan pengalaman belanja cepat, aman, dan nyaman.
                    Produk berkualitas dengan sentuhan teknologi terbaru.
                </p>

                {{-- SOCIAL MEDIA --}}
                <div class="d-flex gap-3 mt-4">
                    <a href="https://www.facebook.com/" target="_blank" class="social-icon">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/wangi.project?igsh=bTF3ODdleXFkaWxk" target="_blank" class="social-icon">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="https://x.com/" target="_blank" class="social-icon">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                    <a href="https://www.youtube.com/" target="_blank" class="social-icon">
                        <i class="bi bi-youtube"></i>
                    </a>
                </div>
            </div>

            {{-- MENU --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Menu</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('catalog.index') }}">Katalog Produk</a></li>
                    <li><a href="{{ route('tentang') }}">Tentang Kami</a></li>
                </ul>
            </div>

            {{-- HELP --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="footer-title">Bantuan</h6>
                <ul class="footer-links">
                    <li><a href="{{ route('bantuan') }}#faq-content">FAQ</a></li>
                    <li><a href="{{ route('bantuan') }}#cart-content">Cara Belanja</a></li>
                    <li><a href="{{ route('bantuan') }}#privacy-content">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('bantuan') }}#terms-content">Laporan Pengaduan</a></li>
                </ul>
            </div>

            {{-- CONTACT --}}
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-title">Hubungi Kami</h6>

                <div class="footer-contact">
                    <p>
                        <a href="#" 
                        target="_blank" 
                        class="contact-phone">

                            <i class="bi bi-geo-alt phone-icon"></i>
                            Bandung, Indonesia
                        </a>
                    </p>
                    <p>
                        <a href="https://wa.me/6281382771810"
                        target="_blank"
                        class="contact-phone">

                            <i class="bi bi-telephone phone-icon"></i>
                            (+62) 81382771810
                        </a>
                    </p>
                    <p>
                        <a href="mailto:bginner1933@gmail.com" 
                        target="_blank"
                        class="contact-phone">

                            <i class="bi bi-envelope phone-icon"></i>
                            bginner1933@gmail.com
                        </a>
                    </p>
                </div>

                <div class="payment-icons mt-4">
                    <i class="bi bi-credit-card-2-front"></i>
                    <i class="bi bi-bank"></i>
                    <i class="bi bi-qr-code-scan"></i>
                    <i class="bi bi-wallet2"></i>
                    <i class="bi bi-shield-check"></i>
                </div>
            </div>

        </div>

        <hr class="footer-divider">

        <div class="text-center text-md-start">
            <small class="text-white-50">
                © {{ date('Y') }} WangiShop. All rights reserved.
            </small>
        </div>

    </div>
</footer>

<style>
.footer-modern {
    background: linear-gradient(135deg, #0b1220, #0d6efd);
    position: relative;
    overflow: hidden;
}

.footer-logo {
    width: 42px;
    height: 42px;
    background: rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 20px;
}

.footer-title {
    font-weight: 700;
    margin-bottom: 18px;
    color: #ffffff;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 10px;
}

.footer-links a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    transition: 0.3s;
}

.footer-links a:hover {
    color: #ffffff;
    transform: translateX(5px);
}

.social-icon {
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: rgba(255,255,255,0.08);
    color: white;
    transition: 0.3s;
}

.social-icon:hover {
    background: #ffffff;
    color: #0d6efd;
    transform: translateY(-3px);
}

.footer-contact p {
    color: rgba(255,255,255,0.6);
    margin-bottom: 10px;
}

.footer-contact i {
    margin-right: 8px;
    color: #ffffff;
}

.payment-icons {
    display: flex;
    gap: 15px;
    font-size: 22px;
    color: rgba(255,255,255,0.6);
}

.payment-icons i:hover {
    color: #ffffff;
    transform: scale(1.15);
    transition: 0.3s;
}

.footer-divider {
    border-color: rgba(255,255,255,0.15);
    margin: 30px 0;
}

.contact-phone{
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-weight: bold;
    font-size: 18px;
}

.contact-phone:hover{
    color: white;
}

.phone-icon{
    color: #60a5fa !important;
    margin-right: 6px;
}
</style>