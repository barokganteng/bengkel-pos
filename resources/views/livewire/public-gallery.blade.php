<div class="container">
    {{-- 1. Header Halaman --}}
    <div class="row mb-4">
        <div class="col-md-12 text-center">
            <h1 class="display-4">Galeri Kami</h1>
            <p class="lead">Lihat hasil pekerjaan dan aktivitas di bengkel kami.</p>
        </div>
    </div>

    {{-- 2. Grid Galeri --}}
    <div class="row">
        @forelse ($galleries as $gallery)
            <div class="col-md-4 col-sm-6 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <a href="{{ Storage::get($gallery->image_path) }}" data-toggle="lightbox">
                        <img src="{{ Storage::get($gallery->image_path) }}" class="card-img-top"
                            style="height: 250px; object-fit: cover;" alt="{{ $gallery->caption }}">
                    </a>
                    @if ($gallery->caption)
                        <div class="card-body text-center">
                            <p class="card-text">{{ $gallery->caption }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-center">Belum ada gambar di galeri.</p>
            </div>
        @endforelse
    </div>
</div>
