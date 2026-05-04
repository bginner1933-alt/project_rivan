<div class="product-card">

    {{-- IMAGE --}}
    <div class="product-img">

        <a href="{{ route('catalog.show', $product->slug) }}">
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
        </a>

        {{-- DISCOUNT --}}
        @if($product->has_discount)
            <div class="badge-discount">
                -{{ $product->discount_percentage }}%
            </div>
        @endif

        {{-- WISHLIST --}}
        @auth
        <button onclick="toggleWishlist({{ $product->id }})"
            class="wishlist-btn wishlist-btn-{{ $product->id }}">
            <i class="bi {{ Auth::user()->hasInWishlist($product) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
        </button>
        @endauth

    </div>

    {{-- BODY --}}
    <div class="product-body">

        <small class="category">{{ $product->category->name }}</small>

        <a href="{{ route('catalog.show', $product->slug) }}" class="title">
            {{ Str::limit($product->name, 45) }}
        </a>

        <div class="price-box">
            @if($product->has_discount)
                <span class="old-price">{{ $product->formatted_original_price }}</span>
            @endif

            <span class="price">{{ $product->formatted_price }}</span>
        </div>

        @if($product->stock <= 5 && $product->stock > 0)
            <small class="stock low">
                Stok tinggal {{ $product->stock }}
            </small>
        @elseif($product->stock == 0)
            <small class="stock out">Stok Habis</small>
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="product-footer">
        <form action="{{ route('cart.add') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            <button class="btn-cart" @if($product->stock == 0) disabled @endif>
                <i class="bi bi-cart-plus"></i>
                {{ $product->stock == 0 ? 'Habis' : 'Tambah' }}
            </button>
        </form>
    </div>

</div>

{{-- STYLE PREMIUM --}}
<style>
:root{
    --blue:#2563eb;
    --blue-dark:#1e40af;
}

.product-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.06);
    transition:.3s;
}
.product-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 40px rgba(37,99,235,0.15);
}

/* IMAGE */
.product-img{
    position:relative;
    height:210px;
    overflow:hidden;
}
.product-img img{
    width:100%;
    height:100%;
    object-fit:cover;
    transition:.4s;
}
.product-card:hover img{
    transform:scale(1.05);
}

/* DISCOUNT */
.badge-discount{
    position:absolute;
    top:10px;
    left:10px;
    background:var(--blue);
    color:#fff;
    padding:5px 10px;
    font-size:12px;
    border-radius:50px;
    font-weight:600;
}

/* WISHLIST */
.wishlist-btn{
    position:absolute;
    top:10px;
    right:10px;
    width:36px;
    height:36px;
    border:none;
    border-radius:50%;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
    cursor:pointer;
    transition:.3s;
}
.wishlist-btn i{
    font-size:18px;
    color:#64748b;
}
.wishlist-btn:hover i{
    color:#ef4444;
}

/* BODY */
.product-body{
    padding:14px 16px;
}
.category{
    font-size:12px;
    color:#64748b;
}
.title{
    display:block;
    font-weight:600;
    color:#0f172a;
    text-decoration:none;
    margin:5px 0;
    transition:.3s;
}
.title:hover{
    color:var(--blue);
}

.price-box{
    margin-top:6px;
}
.old-price{
    font-size:12px;
    color:#94a3b8;
    text-decoration:line-through;
    margin-right:6px;
}
.price{
    font-weight:700;
    color:var(--blue);
}

/* STOCK */
.stock{
    display:block;
    margin-top:8px;
    font-size:12px;
}
.stock.low{ color:#f59e0b; }
.stock.out{ color:#ef4444; }

/* FOOTER */
.product-footer{
    padding:12px 16px 16px;
}
.btn-cart{
    width:100%;
    padding:10px;
    border:none;
    border-radius:12px;
    background:var(--blue);
    color:#fff;
    font-weight:600;
    transition:.3s;
}
.btn-cart:hover{
    background:var(--blue-dark);
}
.btn-cart:disabled{
    background:#cbd5e1;
    cursor:not-allowed;
}
</style>