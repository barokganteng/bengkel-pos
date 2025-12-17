<div>
    {{-- 1. Header Halaman --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Manajemen Galeri</h3>
        </div>
    </div>

    {{-- Tampilkan Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- 2. Form Upload --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Upload Gambar Baru</h6>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="store">
                <div class="mb-3">
                    <label for="image" class="form-label">File Gambar</label>
                    <input type="file" wire:model="image" class="form-control" id="image">

                    <div wire:loading wire:target="image" class="text-primary mt-1">Uploading...</div>

                    @if ($image && !$errors->has('image'))
                        <img src="{{ $image->temporaryUrl() }}" class="img-thumbnail mt-3" style="max-height: 200px;">
                    @endif

                    @error('image')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="caption" class="form-label">Caption (Opsional)</label>
                    <input type="text" wire:model="caption" class="form-control" id="caption">
                    @error('caption')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Upload</button>
            </form>
        </div>
    </div>

    {{-- 3. Daftar Gambar (Galeri) --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Gambar Ter-upload</h6>
        </div>
        <div class="card-body">
            @if ($galleries->isEmpty())
                <p class="text-center">Belum ada gambar di galeri.</p>
            @else
                <div class="row">
                    @foreach ($galleries as $gallery)
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <img src="{{ $gallery->image_path }}" class="card-img-top"
                                    style="height: 200px; object-fit: cover;" alt="{{ $gallery->caption }}">
                                <div class="card-body">
                                    <p class="card-text">{{ $gallery->caption ?? 'Tanpa caption' }}</p>
                                </div>
                                <div class="card-footer text-right">
                                    <button wire:click="delete({{ $gallery->id }})"
                                        wire:confirm="Anda yakin ingin menghapus gambar ini?"
                                        class="btn btn-sm btn-danger">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
