<div>
    <div class="container-fluid bg-light p-5 text-center">
        <h1 class="display-3">Selamat Datang di Bengkel Kami</h1>
        <p class="lead">Solusi servis terbaik untuk kendaraan Anda. Profesional, cepat, dan terpercaya.</p>
        <a href="#" class="btn btn-primary btn-lg">Booking Servis Sekarang</a>
    </div>

    <div class="container my-5">
        <div class="row text-center">
            <div class="col-md-4">
                <i class="fas fa-fw fa-tachometer-alt fa-3x text-primary mb-3"></i>
                <h4>Servis Cepat</h4>
                <p class="text-muted">Layanan servis cepat tanpa mengurangi kualitas pengerjaan.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-fw fa-users-cog fa-3x text-primary mb-3"></i>
                <h4>Mekanik Berpengalaman</h4>
                <p class="text-muted">Dikerjakan oleh mekanik profesional dan bersertifikat.</p>
            </div>
            <div class="col-md-4">
                <i class="fas fa-fw fa-box-open fa-3x text-primary mb-3"></i>
                <h4>Sparepart Original</h4>
                <p class="text-muted">Kami hanya menggunakan suku cadang asli dan berkualitas.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-white p-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2>Lihat Hasil Pekerjaan Kami</h2>
                    <p>Kami bangga dengan hasil pekerjaan kami. Lihat galeri untuk melihat kendaraan yang telah kami
                        tangani.</p>
                    <a href="{{ route('public.gallery') }}" class="btn btn-outline-primary">Lihat Galeri</a>
                </div>
                <div class="col-md-6">

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
