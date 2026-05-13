@props(['product'])

<div class="card h-100 border-0 shadow-sm product-card">
    {{-- Gambar --}}
    <div class="position-relative overflow-hidden bg-light" style="padding-top: 100%;">
        <img src="{{ $product->image_url }}"
             class="card-img-top position-absolute top-0 start-0 w-100 h-100 object-fit-cover">

        @if($product->has_discount)
             <span class="position-absolute top-0 start-0 m-2 badge bg-danger">
                 -{{ $product->discount_percentage }}%
             </span>
        @endif
    </div>

    {{-- Info --}}
    <div class="card-body d-flex flex-column">
        <small class="text-muted mb-1">{{ $product->category?->name }}</small>
        <h6 class="card-title mb-2">
            <a href="{{ route('catalog.show', $product->slug) }}" class="text-decoration-none text-dark stretched-link">
                {{ $product->name }}
            </a>
        </h6>
        <div class="mt-auto">

            {{-- PRODUK BELI --}}
            @if($product->price)

                @if($product->has_discount)
                    <p class="fw-bold text-danger mb-0">
                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                    </p>

                    <small class="text-decoration-line-through text-muted">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </small>
                @else
                    <span class="fw-bold mb-0">
                        Starting From : <span class="text-danger">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    </span>
                @endif

            {{-- PRODUK SEWA --}}
            @elseif($product->rental_price)

                <p class="fw-bold text-info mb-0">
                    Rp {{ number_format($product->rental_price, 0, ',', '.') }}
                    / {{ $product->rental_unit }}
                </p>

            @endif

        </div>
    </div>
</div>