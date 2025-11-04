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
                            <select wire:model="selected_vehicle_id" class="form-control" id="vehicle">
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->license_plate }} ({{ $vehicle->brand }} {{ $vehicle->model }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-group">
                            <label>Cari Pelanggan (Nama, Email, HP)</label>
                            <input type="text" wire:model.live.debounce.300ms="customer_search" class="form-control"
                                placeholder="Ketik min 2 huruf...">
                        </div>

                        @if (count($customers) > 0)
                            <ul class="list-group">
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

                    <button class="btn btn-success btn-block mt-3" wire:click="saveTransaction()">
                        Simpan Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
