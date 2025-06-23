<div class="p-6">
    <h1 class="text-2xl font-bold">Mercado P2P</h1>
    <p class="mt-2 text-gray-600">Explora los traders disponibles y sus ofertas para intercambiar USDT por moneda local.
    </p>

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

    <div class="mt-6 bg-white shadow-sm rounded-lg overflow-x-auto">
        @if ($traders->isEmpty())
            <p class="p-4 text-gray-600">No hay traders disponibles en este momento.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Trader
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Saldo
                            USDT</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">Saldo
                            Local</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">
                            Ofertas Activas</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($traders as $trader)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $trader->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trader->balance ? number_format($trader->balance->usdt_balance, 8) : '0' }} USDT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trader->balance ? number_format($trader->balance->local_balance, 2) . ' ' . $trader->balance->local_currency : '0 USD' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $trader->coins->where('status', 'active')->count() }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button wire:click="viewTraderOffers({{ $trader->id }})"
                                    class="text-blue-600 hover:text-blue-900">
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
    <x-modal wire:model="showOffersModal" max-width="2xl">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Ofertas de
                {{ $traders->find($selectedTraderId)->name ?? 'Trader' }}</h2>
            <p class="mt-1 text-sm text-gray-600">Lista de ofertas activas disponibles para este trader.</p>

            @if (empty($selectedTraderOffers))
                <p class="mt-4 text-gray-600">Este trader no tiene ofertas activas.</p>
            @else
                <div class="mt-4 bg-white shadow-sm rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Compra</th>
                                <th
                                    class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Venta</th>
                                <th
                                    class="px-6 py-3 text-left text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Moneda</th>
                                <th
                                    class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Mínimo</th>
                                <th
                                    class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Máximo</th>
                                <th
                                    class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase tracking-wider">
                                    Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($selectedTraderOffers as $offer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->buy_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        {{ number_format($offer->sell_price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $offer->local_currency }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        {{ number_format($offer->min_amount, 8) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        {{ number_format($offer->max_amount, 8) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right space-x-2">
                                        <button wire:click="openOrderModal({{ $offer->id }}, 'buy')"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded">
                                            Iniciar Compra
                                        </button>
                                        <button wire:click="openOrderModal({{ $offer->id }}, 'sell')"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                            Iniciar Venta
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
    <x-modal wire:model="showOrderModal" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ $type === 'buy' ? 'Crear Orden de Compra' : 'Crear Orden de Venta' }}</h2>
            <p class="mt-1 text-sm text-gray-600">Especifica el monto para continuar con la
                {{ $type === 'buy' ? 'compra' : 'venta' }}.</p>

            <form wire:submit.prevent="startOrder" class="mt-6 space-y-6">
                <div>
                    <x-label for="usdt_amount" value="Monto en USDT" />
                    <x-input wire:model="usdt_amount" id="usdt_amount" type="number" step="0.00000001"
                        class="mt-1 block w-full" required
                        placeholder="Entre {{ number_format($min_amount, 8) }} y {{ number_format($max_amount, 8) }}" />
                    <x-input-error for="usdt_amount" class="mt-2" />
                </div>

                <div>
                    <p class="text-sm text-gray-600">
                        Precio: {{ $type === 'buy' ? number_format($sell_price, 2) : number_format($buy_price, 2) }}
                        {{ $local_currency }}/USDT
                    </p>
                    <p class="text-sm text-gray-600">
                        Total en {{ $local_currency }}:
                        {{ number_format($usdt_amount * ($type === 'buy' ? $sell_price : $buy_price), 2) }}
                    </p>
                    @if ($type === 'sell')
                        <p class="text-sm text-gray-600 mt-2">
                            Tu saldo USDT: {{ number_format(auth()->user()->balance->usdt_balance, 8) }} USDT
                        </p>
                    @endif
                </div>

                <div class="flex items-center gap-4">
                    <x-button
                        type="submit">{{ $type === 'buy' ? 'Crear Orden de Compra' : 'Crear Orden de Venta' }}</x-button>
                    <x-secondary-button wire:click="$set('showOrderModal', false)">
                        Cancelar
                    </x-secondary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>
