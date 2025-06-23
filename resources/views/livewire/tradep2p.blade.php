<div class="p-6">
    <h1 class="text-2xl font-bold">Mercado P2P</h1>
    <p class="mt-2 text-gray-600">Explora los traders disponibles y sus ofertas para intercambiar USDT por moneda local.</p>

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

    <!-- Tabla de traders -->
    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-x-auto">
        @if (empty($traders))
            <p class="p-4 text-gray-600">No hay traders disponibles en este momento.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trader</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo USDT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Local</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ofertas Activas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($traders as $trader)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trader->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trader->balance ? number_format($trader->balance->usdt_balance, 8) : '0' }} USDT</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trader->balance ? number_format($trader->balance->local_balance, 2) . ' ' . $trader->balance->local_currency : '0 USD' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trader->coins->where('status', 'active')->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="viewTraderOffers({{ $trader->id }})" class="text-indigo-600 hover:text-indigo-900">
                                    Ver Ofertas
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Modal para ver ofertas del trader -->
    <x-modal wire:model="showOffersModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Ofertas de {{ $traders->find($selectedTraderId)->name ?? 'Trader' }}</h2>
            <p class="mt-1 text-sm text-gray-600">Lista de ofertas activas disponibles para este trader.</p>

            @if (empty($selectedTraderOffers))
                <p class="mt-4 text-gray-600">Este trader no tiene ofertas activas.</p>
            @else
                <div class="mt-4 bg-white shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Compra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Venta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Moneda</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mínimo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Máximo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($selectedTraderOffers as $offer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->buy_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->sell_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $offer->local_currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->min_amount, 8) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->max_amount, 8) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="openOrderModal({{ $offer->id }})" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">
                                            Iniciar Orden
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showOffersModal', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>

    <!-- Modal para crear orden -->
    <x-modal wire:model="showOrderModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Crear Orden</h2>
            <p class="mt-1 text-sm text-gray-600">Especifica el monto y tipo de orden para continuar.</p>

            <form wire:submit.prevent="startOrder" class="mt-6 space-y-6">
                <div>
                    <x-label for="type" value="Tipo de Orden" />
                    <select wire:model="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="buy">Compra</option>
                        <option value="sell">Venta</option>
                    </select>
                    <x-input-error for="type" class="mt-2" />
                </div>

                <div>
                    <x-label for="usdt_amount" value="Monto en USDT" />
                    <x-input wire:model="usdt_amount" id="usdt_amount" type="number" step="0.00000001" class="mt-1 block w-full" required
                             placeholder="Entre {{ number_format($min_amount, 8) }} y {{ number_format($max_amount, 8) }}" />
                    <x-input-error for="usdt_amount" class="mt-2" />
                </div>

                <div>
                    <p class="text-sm text-gray-600">
                        Precio: {{ $type === 'buy' ? number_format($sell_price, 2) : number_format($buy_price, 2) }} {{ $local_currency }}/USDT
                    </p>
                    <p class="text-sm text-gray-600">
                        Total en {{ $local_currency }}: {{ number_format($usdt_amount * ($type === 'buy' ? $sell_price : $buy_price), 2) }}
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <x-button type="submit">Crear Orden</x-button>
                    <x-secondary-button wire:click="$set('showOrderModal', false)">
                        Cancelar
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>