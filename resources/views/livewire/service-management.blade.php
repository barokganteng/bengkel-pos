<div>
    {{-- 1. Header Halaman dan Tombol Tambah --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Manajemen Jasa Servis</h3>
        </div>
        <div class="col-md-6 text-end">
            <button wire:click="create()" class="btn btn-primary">Tambah Jasa Baru</button>
        </div>
    </div>

    {{-- Tampilkan Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- 2. Input Pencarian --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                placeholder="Cari nama jasa...">
        </div>
    </div>

    {{-- 3. Tabel Data Jasa Servis --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Nama Jasa</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($services as $index => $service)
                    <tr>
                        <td>{{ $services->firstItem() + $index }}</td>
                        <td>{{ $service->name }}</td>
                        <td>Rp {{ number_format($service->price, 0, ',', '.') }}</td>
                        <td>
                            <button wire:click="edit({{ $service->id }})" class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $service->id }})"
                                wire:confirm="Anda yakin ingin menghapus jasa ini?"
                                class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data jasa servis.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-3">
        {{ $services->links() }}
    </div>


    {{-- 4. Modal Form (Create/Edit) --}}
    @if ($isModalOpen)
        <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $service_id ? 'Edit Jasa Servis' : 'Tambah Jasa Servis' }}</h5>
                        <button type="button" wire:click="closeModal()" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Jasa</label>
                                <input type="text" wire:model="name" class="form-control" id="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="price" class="form-label">Harga</label>
                                <input type="number" wire:model="price" class="form-control" id="price">
                                @error('price')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal()" class="btn btn-secondary">Batal</button>
                            <button type="submit"
                                class="btn btn-primary">{{ $service_id ? 'Perbarui' : 'Simpan' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Modal Backdrop --}}
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
