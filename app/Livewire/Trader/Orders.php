<?php

namespace App\Livewire\Trader;

use Livewire\Component;
use App\Models\Order;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;

class Orders extends Component
{
    public $orders;
    public $selectedOrder;
    public $showOrderModal = false;

    public function mount()
    {
        $this->orders = Order::where('seller_id', auth()->id())
            ->orWhere('buyer_id', auth()->id())
            ->with(['buyer', 'seller', 'coin'])
            ->whereIn('status', ['queued', 'proof_uploaded'])
            ->latest()
            ->get();
    }

    public function openOrderModal($orderId)
    {
        $this->selectedOrder = Order::where('id', $orderId)
            ->where(function ($query) {
                $query->where('seller_id', auth()->id())
                    ->orWhere('buyer_id', auth()->id());
            })
            ->with(['buyer', 'seller', 'coin'])
            ->firstOrFail();
        $this->showOrderModal = true;

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->selectedOrder->id,
            'action' => 'viewed_order',
            'description' => 'Trader ' . auth()->id() . ' vio los detalles de la orden ' . $this->selectedOrder->id,
            'changes' => ['status' => $this->selectedOrder->status],
        ]);
    }

    public function confirmOrder()
    {
        if ($this->selectedOrder->status !== 'proof_uploaded') {
            session()->flash('error', 'La orden debe tener un comprobante subido para ser confirmada.');
            return;
        }

        $buyer = $this->selectedOrder->buyer;
        $seller = $this->selectedOrder->seller;
        $buyerBalance = $buyer->balance;
        $sellerBalance = $seller->balance;

        // Verificar saldo suficiente
        if ($this->selectedOrder->type === 'buy') {
            // Trader (vendedor) envía USDT
            if ($sellerBalance->usdt_balance < $this->selectedOrder->usdt_amount) {
                session()->flash('error', 'El vendedor no tiene suficiente saldo USDT.');
                return;
            }
        } else {
            // Usuario (vendedor) envía USDT
            if ($sellerBalance->usdt_balance < $this->selectedOrder->usdt_amount) {
                session()->flash('error', 'No tienes suficiente saldo USDT para completar esta transacción.');
                return;
            }
        }

        // Actualizar balances
        if ($this->selectedOrder->type === 'buy') {
            // Comprador recibe USDT, vendedor recibe moneda local
            $buyerBalance->usdt_balance += $this->selectedOrder->usdt_amount;
            $sellerBalance->local_balance += $this->selectedOrder->local_amount;
            $sellerBalance->usdt_balance -= $this->selectedOrder->usdt_amount;
        } else {
            // Vendedor envía USDT, recibe moneda local; comprador recibe USDT
            $sellerBalance->usdt_balance -= $this->selectedOrder->usdt_amount;
            $sellerBalance->local_balance += $this->selectedOrder->local_amount;
            $buyerBalance->usdt_balance += $this->selectedOrder->usdt_amount;
        }

        // Guardar balances
        $buyerBalance->save();
        $sellerBalance->save();

        // Actualizar estado de la orden
        $this->selectedOrder->status = 'completed';
        $this->selectedOrder->save();

        // Registrar en logs
        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->selectedOrder->id,
            'action' => 'completed',
            'description' => 'Trader ' . auth()->id() . ' completó la orden ' . $this->selectedOrder->id,
            'changes' => [
                'status' => ['old' => 'proof_uploaded', 'new' => 'completed'],
                'buyer_balance' => ['usdt' => $buyerBalance->usdt_balance, 'local' => $buyerBalance->local_balance],
                'seller_balance' => ['usdt' => $sellerBalance->usdt_balance, 'local' => $sellerBalance->local_balance],
            ],
        ]);

        $this->orders = $this->orders->filter(fn($order) => $order->id !== $this->selectedOrder->id);
        $this->showOrderModal = false;
        session()->flash('message', 'Orden completada exitosamente.');
    }

    public function reportError()
    {
        if ($this->selectedOrder->status !== 'proof_uploaded') {
            session()->flash('error', 'La orden debe tener un comprobante subido para reportar un error.');
            return;
        }

        $this->selectedOrder->status = 'disputed';
        $this->selectedOrder->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->selectedOrder->id,
            'action' => 'disputed',
            'description' => 'Trader ' . auth()->id() . ' reportó un error en la orden ' . $this->selectedOrder->id,
            'changes' => ['status' => ['old' => 'proof_uploaded', 'new' => 'disputed']],
        ]);

        $this->orders = $this->orders->filter(fn($order) => $order->id !== $this->selectedOrder->id);
        $this->showOrderModal = false;
        session()->flash('message', 'Error reportado. La orden está ahora en disputa.');
        $this->selectedOrder = null;
    }

    public function render()
    {
        return view('livewire.trader.orders');
    }
}