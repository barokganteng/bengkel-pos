<div>
    {{-- 1. Header Halaman --}}
    <div class="row mb-3">
        <div class="col-md-12">
            <h3>Buat Transaksi Servis Baru</h3>
        </div>
    </div>

    {{-- Tampilkan Flash Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">1. Informasi Pelanggan</h6>
                </div>
                <div class="card-body">

                    @if ($selected_customer_id)
                        <div class="mb-3">
                            <label>Pelanggan</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="{{ $selected_customer_name }}"
                                    readonly>
                                <button wire:click="resetCustomer()" class="btn btn-outline-danger"
                                    type="button">Ganti</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="vehicle">Kendaraan</label>
                            <div class="input-group"> <select wire:model="selected_vehicle_id" class="form-control"
                                    id="vehicle" {{ count($vehicles) == 0 ? 'disabled' : '' }}>
                                    <option value="">-- Pilih Kendaraan --</option>
                                    @foreach ($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}">
                                            {{ $vehicle->license_plate }} ({{ $vehicle->brand }} {{ $vehicle->model }})
                                        </option>
                                    @endforeach
                                </select>
                                <button wire:click="openNewVehicleModal()" class="btn btn-outline-success"
                                    type="button">Baru +</button>
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label>Cari Pelanggan (Nama, Email, HP)</label>
                            <div class="input-group"> <input type="text"
                                    wire:model.live.debounce.300ms="customer_search" class="form-control"
                                    placeholder="Ketik min 2 huruf...">
                                <button wire:click="openNewCustomerModal()" class="btn btn-outline-success"
                                    type="button">Baru +</button>
                            </div>
                        </div>

                        @if (count($customers) > 0)
                            <ul class="list-group mt-2">
                                @foreach ($customers as $customer)
                                    <li class="list-group-item list-group-item-action" style="cursor: pointer;"
                                        wire:click="selectCustomer({{ $customer->id }})">
                                        <strong>{{ $customer->name }}</strong> ({{ $customer->email }})
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endif

                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">2. Tambah Item Servis</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Cari Jasa Servis atau Sparepart</label>
                        <input type="text" wire:model.live.debounce.300ms="item_search" class="form-control"
                            placeholder="Ketik nama item atau SKU...">
                    </div>

                    @if (count($item_results) > 0)
                        <ul class="list-group">
                            @foreach ($item_results as $item)
                                <li class="list-group-item list-group-item-action" style="cursor: pointer;"
                                    wire:click="addItemToCart('{{ $item['type'] }}', {{ $item['id'] }})">
                                    <strong>{{ $item['name'] }}</strong>
                                    <span class="float-right">
                                        Rp {{ number_format($item['price']) }}
                                        @if ($item['type'] == 'sparepart')
                                            | Stok: {{ $item['stock'] }}
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">3. Rincian Biaya</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($cart as $key => $item)
                                    <tr>
                                        <td>
                                            {{ $item['name'] }} <br>
                                            <small>@ Rp {{ number_format($item['price']) }}</small>
                                        </td>
                                        <td>
                                            {{ $item['quantity'] }}
                                        </td>
                                        <td>{{ number_format($item['price'] * $item['quantity']) }}</td>
                                        <td>
                                            <button wire:click="removeItemFromCart({{ $key }})"
                                                class="btn btn-sm btn-danger p-0 px-1">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Keranjang kosong</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <hr>

                    <h5>Total: <span class="float-right">Rp {{ number_format($total) }}</span></h5>

                    <hr>

                    <div class="form-group">
                        <label for="mechanic">Pilih Mekanik</label>
                        <select wire:model="selected_mechanic_id" class="form-control" id="mechanic">
                            <option value="">-- Pilih Mekanik --</option>
                            @foreach ($mechanics as $mechanic)
                                <option value="{{ $mechanic->id }}">{{ $mechanic->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea wire:model="notes" class="form-control" id="notes" rows="2"
                            placeholder="Cth: Garansi 1 minggu, Oli bawa sendiri..."></textarea>
                    </div>

                    <button class="btn btn-success btn-block mt-3" wire:click="saveTransaction()">
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
        @if ($isNewCustomerModalOpen)
            <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Pelanggan Baru</h5>
                            <button type="button" wire:click="$set('isNewCustomerModalOpen', false)"
                                class="btn-close"></button>
                        </div>
                        <form wire:submit.prevent="saveNewCustomer">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="new_name" class="form-label">Nama</label>
                                    <input type="text" wire:model="new_name" class="form-control" id="new_name">
                                    @error('new_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_email" class="form-label">Email</label>
                                    <input type="email" wire:model="new_email" class="form-control"
                                        id="new_email">
                                    @error('new_email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_phone" class="form-label">No. HP (WhatsApp)</label>
                                    <input type="text" wire:model="new_phone" class="form-control"
                                        id="new_phone">
                                    @error('new_phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password</label>
                                    <input type="password" wire:model="new_password" class="form-control"
                                        id="new_password">
                                    <small class="form-text text-muted">Pelanggan bisa menggunakan ini untuk login
                                        nanti.</small>
                                    @error('new_password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="$set('isNewCustomerModalOpen', false)"
                                    class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Pelanggan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        @endif

        @if ($isNewVehicleModalOpen)
            <div class="modal fade show" tabindex="-1" style="display: block;" aria-modal="true" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Kendaraan Baru</h5>
                            <button type="button" wire:click="$set('isNewVehicleModalOpen', false)"
                                class="btn-close"></button>
                        </div>
                        <form wire:submit.prevent="saveNewVehicle">
                            <div class="modal-body">
                                <p>Menambahkan kendaraan untuk: <strong>{{ $selected_customer_name }}</strong></p>
                                <hr>
                                <div class="mb-3">
                                    <label for="new_license_plate" class="form-label">Nomor Polisi</label>
                                    <input type="text" wire:model="new_license_plate" class="form-control"
                                        id="new_license_plate" placeholder="G 1234 AB">
                                    @error('new_license_plate')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_brand" class="form-label">Merk</label>
                                    <input type="text" wire:model="new_brand" class="form-control" id="new_brand"
                                        placeholder="Honda">
                                    @error('new_brand')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_model" class="form-label">Model</label>
                                    <input type="text" wire:model="new_model" class="form-control" id="new_model"
                                        placeholder="Vario 150">
                                    @error('new_model')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="new_year" class="form-label">Tahun (Opsional)</label>
                                    <input type="number" wire:model="new_year" class="form-control" id="new_year"
                                        placeholder="2020">
                                    @error('new_year')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" wire:click="$set('isNewVehicleModalOpen', false)"
                                    class="btn btn-secondary">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan Kendaraan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        @endif
    </div>
</div>
