{{-- <aside>
        <div class="sidebar">
            <h5 class="sidebar-title">Kategori</h5>
            <ul class="sidebar-links">
                @foreach ($categories as $category)
                    <li><a href="{{ route('catalog.index', ['category' => $category->slug]) }}">{{ $category->name }}</a></li>
                @endforeach
            </ul>
        </div>
</aside> --}}