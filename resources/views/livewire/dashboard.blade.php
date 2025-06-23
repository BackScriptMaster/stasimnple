<div class="container mx-auto px-6 py-12 max-w-4xl">
    <!-- Mensajes -->
    @if (session('message'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
    @endif

    <!-- Contenido según el rol -->
    @if ($userRole === 'admin')
        <!-- Vista para administradores -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Panel de Administración</h2>
            <p class="mt-2 text-sm text-gray-600">Accede a los registros del sistema para auditoría.</p>
            <div class="mt-6">
                <a href="{{ route('admin.logs') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Ver Logs del Sistema
                </a>
            </div>
        </div>
    @elseif ($userRole === 'trader')
        <!-- Vista para traders -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Panel de Trader</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-Ist das korrekt?4">
                <div>
                    <p class="text-sm text-gray-500">Saldo USDT</p>
                    <p class="text-gray-900">{{ number_format($balance->usdt_balance, 8) }} USDT</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Saldo Local</p>
                    <p class="text-gray-900">{{ number_format($balance->local_balance, 2) }} {{ $balance->local_currency }}</p>
                </div>
            </div>
            <div class="mt-6">
                <x-button wire:click="openModalDeposit" class="bg-blue-500 hover:bg-blue-700">
                    Depositar
                </x-button>
                <a href="{{ route('tradep2p') }}"
                   class="ml-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Ver Mercado P2P
                </a>
            </div>
        </div>

        <!-- Ofertas Activas del Trader -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Tus Ofertas Activas</h2>
            @if (auth()->user()->coins->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 mt-4">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venta</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moneda</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mínimo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Máximo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach (auth()->user()->coins->where('status', 'active') as $coin)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->buy_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->sell_price, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coin->local_currency }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->min_amount, 8) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->max_amount, 8) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="mt-4 text-gray-600">No tienes ofertas activas.</p>
            @endif
        </div>

        <!-- Órdenes Recientes -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Órdenes Recientes</h2>
            @if ($orders->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 mt-4">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto USDT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Local</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->type === 'buy' ? 'Compra' : 'Venta' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->usdt_amount, 8) }} USDT</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->local_amount, 2) }} {{ $order->local_currency }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $order->status === 'pending' ? 'Pendiente' : ($order->status === 'confirmed' ? 'Pago Confirmado' : ($order->status === 'proof_uploaded' ? 'Comprobante Subido' : ($order->status === 'queued' ? 'En Cola' : ($order->status === 'completed' ? 'Completada' : 'En Disputa')))) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('payment.process', ['datetime' => $order->created_at->format('YmdHis')]) }}"
                                       class="text-blue-600 hover:underline">Ver Detalles</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="mt-4 text-gray-600">No tienes órdenes recientes.</p>
            @endif
        </div>
    @else
        <!-- Vista para usuarios regulares -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Balance</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Saldo USDT</p>
                    <p class="text-gray-900">{{ number_format($balance->usdt_balance, 8) }} USDT</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Saldo Local</p>
                    <p class="text-gray-900">{{ number_format($balance->local_balance, 2) }} {{ $balance->local_currency }}</p>
                </div>
            </div>
            <div class="mt-6">
                <x-button wire:click="openModalDeposit" class="bg-blue-500 hover:bg-blue-700">
                    Depositar
                </x-button>
                @if (!auth()->user()->hasRole('trader'))
                    <x-button wire:click="openModalRegisterTrader" class="bg-green-500 hover:bg-green-700 ml-4">
                        Registrarme como Trader
                    </x-button>
                @endif
                <a href="{{ route('tradep2p') }}"
                   class="ml-4 bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Ir al Mercado P2P
                </a>
            </div>
        </div>

        <!-- Órdenes Recientes -->
        <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900">Órdenes Recientes</h2>
            @if ($orders->count() > 0)
                <table class="min-w-full divide-y divide-gray-200 mt-4">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto USDT</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Local</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $order->type === 'buy' ? 'Compra' : 'Venta' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->usdt_amount, 8) }} USDT</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->local_amount, 2) }} {{ $order->local_currency }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $order->status === 'pending' ? 'Pendiente' : ($order->status === 'confirmed' ? 'Pago Confirmado' : ($order->status === 'proof_uploaded' ? 'Comprobante Subido' : ($order->status === 'queued' ? 'En Cola' : ($order->status === 'completed' ? 'Completada' : 'En Disputa')))) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('payment.process', ['datetime' => $order->created_at->format('YmdHis')]) }}"
                                       class="text-blue-600 hover:underline">Ver Detalles</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="mt-4 text-gray-600">No tienes órdenes recientes.</p>
            @endif
        </div>
    @endif

    <!-- Modal para registrarse como trader -->
<x-modal wire:model.live="showModalRegisterTrader" max-width="lg">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Registrarse como Trader</h2>
        <p class="mt-1 text-sm text-gray-600">
            Completa los detalles para registrarte como trader y crear tu primera oferta.
        </p>

        <form wire:submit.prevent="registerAsTrader" class="mt-6 space-y-4">
            @if ($errors->has('form_error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $errors->first('form_error') }}
                </div>
            @endif
            <div>
                <x-label for="buy_price" value="Precio de Compra (USDT)" />
                <x-input wire:model.live="buy_price" id="buy_price" type="number" step="0.01" class="mt-1 block w-full" required />
                @error('buy_price') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-label for="sell_price" value="Precio de Venta (USDT)" />
                <x-input wire:model.live="sell_price" id="sell_price" type="number" step="0.01" class="mt-1 block w-full" required />
                @error('sell_price') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-label for="local_currency" value="Moneda Local" />
                <x-input wire:model.live="local_currency" id="local_currency" type="text" class="mt-1 block w-full" required oninput="this.value = this.value.toUpperCase()" />
                @error('local_currency') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-label for="min_amount" value="Monto Mínimo (USDT)" />
                <x-input wire:model.live="min_amount" id="min_amount" type="number" step="0.01" class="mt-1 block w-full" required />
                @error('min_amount') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>
            <div>
                <x-label for="max_amount" value="Monto Máximo (USDT)" />
                <x-input wire:model.live="max_amount" id="max_amount" type="number" step="0.01" class="mt-1 block w-full" required />
                @error('max_amount') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-4">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading wire:target="registerAsTrader">Procesando...</span>
                    <span wire:loading.remove wire:target="registerAsTrader">Regístrame</span>
                </x-button>
                <x-secondary-button wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
            </div>
        </form>
    </div>
</x-modal>

<!-- Modal para depósito -->
<x-modal wire:model.live="showModalDeposit" max-width="sm">
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900">Depositar USDT</h2>
        <p class="mt-1 text-sm text-gray-600">
            Ingresa la cantidad de USDT que deseas depositar (simulación).
        </p>

        <form wire:submit.prevent="deposit" class="mt-6 space-y-4">
            @if ($errors->has('form_error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $errors->first('form_error') }}
                </div>
            @endif
            <div>
                <x-label for="deposit_amount" value="Cantidad (USDT)" />
                <x-input wire:model.live="deposit_amount" id="deposit_amount" type="number" step="0.00000001" class="mt-1 block w-full" required />
                @error('deposit_amount') <span class="mt-2 text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center gap-4">
                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading wire:target="deposit">Procesando...</span>
                    <span wire:loading.remove wire:target="deposit">Confirmar</span>
                </x-button>
                <x-secondary-button wire:click="closeModal">
                    Cancelar
                </x-secondary-button>
            </div>
        </form>
    </div>
</x-modal>
</div>