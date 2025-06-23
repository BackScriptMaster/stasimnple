<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Coin;
use App\Models\Balance;
use App\Models\Log;
use App\Models\Order;
use Illuminate\Support\Facades\Log as LaravelLog;

class Dashboard extends Component
{
    public $showModalRegisterTrader = false;
    public $showModalDeposit = false;
    public $buy_price;
    public $sell_price;
    public $local_currency = 'BOB'; // Moneda local por defecto
    public $min_amount;
    public $max_amount;
    public $deposit_amount;
    public $balance;
    public $orders;
    public $userRole;

    public function rules()
    {
        return [
            'buy_price' => 'required|numeric|min:0.01',
            'sell_price' => 'required|numeric|min:0.01',
            'min_amount' => 'required|numeric|min:10',
            'max_amount' => [
                'required',
                'numeric',
                'min:10',
                'gte:min_amount',
            ],
            'deposit_amount' => 'required|numeric|min:1',
        ];
    }

    public function mount()
    {
        // Cargar balance
        $this->balance = auth()->user()->balance ?? Balance::create([
            'user_id' => auth()->id(),
            'usdt_balance' => 0,
            'local_balance' => 0,
            'local_currency' => 'BOB',
        ]);

        if ($this->balance->wasRecentlyCreated) {
            Log::create([
                'user_id' => auth()->id(),
                'loggable_type' => Balance::class,
                'loggable_id' => $this->balance->id,
                'action' => 'created',
                'description' => 'Balance creado para el usuario ' . auth()->id(),
                'changes' => $this->balance->toArray(),
            ]);
        }

        // Determinar rol
        $roles = auth()->user()->getRoleNames();
        $this->userRole = $roles->contains('admin') ? 'admin' : ($roles->contains('trader') ? 'trader' : 'user');

        // Cargar órdenes
        $this->orders = Order::where('buyer_id', auth()->id())
            ->orWhere('seller_id', auth()->id())
            ->with(['buyer', 'seller', 'coin'])
            ->latest()
            ->take(10)
            ->get();
    }

    public function openModalRegisterTrader()
    {
        $this->reset(['buy_price', 'sell_price', 'min_amount', 'max_amount']);
        $this->local_currency = 'BOB'; // Asegurar moneda por defecto
        $this->showModalRegisterTrader = true;
    }

    public function openModalDeposit()
    {
        $this->reset(['deposit_amount']);
        $this->showModalDeposit = true;
    }

    public function closeModal()
    {
        $this->showModalRegisterTrader = false;
        $this->showModalDeposit = false;
        $this->reset(['buy_price', 'sell_price', 'min_amount', 'max_amount', 'deposit_amount']);
    }

    public function registerAsTrader()
    {
        // Validar balance mínimo
        if ($this->balance->usdt_balance < 10) {
            session()->flash('error', 'Necesitas al menos 10 USDT para registrarte como trader.');
            $this->showModalRegisterTrader = false;
            return;
        }

        // Validar montos según balance actual
        if ($this->min_amount < 10) {
            session()->flash('error', 'El monto mínimo debe ser al menos 10 USDT.');
            return;
        }
        if ($this->max_amount < $this->min_amount) {
            session()->flash('error', 'El monto máximo debe ser mayor o igual al monto mínimo.');
            return;
        }
        if ($this->max_amount > $this->balance->usdt_balance) {
            session()->flash('error', "El monto máximo no puede exceder tu saldo de {$this->balance->usdt_balance} USDT.");
            return;
        }

        // Normalizar moneda
        $this->local_currency = 'BOB';

        $this->validate([
            'buy_price' => 'required|numeric|min:0.01',
            'sell_price' => 'required|numeric|min:0.01',
            'min_amount' => 'required|numeric|min:10',
            'max_amount' => 'required|numeric|min:10|gte:min_amount',
        ]);

        // Crear oferta
        $coin = Coin::create([
            'user_id' => auth()->id(),
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'local_currency' => $this->local_currency,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'status' => 'active',
        ]);

        // Registrar log
        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => Coin::class,
            'loggable_id' => $coin->id,
            'action' => 'created',
            'description' => 'Oferta creada por el usuario ' . auth()->id(),
            'changes' => $coin->toArray(),
        ]);

        // Asignar rol trader
        auth()->user()->assignRole('trader');

        // Registrar log de rol
        Log::create([
            'user_id' => auth()->id(),
            'loggable_type' => User::class,
            'loggable_id' => auth()->id(),
            'action' => 'role_assigned',
            'description' => 'Rol trader asignado al usuario ' . auth()->id(),
            'changes' => ['roles' => auth()->user()->getRoleNames()->toArray()],
        ]);

        // Actualizar rol
        $this->userRole = 'trader';
        $this->showModalRegisterTrader = false;

        session()->flash('message', '¡Registrado como trader! Tu oferta está activa.');
    }

    public function deposit()
    {
        try {
            $this->validate(['deposit_amount' => 'required|numeric|min:1']);

            $old_balance = $this->balance->usdt_balance;
            $this->balance->usdt_balance += $this->deposit_amount;
            $this->balance->save();

            Log::create([
                'user_id' => auth()->id(),
                'loggable_type' => Balance::class,
                'loggable_id' => $this->balance->id,
                'action' => 'deposit',
                'description' => 'Depósito de ' . $this->deposit_amount . ' USDT',
                'changes' => [
                    'usdt_balance' => ['old' => $old_balance, 'new' => $this->balance->usdt_balance],
                ],
            ]);

            $this->showModalDeposit = false;
            session()->flash('message', 'Depósito simulado exitosamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('deposit_amount', $e->errors()['deposit_amount'][0]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}