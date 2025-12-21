<div>
    <style>
        .hero-section {
            /* Ganti URL ini dengan gambar Anda. Simpan di public/img/ */
            /* Anda bisa cari di Unsplash.com dengan kata kunci "motorcycle repair" */
            background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('/img/hero.jpg');
            /* Contoh gambar */
            background-size: cover;
            background-position: center;
            height: 60vh;
            /* 60% dari tinggi layar */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
        }
    </style>
    <div class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold">Selamat Datang di Bengkel Ku</h1>
            <p class="lead fs-4">Solusi servis terbaik untuk kendaraan Anda. Profesional, cepat, dan terpercaya.</p>
            <a href="{{ route('public.booking') }}" class="btn btn-primary btn-lg mt-3">Booking Servis Sekarang</a>
        </div>
    </div>

    <div class="container my-5">
        <div class="row text-center mb-4">
            <div class="col-12">
                <h2>Layanan Kami</h2>
                <p class="lead">Kami melayani berbagai kebutuhan servis kendaraan Anda.</p>
            </div>
        </div>
        <div class="row text-center">
            @forelse ($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <i class="fas fa-fw fa-wrench fa-3x text-primary mb-3"></i>
                            <h4 class="card-title">{{ $service->name }}</h4>
                            <p class="card-text text-muted">Mulai dari Rp {{ number_format($service->price) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">Layanan akan segera diperbarui...</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="container-fluid bg-white p-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <h2>Lihat Hasil Pekerjaan Kami</h2>
                    <p>Kami bangga dengan hasil pekerjaan kami. Lihat galeri untuk melihat kendaraan yang telah kami
                        tangani.</p>
                    <a href="{{ route('public.gallery') }}" class="btn btn-outline-primary">Lihat Semua Galeri
                        &rarr;</a>
                </div>
                <div class="col-md-6">
                    @if ($latestGalleries->isEmpty())
                        <p class="text-muted">Galeri akan segera diperbarui...</p>
                    @else
                        <div class="row">
                            @foreach ($latestGalleries as $gallery)
                                <div class="col-6 mb-3">
                                    <img src="{{ Storage::url($gallery->image_path) }}"
                                        class="img-fluid rounded shadow-sm"
                                        style="height: 150px; width: 100%; object-fit: cover;"
                                        alt="{{ $gallery->caption ?? 'Galeri Bengkel' }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container my-5">
        <div class="row">
            <div class="col-12 text-center mb-4">
                <h2>Temukan Kami di Sini</h2>
                <p class="lead">Kunjungi bengkel kami yang berlokasi strategis.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm border-0" style="height: 400px;">
                    <iframe src="{{ env('GMAPS_EMBED_URL') }}" width="100%" height="100%" style="border:0;"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
            <div class="col-md-4">
                <h4>Alamat Bengkel</h4>
                <p>
                    Jl. Urip Sumoharjo, Podosugih<br>
                    Kec. Pekalongan Bar., Kota Pekalongan<br>
                    Jawa Tengah 51111
                    <br><br>
                    <strong>Jam Buka:</strong><br>
                    Senin - Sabtu: 08:00 - 17:00
                </p>
                <a href="{{ env('GMAPS_SHARE_URL') }}" target="_blank" class="btn btn-primary btn-lg">
                    <i class="fas fa-fw fa-map-marker-alt"></i>
                    Buka di Google Maps
                </a>
            </div>
        </div>
    </div>

</div>
