<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Coin;
use App\Models\Order;
use App\Models\Log;

class Tradep2p extends Component
{
    public $traders;
    public $selectedTraderId;
    public $selectedTraderOffers = [];
    public $showOffersModal = false;
    public $showOrderModal = false;
    public $selectedCoinId;
    public $usdt_amount;
    public $type = 'buy'; // buy o sell
    public $min_amount;
    public $max_amount;
    public $buy_price;
    public $sell_price;
    public $local_currency;

    protected $rules = [
        'usdt_amount' => 'required|numeric|min:0',
        'type' => 'required|in:buy,sell',
    ];

    public function mount()
    {
        $this->traders = User::role('trader')->with('balance', 'coins')->get();
    }

    public function viewTraderOffers($traderId)
    {
        $trader = User::role('trader')->findOrFail($traderId);
        $this->selectedTraderId = $traderId;
        $this->selectedTraderOffers = Coin::where('user_id', $traderId)
            ->where('status', 'active')
            ->get();

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => User::class,
            'loggable_id' => $traderId,
            'action' => 'viewed_trader_profile',
            'description' => 'Usuario ' . auth()->id() . ' vio el perfil del trader ' . $traderId,
            'changes' => ['trader_name' => $trader->name],
        ]);

        $this->showOffersModal = true;
    }

    public function openOrderModal($coinId, $type)
    {
        $coin = Coin::where('id', $coinId)->where('status', 'active')->firstOrFail();
        $this->selectedCoinId = $coinId;
        $this->min_amount = $coin->min_amount;
        $this->max_amount = $coin->max_amount;
        $this->buy_price = $coin->buy_price;
        $this->sell_price = $coin->sell_price;
        $this->local_currency = $coin->local_currency;
        $this->usdt_amount = null;
        $this->type = $type;
        $this->showOrderModal = true;
    }

    public function startOrder()
    {
        $coin = Coin::findOrFail($this->selectedCoinId);
        $trader = User::findOrFail($this->selectedTraderId);

        // Validar monto
        $this->validate([
            'usdt_amount' => [
                'required',
                'numeric',
                'min:' . $this->min_amount,
                'max:' . $this->max_amount,
            ],
        ]);

        // Verificar saldo del vendedor
        $seller = $this->type === 'buy' ? $trader : auth()->user();
        if ($seller->balance->usdt_balance < $this->usdt_amount) {
            session()->flash('error', 'El ' . ($this->type === 'buy' ? 'trader' : 'usuario') . ' no tiene suficiente saldo USDT.');
            return;
        }

        // Calcular monto local
        $price = $this->type === 'buy' ? $coin->sell_price : $coin->buy_price;
        $local_amount = $this->usdt_amount * $price;

        // Crear orden con estado 'queued'
        $order = Order::create([
            'buyer_id' => $this->type === 'buy' ? auth()->id() : $trader->id,
            'seller_id' => $this->type === 'buy' ? $trader->id : auth()->id(),
            'coin_id' => $coin->id,
            'usdt_amount' => $this->usdt_amount,
            'local_amount' => $local_amount,
            'local_currency' => $coin->local_currency,
            'type' => $this->type,
            'status' => 'queued',
        ]);

        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Order::class,
            'loggable_id' => $order->id,
            'action' => 'created',
            'description' => 'Orden creada por el usuario ' . auth()->id() . ' con trader ' . $trader->id . ' y puesta en cola',
            'changes' => $order->toArray(),
        ]);

        $this->showOrderModal = false;
        $this->showOffersModal = false;

        $datetime = $order->created_at->format('YmdHis');
        return redirect()->route('payment.process', ['datetime' => $datetime])
            ->with('message', 'Orden puesta en cola exitosamente. Espera la confirmación del trader.');
    }

    public function render()
    {
        return view('livewire.tradep2p');
    }
}