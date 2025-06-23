<div class="container mx-auto px-6 py-12 max-w-4xl">
    <!-- Header -->
    <h1 class="text-3xl font-bold mb-12 text-gray-900">Gestión de Órdenes (Trader)</h1>

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

    <!-- Lista de órdenes -->
    <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900">Órdenes Pendientes</h2>
        @if ($orders->isEmpty())
            <p class="mt-4 text-gray-600">No tienes órdenes pendientes en este momento.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200 mt-4">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto
                            USDT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto
                            Local</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $order->type === 'buy' ? 'Compra' : 'Venta' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->usdt_amount, 8) }} USDT
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($order->local_amount, 2) }}
                                {{ $order->local_currency }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $order->status === 'queued' ? 'En Cola' : 'Comprobante Subido' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="openOrderModal({{ $order->id }})"
                                    class="text-blue-600 hover:underline">
                                    Ver Detalles
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Modal para detalles de la orden -->
    <x-modal wire:model.live="showOrderModal">
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
                            {{ $selectedOrder->status === 'queued' ? 'En Cola' : 'Comprobante Subido' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Usuario</p>
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
                @if ($selectedOrder->status === 'proof_uploaded')
                    <div class="mt-6 flex justify-end gap-4">
                        <x-button wire:click="confirmOrder" class="bg-green-500 hover:bg-green-700">
                            Confirmar Orden
                        </x-button>
                        <x-button wire:click="reportError" class="bg-red-500 hover:bg-red-700">
                            Reportar Error
                        </x-button>
                    </div>
                @endif
            @endif
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showOrderModal', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
