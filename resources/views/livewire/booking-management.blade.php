<div>
    {{-- 1. Header Halaman --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Manajemen Booking Online</h3>
        </div>
    </div>

    {{-- Tampilkan Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 2. Baris Filter --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>Filter Status</label>
                    <select wire:model.live="filterStatus" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Filter Tanggal Booking</label>
                    <input type="date" wire:model.live="filterDate" class="form-control">
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Tabel Data Booking --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal Booking</th>
                            <th>Pelanggan</th>
                            <th>Kendaraan</th>
                            <th>Keterangan Servis</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bookings as $booking)
                            <tr>
                                <td>#{{ $booking->id }}</td>
                                <td>{{ $booking->booking_date ? \Carbon\Carbon::parse($booking->booking_date)->format('d M Y H:i') : '-' }}
                                </td>
                                <td>{{ $booking->customer->name ?? 'N/A' }}</td>
                                <td>{{ $booking->vehicle->license_plate ?? 'N/A' }}</td>
                                <td>{{ $booking->service_description ?? '-' }}</td>
                                <td>
                                    @if ($booking->status == 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($booking->status == 'confirmed')
                                        <span class="badge bg-success">Confirmed</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button"
                                            data-toggle="dropdown">
                                            Ubah Status
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="updateBookingStatus({{ $booking->id }}, 'confirmed')">Konfirmasi</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="updateBookingStatus({{ $booking->id }}, 'completed')">Selesaikan</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="updateBookingStatus({{ $booking->id }}, 'cancelled')">Batalkan</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="updateBookingStatus({{ $booking->id }}, 'pending')">Set
                                                Pending</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Belum ada data booking.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="mt-3">
                {{ $bookings->links() }}
            </div>
        </div>
    </div>
</div>
