<?php

namespace App\Livewire\Trader;

use Livewire\Component;
use App\Models\Balance;
use App\Models\Coin;
use App\Models\Order;
use App\Models\Log;
use Illuminate\Support\Facades\Storage;

class Dashboard extends Component
{
    public $showModalCreateOffer = false;
    public $showModalDeposit = false;
    public $showModalOrderDetails = false;
    public $buy_price;
    public $sell_price;
    public $local_currency = 'USD';
    public $min_amount;
    public $max_amount;
    public $deposit_amount;
    public $balance;
    public $coins;
    public $orders;
    public $selectedOrder;

    protected $rules = [
        'buy_price' => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
        'local_currency' => 'required|string|size:3',
        'min_amount' => 'required|numeric|min:10',
        'max_amount' => 'required|numeric|min:10|max:1000|gte:min_amount',
        'deposit_amount' => 'required|numeric|min:1',
    ];

    public function mount()
    {
        if (!auth()->user()->hasRole('trader')) {
            abort(403, 'No tienes permiso para acceder a este dashboard.');
        }

        $this->balance = auth()->user()->balance;
        if (!$this->balance) {
            $this->balance = Balance::create([
                'user_id' => auth()->id(),
                'usdt_balance' => 0,
                'local_balance' => 0,
                'local_currency' => 'USD',
            ]);

            Log::create([
                'user_id' => auth()->id(),
                'loggable_type' => Balance::class,
                'loggable_id' => $this->balance->id,
                'action' => 'created',
                'description' => 'Balance creado para el usuario ' . auth()->id(),
                'changes' => $this->balance->toArray(),
            ]);
        }

        $this->coins = Coin::where('user_id', auth()->id())->get();
        $this->orders = Order::where('buyer_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->get();
    }

    public function openModalCreateOffer()
    {
        $this->showModalCreateOffer = true;
    }

    public function openModalDeposit()
    {
        $this->showModalDeposit = true;
    }

    public function openModalOrderDetails($orderId)
    {
        $this->selectedOrder = Order::where('id', $orderId)
            ->where(function ($query) {
                $query->where('buyer_id', auth()->id())
                    ->orWhere('seller_id', auth()->id());
            })
            ->firstOrFail();
        $this->showModalOrderDetails = true;
    }

    public function createOffer()
    {
        if ($this->balance->usdt_balance < 10) {
            session()->flash('error', 'Necesitas un balance mínimo de 10 USDT para crear una oferta.');
            return;
        }

        $this->validate();

        if ($this->max_amount > $this->balance->usdt_balance) {
            session()->flash('error', 'El monto máximo de la oferta no puede exceder tu balance de USDT.');
            return;
        }

        $coin = Coin::create([
            'user_id' => auth()->id(),
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'local_currency' => $this->local_currency,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'status' => 'active',
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Coin::class,
            'loggable_id' => $coin->id,
            'action' => 'created',
            'description' => 'Oferta creada por el usuario ' . auth()->id(),
            'changes' => $coin->toArray(),
        ]);

        $this->showModalCreateOffer = false;
        $this->reset(['buy_price', 'sell_price', 'local_currency', 'min_amount', 'max_amount']);
        $this->coins = Coin::where('user_id', auth()->id())->get();

        session()->flash('message', '¡Oferta creada exitosamente!');
    }

    public function deposit()
    {
        $this->validate([
            'deposit_amount' => $this->rules['deposit_amount'],
        ]);

        $this->balance->usdt_balance += $this->deposit_amount;
        $this->balance->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Balance::class,
            'loggable_id' => $this->balance->id,
            'action' => 'deposit',
            'description' => 'Usuario ' . auth()->id() . ' depositó ' . $this->deposit_amount . ' USDT',
            'changes' => ['usdt_balance' => ['old' => $this->balance->usdt_balance - $this->deposit_amount, 'new' => $this->balance->usdt_balance]],
        ]);

        $this->showModalDeposit = false;
        $this->reset(['deposit_amount']);

        session()->flash('message', 'Depósito simulado exitosamente.');
    }

    public function toggleOfferStatus($coinId)
    {
        $coin = Coin::where('id', $coinId)->where('user_id', auth()->id())->firstOrFail();
        $oldStatus = $coin->status;
        $coin->status = $coin->status === 'active' ? 'inactive' : 'active';
        $coin->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Coin::class,
            'loggable_id' => $coin->id,
            'action' => 'updated',
            'description' => 'Estado de oferta cambiado de ' . $oldStatus . ' a ' . $coin->status,
            'changes' => ['status' => ['old' => $oldStatus, 'new' => $coin->status]],
        ]);

        $this->coins = Coin::where('user_id', auth()->id())->get();

        session()->flash('message', 'Estado de la oferta actualizado.');
    }

    public function completeTransaction()
    {
        if ($this->selectedOrder->status !== 'queued') {
            session()->flash('error', 'La orden no está en cola.');
            return;
        }

        // Actualizar balances
        $buyer = $this->selectedOrder->buyer;
        $seller = $this->selectedOrder->seller;
        $buyerBalance = $buyer->balance;
        $sellerBalance = $seller->balance;

        if ($this->selectedOrder->type === 'buy') {
            // Comprador recibe USDT, vendedor recibe moneda local
            $buyerBalance->usdt_balance += $this->selectedOrder->usdt_amount;
            $sellerBalance->local_balance += $this->selectedOrder->local_amount;
            $sellerBalance->usdt_balance -= $this->selectedOrder->usdt_amount;
        } else {
            // Vendedor recibe USDT, comprador recibe moneda local
            $sellerBalance->usdt_balance += $this->selectedOrder->usdt_amount;
            $buyerBalance->local_balance += $this->selectedOrder->local_amount;
            $buyerBalance->usdt_balance -= $this->selectedOrder->usdt_amount;
        }

        // Verificar que el vendedor tenga suficiente balance
        if ($sellerBalance->usdt_balance < 0) {
            session()->flash('error', 'El vendedor no tiene suficiente USDT para completar la transacción.');
            return;
        }

        $buyerBalance->save();
        $sellerBalance->save();

        // Actualizar estado de la orden
        $this->selectedOrder->status = 'completed';
        $this->selectedOrder->save();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $this->selectedOrder->id,
            'action' => 'completed',
            'description' => 'Trader ' . auth()->id() . ' completó la transacción de la orden ' . $this->selectedOrder->id,
            'changes' => ['status' => ['old' => 'queued', 'new' => 'completed']],
        ]);

        $this->showModalOrderDetails = false;
        $this->orders = Order::where('buyer_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->get();

        session()->flash('message', 'Transacción completada exitosamente.');
    }

    public function reportError()
    {
        if ($this->selectedOrder->status !== 'queued') {
            session()->flash('error', 'La orden no está en cola.');
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
            'changes' => ['status' => ['old' => 'queued', 'new' => 'disputed']],
        ]);

        $this->showModalOrderDetails = false;
        $this->orders = Order::where('buyer_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->get();

        session()->flash('message', 'Error reportado. La orden está ahora en disputa.');
    }

    public function render()
    {
        return view('livewire.trader.dashboard');
    }
}