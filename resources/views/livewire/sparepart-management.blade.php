<div>
    {{-- 1. Header Halaman dan Tombol Tambah --}}
    <div class="row mb-3">
        <div class="col-md-6">
            <h3>Manajemen Spare Part</h3>
        </div>
        <div class="col-md-6 text-end">
            <button wire:click="create()" class="btn btn-primary">Tambah Spare Part Baru</button>
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
                placeholder="Cari nama atau SKU...">
        </div>
    </div>

    {{-- 3. Tabel Data Spare Part --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No.</th>
                    <th>Nama Spare Part</th>
                    <th>SKU</th>
                    <th>Stok</th>
                    <th>Harga Jual</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($spareparts as $index => $sparepart)
                    <tr>
                        <td>{{ $spareparts->firstItem() + $index }}</td>
                        <td>{{ $sparepart->name }}</td>
                        <td>{{ $sparepart->sku }}</td>
                        <td>{{ $sparepart->stock }}</td>
                        <td>Rp {{ number_format($sparepart->sale_price, 0, ',', '.') }}</td>
                        <td>
                            <button wire:click="edit({{ $sparepart->id }})" class="btn btn-sm btn-warning">Edit</button>
                            <button wire:click="delete({{ $sparepart->id }})"
                                wire:confirm="Anda yakin ingin menghapus spare part ini?"
                                class="btn btn-sm btn-danger">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data spare part.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Links --}}
    <div class="mt-3">
        {{ $spareparts->links() }}
    </div>


    {{-- 4. Modal Form (Create/Edit) --}}
    @if ($isModalOpen)
        <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $sparepart_id ? 'Edit Spare Part' : 'Tambah Spare Part' }}</h5>
                        <button type="button" wire:click="closeModal()" class="btn-close"></button>
                    </div>
                    <form wire:submit.prevent="store">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Spare Part</label>
                                <input type="text" wire:model="name" class="form-control" id="name">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU (Kode Barang)</label>
                                <input type="text" wire:model="sku" class="form-control" id="sku">
                                @error('sku')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="stock" class="form-label">Stok</label>
                                    <input type="number" wire:model="stock" class="form-control" id="stock">
                                    @error('stock')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="sale_price" class="form-label">Harga Jual</label>
                                    <input type="number" wire:model="sale_price" class="form-control" id="sale_price">
                                    @error('sale_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal()" class="btn btn-secondary">Batal</button>
                            <button type="submit"
                                class="btn btn-primary">{{ $sparepart_id ? 'Perbarui' : 'Simpan' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- Modal Backdrop --}}
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
