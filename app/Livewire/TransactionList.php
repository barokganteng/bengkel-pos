<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceHistory;
use App\Models\ServiceDetail;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

class TransactionList extends Component
{
    use WithPagination;

    // Properti untuk filter
    public $search = '';
    public $filterStatus = '';
    public $filterDate = '';

    // Properti untuk modal detail
    public $isModalOpen = false;
    public $selectedTransaction = null;
    public $details = [];

    #[Layout(
        'layouts.admin'
    )]
    public function render()
    {
        $query = ServiceHistory::query()
            // Eager Loading (PENTING untuk performa)
            ->with(['customer', 'vehicle', 'mechanic']);

        // Terapkan filter pencarian (Nama Pelanggan atau No. Polisi)
        if (!empty($this->search)) {
            $query->whereHas('customer', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhereHas('vehicle', function ($q) {
                $q->where('license_plate', 'like', '%' . $this->search . '%');
            });
        }

        // Terapkan filter status
        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        // Terapkan filter tanggal
        if (!empty($this->filterDate)) {
            $query->whereDate('service_date', $this->filterDate);
        }

        // Ambil data dengan urutan terbaru dan pagination
        $transactions = $query->latest('service_date')->paginate(10);

        return view('livewire.transaction-list', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Tampilkan modal detail transaksi
     */
    public function showDetails($id)
    {
        // Ambil data transaksi induk (termasuk relasi)
        $this->selectedTransaction = ServiceHistory::with(['customer', 'vehicle', 'mechanic'])->findOrFail($id);

        // Ambil data detail (polimorfik itemable)
        $this->details = ServiceDetail::with('itemable')
            ->where('service_history_id', $id)
            ->get();

        $this->isModalOpen = true;
    }

    /**
     * Mengubah status transaksi
     */
    public function updateTransactionStatus($id, $status)
    {
        // Validasi status
        $allowedStatus = ['pending', 'in_progress', 'done', 'paid'];
        if (!in_array($status, $allowedStatus)) {
            session()->flash('error', 'Status tidak valid.'); // Kirim pesan error
            return;
        }

        $transaction = ServiceHistory::find($id);
        if ($transaction) {
            $transaction->status = $status;
            $transaction->save();
            session()->flash('message', 'Status transaksi #' . $id . ' berhasil diubah menjadi ' . $status);
        }
    }

    /**
     * Tutup modal
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->selectedTransaction = null;
        $this->details = [];
    }
}
