<div>
    {{-- 1. Header Halaman --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Riwayat Transaksi Servis</h3>
        </div>
    </div>

    {{-- Tampilkan Flash Message (jika ada) --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- 2. Baris Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label>Cari (Pelanggan/No. Polisi)</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Ketik pencarian...">
                </div>
                <div class="col-md-4">
                    <label>Filter Status</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                        <option value="paid">Paid</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Filter Tanggal</label>
                    <input type="date" wire:model.live="filterDate" class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Tabel Data Transaksi --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Mekanik</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            <tr>
                                <td>#{{ $tx->id }}</td>
                                <td>{{ $tx->service_date ? \Carbon\Carbon::parse($tx->service_date)->format('d M Y H:i') : '-' }}
                                </td>
                                <td>{{ $tx->customer->name ?? 'N/A' }}</td>
                                <td>{{ $tx->vehicle->license_plate ?? 'N/A' }}</td>
                                <td>{{ $tx->mechanic->name ?? 'N/A' }}</td>
                                <td>Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td><span class="badge bg-info text-white">{{ $tx->status }}</span></td>
                                <td>
                                    <button wire:click="showDetails({{ $tx->id }})"
                                        class="btn btn-sm btn-info">Detail</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada data transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>


    {{-- 4. Modal Detail Transaksi --}}
    @if ($isModalOpen && $selectedTransaction)
        <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Transaksi #{{ $selectedTransaction->id }}</h5>
                        <button type="button" wire:click="closeModal()" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Info Kuitansi --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Pelanggan:</strong> {{ $selectedTransaction->customer->name ?? 'N/A' }}<br>
                                <strong>No. HP:</strong> {{ $selectedTransaction->customer->phone ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 text-md-right">
                                <strong>Tanggal:</strong>
                                {{ \Carbon\Carbon::parse($selectedTransaction->service_date)->format('d M Y H:i') }}<br>
                                <strong>Status:</strong> <span
                                    class="badge bg-info text-white">{{ $selectedTransaction->status }}</span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Kendaraan:</strong> {{ $selectedTransaction->vehicle->license_plate ?? 'N/A' }}
                                ({{ $selectedTransaction->vehicle->brand ?? '' }}
                                {{ $selectedTransaction->vehicle->model ?? '' }})
                            </div>
                            <div class="col-md-6 text-md-right">
                                <strong>Mekanik:</strong> {{ $selectedTransaction->mechanic->name ?? 'N/A' }}
                            </div>
                        </div>

                        {{-- Tabel Rincian Item --}}
                        <table class="table table-sm table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Item</th>
                                    <th>Kategori</th>
                                    <th>Qty</th>
                                    <th>Harga Satuan</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $detail)
                                    <tr>
                                        <td>{{ $detail->itemable->name ?? 'Item Dihapus' }}</td>
                                        <td>{{ $detail->itemable_type == 'App\Models\Service' ? 'Jasa' : 'Sparepart' }}
                                        </td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>Rp {{ number_format($detail->price_at_transaction, 0, ',', '.') }}</td>
                                        <td>Rp
                                            {{ number_format($detail->price_at_transaction * $detail->quantity, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td colspan="4" class="text-right">GRAND TOTAL</td>
                                    <td>Rp {{ number_format($selectedTransaction->total_price, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal()" class="btn btn-secondary">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- Modal Backdrop --}}
        <div class="modal-backdrop fade show"></div>
    @endif
</div>
