<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Order;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class PaymentProcess extends Component
{
    use WithFileUploads;

    public $datetime;
    public $order;
    public $voucher;
    public $showStatusModal = false;

    protected $rules = [
        'voucher' => 'required|image|mimes:jpg,jpeg,png|max:5120', // 5MB max
    ];

    public function mount($datetime)
    {
        $this->datetime = $datetime;
        $parsedDate = Carbon::createFromFormat('YmdHis', $datetime);
        $this->order = Order::where('created_at', $parsedDate)
            ->where(function ($query) {
                $query->where('buyer_id', auth()->id())
                    ->orWhere('seller_id', auth()->id());
            })
            ->with(['buyer', 'seller', 'coin'])
            ->firstOrFail();
    }

    public function confirmTransaction()
    {
        if ($this->order->status !== 'queued') {
            session()->flash('error', 'La transacción no está en estado en cola.');
            return;
        }

        $this->order->status = 'confirmed';
        $this->order->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->order->id,
            'action' => 'confirmed_transaction',
            'description' => 'Usuario ' . auth()->id() . ' confirmó ' . ($this->order->buyer_id === auth()->id() ? 'el envío del pago' : 'la recepción del pago') . ' para la orden ' . $this->order->id,
            'changes' => ['status' => ['old' => 'queued', 'new' => 'confirmed']],
        ]);

        session()->flash('message', 'Pago confirmado exitosamente.');
    }

    public function uploadProof()
    {
        if ($this->order->status !== 'confirmed') {
            session()->flash('error', 'Debes confirmar el pago antes de subir el comprobante.');
            return;
        }
        if ($this->order->buyer_id !== auth()->id()) {
            session()->flash('error', 'Solo el comprador puede subir el comprobante.');
            return;
        }

        $this->validate();

        $filename = 'voucher_' . $this->order->id . '_' . time() . '.' . $this->voucher->getClientOriginalExtension();
        $this->voucher->storeAs('vouchers', $filename, 'public');

        $this->order->voucher_path = 'vouchers/' . $filename;
        $this->order->status = 'proof_uploaded';
        $this->order->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->order->id,
            'action' => 'proof_uploaded',
            'description' => 'Usuario ' . auth()->id() . ' subió un comprobante para la orden ' . $this->order->id,
            'changes' => ['voucher_path' => $this->order->voucher_path, 'status' => ['old' => 'confirmed', 'new' => 'proof_uploaded']],
        ]);

        session()->flash('message', 'Comprobante subido correctamente. Espera la revisión del trader.');
    }

    public function openStatusModal()
    {
        $this->showStatusModal = true;
    }

    public function render()
    {
        return view('livewire.payment-process');
    }
}