<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
    <div class="col-span-2">
        <h1 class="text-2xl font-bold">Dashboard de Trader</h1>

        <!-- Mostrar balance -->
        <div class="mt-6 bg-white shadow-sm rounded-lg p-4">
            <h2 class="text-lg font-medium text-gray-900">Tu Saldo</h2>
            <p class="mt-2 text-gray-600">
                USDT: {{ number_format($balance->usdt_balance, 8) }} USDT
            </p>
            <p class="mt-1 text-gray-600">
                Moneda Local ({{ $balance->local_currency }}): {{ number_format($balance->local_balance, 2) }}
                {{ $balance->local_currency }}
            </p>
            <button wire:click="openModalDeposit"
                class="mt-4 bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Depositar
            </button>
        </div>

        <!-- Mensaje de éxito -->
        @if (session('message'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('message') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Ofertas -->
        <div class="mt-6">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-medium text-gray-900">Tus Ofertas</h2>
                <button wire:click="openModalCreateOffer"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Crear Nueva Oferta
                </button>
            </div>
            @if ($coins->isEmpty())
                <p class="mt-4 text-gray-600">No tienes ofertas creadas.</p>
            @else
                <div class="mt-4 bg-white shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Compra</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Venta</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Moneda</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Mínimo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Máximo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($coins as $coin)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->buy_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->sell_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $coin->local_currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->min_amount, 8) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($coin->max_amount, 8) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="{{ $coin->status === 'active' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $coin->status === 'active' ? 'Activa' : 'Inactiva' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="toggleOfferStatus({{ $coin->id }})"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $coin->status === 'active' ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Órdenes -->
        <div class="mt-6">
            <h2 class="text-lg font-medium text-gray-900">Tus Órdenes</h2>
            @if ($orders->isEmpty())
                <p class="mt-4 text-gray-600">No tienes órdenes registradas.</p>
            @else
                <div class="mt-4 bg-white shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tipo</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    USDT</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Moneda Local</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Estado</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Rol</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($orders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $order->type === 'buy' ? 'Compra' : 'Venta' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->usdt_amount, 8) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ number_format($order->local_amount, 2) }} {{ $order->local_currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="{{ $order->status === 'completed' ? 'text-green-600' : ($order->status === 'pending' ? 'text-yellow-600' : ($order->status === 'queued' ? 'text-blue-600' : ($order->status === 'disputed' ? 'text-red-600' : 'text-red-600'))) }}">
                                            {{ $order->status === 'completed' ? 'Completada' : ($order->status === 'pending' ? 'Pendiente' : ($order->status === 'queued' ? 'En Cola' : ($order->status === 'disputed' ? 'En Disputa' : 'Cancelada'))) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $order->buyer_id === auth()->id() ? 'Comprador' : 'Vendedor' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($order->status === 'queued')
                                            <button wire:click="openModalOrderDetails({{ $order->id }})"
                                                class="text-indigo-600 hover:text-indigo-900">Revisar Orden</button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para crear oferta -->
    <x-modal wire:model="showModalCreateOffer">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Crear Nueva Oferta</h2>
            <p class="mt-1 text-sm text-gray-600">
                Completa los campos para crear una nueva oferta.
            </p>

            <form wire:submit.prevent="createOffer" class="mt-6 space-y-6">
                <div>
                    <x-label for="buy_price" value="Precio de Compra (USDT en moneda local)" />
                    <x-input wire:model="buy_price" id="buy_price" type="number" step="0.01"
                        class="mt-1 block w-full" required />
                    <x-input-error for="buy_price" class="mt-2" />
                </div>

                <div>
                    <x-label for="sell_price" value="Precio de Venta (USDT en moneda local)" />
                    <x-input wire:model="sell_price" id="sell_price" type="number" step="0.01"
                        class="mt-1 block w-full" required />
                    <x-input-error for="sell_price" class="mt-2" />
                </div>

                <div>
                    <x-label for="local_currency" value="Moneda Local (ej. USD)" />
                    <x-input wire:model="local_currency" id="local_currency" type="text" class="mt-1 block w-full"
                        required />
                    <x-input-error for="local_currency" class="mt-2" />
                </div>

                <div>
                    <x-label for="min_amount" value="Monto Mínimo (USDT)" />
                    <x-input wire:model="min_amount" id="min_amount" type="number" step="0.00000001"
                        class="mt-1 block w-full" required />
                    <x-input-error for="min_amount" class="mt-2" />
                </div>

                <div>
                    <x-label for="max_amount" value="Monto Máximo (USDT)" />
                    <x-input wire:model="max_amount" id="max_amount" type="number" step="0.00000001"
                        class="mt-1 block w-full" required />
                    <x-input-error for="max_amount" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-button type="submit">Crear Oferta</x-button>
                    <x-secondary-button wire:click="$set('showModalCreateOffer', false)">
                        Cancelar
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Modal para depositar -->
    <x-modal wire:model="showModalDeposit">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Depositar USDT</h2>
            <p class="mt-1 text-sm text-gray-600">
                Ingresa la cantidad de USDT que deseas depositar (simulación).
            </p>

            <form wire:submit.prevent="deposit" class="mt-6 space-y-6">
                <div>
                    <x-label for="deposit_amount" value="Cantidad (USDT)" />
                    <x-input wire:model="deposit_amount" id="deposit_amount" type="number" step="0.00000001"
                        class="mt-1 block w-full" required />
                    <x-input-error for="deposit_amount" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-button type="submit">Depositar</x-button>
                    <x-secondary-button wire:click="$set('showModalDeposit', false)">
                        Cancelar
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Modal para detalles de la orden -->
    <x-modal wire:model="showModalOrderDetails">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Detalles de la Orden</h2>
            @if ($selectedOrder)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="text-gray-900">{{ $selectedOrder->type === 'buy' ? 'Compra' : 'Venta' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Monto USDT</p>
                        <p class="text-gray-900">{{ number_format($selectedOrder->usdt_amount, 8) }} USDT</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Monto Local</p>
                        <p class="text-gray-900">{{ number_format($selectedOrder->local_amount, 2) }}
                            {{ $selectedOrder->local_currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="text-gray-900">
                            {{ $selectedOrder->status === 'queued' ? 'En Cola' : ($selectedOrder->status === 'completed' ? 'Completada' : ($selectedOrder->status === 'disputed' ? 'En Disputa' : 'Cancelada')) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Trader</p>
                        <p class="text-gray-900">
                            {{ $selectedOrder->seller_id === auth()->id() ? $selectedOrder->buyer->name : $selectedOrder->seller->name }}
                        </p>
                    </div>
                    @if ($selectedOrder->voucher_path)
                        <div>
                            <p class="text-sm text-gray-500">Comprobante</p>
                            <a href="{{ Storage::url($selectedOrder->voucher_path) }}" target="_blank"
                                class="text-blue-600 hover:underline">Ver Comprobante</a>
                        </div>
                    @endif
                </div>
                @if ($selectedOrder->status === 'queued')
                    <div class="mt-6 flex justify-end gap-4">
                        <button wire:click="completeTransaction"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Realizar Transacción
                        </button>
                        <button wire:click="reportError"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Reportar Error
                        </button>
                    </div>
                @endif
            @endif
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showModalOrderDetails', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
