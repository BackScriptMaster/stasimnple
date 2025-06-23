<div>
    <div>
        <div class="container mx-auto px-6 py-12 max-w-4xl">
            <h1 class="text-3xl font-bold mb-8 text-gray-900">Proceso de Pago</h1>

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

            <div class="mb-12 bg-white shadow-sm rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900">Detalles de la Orden</h2>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="text-gray-900">{{ $order->type === 'buy' ? 'Compra' : 'Venta' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tu Rol</p>
                        <p class="text-gray-900">{{ $order->buyer_id === auth()->id() ? 'Comprador' : 'Vendedor' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Monto USDT</p>
                        <p class="text-gray-900">{{ number_format($order->usdt_amount, 8) }} USDT</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Monto Local</p>
                        <p class="text-gray-900">{{ number_format($order->local_amount, 2) }}
                            {{ $order->local_currency }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="text-gray-900">
                            {{ $order->status === 'queued' ? 'En Cola' : ($order->status === 'confirmed' ? 'Pago Confirmado' : ($order->status === 'proof_uploaded' ? 'Comprobante Subido' : ($order->status === 'completed' ? 'Completada' : ($order->status === 'disputed' ? 'En Disputa' : 'Cancelada')))) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Trader</p>
                        <p class="text-gray-900">
                            {{ $order->seller_id === auth()->id() ? $order->buyer->name : $order->seller->name }}</p>
                    </div>
                    @if ($order->voucher_path)
                        <div>
                            <p class="text-sm text-gray-500">Comprobante</p>
                            <a href="{{ Storage::url($order->voucher_path) }}" target="_blank"
                                class="text-blue-600 hover:underline">Ver Comprobante</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-12">
                <div class="flex items-center justify-between bg-gray-100 rounded-xl p-6 border border-gray-200">
                    <div class="flex flex-col items-center text-center flex-1">
                        <div
                            class="w-8 h-8 rounded-full {{ in_array($order->status, ['queued', 'confirmed', 'proof_uploaded', 'completed']) ? 'bg-green-500' : 'bg-blue-500' }} flex items-center justify-center mb-2">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span
                            class="text-sm {{ in_array($order->status, ['queued', 'confirmed', 'proof_uploaded', 'completed']) ? 'text-green-400' : 'text-blue-400' }} font-medium">Orden
                            en Cola</span>
                    </div>

                    <div
                        class="flex-1 h-px {{ in_array($order->status, ['confirmed', 'proof_uploaded', 'completed']) ? 'bg-green-100' : 'bg-gray-300' }} mx-4">
                    </div>

                    <div class="flex flex-col items-center text-center flex-1">
                        <div
                            class="w-8 h-8 rounded-full {{ in_array($order->status, ['confirmed', 'proof_uploaded', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            @if (in_array($order->status, ['confirmed', 'proof_uploaded', 'completed']))
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                </svg>
                            @endif
                        </div>
                        <span
                            class="text-sm {{ in_array($order->status, ['confirmed', 'proof_uploaded', 'completed']) ? 'text-green-400' : 'text-gray-400' }} font-medium">
                            {{ $order->buyer_id === auth()->id() ? 'Confirmar Pago Enviado' : 'Confirmar Recepción Pago' }}
                        </span>
                    </div>

                    <div
                        class="flex-1 h-px {{ in_array($order->status, ['proof_uploaded', 'completed']) ? 'bg-green-100' : 'bg-gray-300' }} mx-4">
                    </div>

                    <div class="flex flex-col items-center text-center flex-1">
                        <div
                            class="w-8 h-8 rounded-full {{ in_array($order->status, ['proof_uploaded', 'completed']) ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                            @if (in_array($order->status, ['proof_uploaded', 'completed']))
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                                </svg>
                            @endif
                        </div>
                        <span
                            class="text-sm {{ in_array($order->status, ['proof_uploaded', 'completed']) ? 'text-green-400' : 'text-gray-400' }} font-medium">
                            {{ $order->buyer_id === auth()->id() ? 'Subir Comprobante' : 'Esperar Comprobante' }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 h-px {{ $order->status === 'completed' ? 'bg-green-100' : 'bg-gray-300' }} mx-4">
                </div>

                <div class="flex flex-col items-center text-center flex-1">
                    <div
                        class="w-8 h-8 rounded-full {{ $order->status === 'completed' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center mb-2">
                        @if ($order->status === 'completed')
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z">
                            </svg>
                        @endif
                    </div>
                    <span
                        class="text-sm {{ $order->status === 'completed' ? 'text-green-400' : 'text-gray-400' }} font-medium">Completada</span>
                </div>
            </div>
        </div>


        @if ($order->status === 'queued')
            <div class="bg-white rounded-xl p-8 border border-gray-200 mt-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100 010 8 0 000 016zm0 012a0 0 0 010-2 0v0a0 0 0 00.0 .707l2.828 2.829a0 0 0 101001-0.010L00 0.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Orden en espera</h2>
                </div>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Tu orden está en cola y será revisada por el trader. Por favor, confirma
                    {{ $order->buyer_id === auth()->id() ? 'el envío del pago' : 'la recepción del pago' }} para
                    continuar.
                </p>
                <div class="mt-6 text-center">
                    <button wire:click="confirmTransaction"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                        {{ $order->buyer_id === auth()->id() ? 'Confirmar Pago Enviado' : 'Confirmar Recepción Pago' }}
                    </button>
                    <button wire:click="openStatusModal"
                        class="ml-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded-lg">
                        Ver Estado
                    </button>
                </div>
            </div>
        @elseif ($order->status === 'confirmed' && $order->buyer_id === auth()->id())
            <div class="mt-6">
                <div class="bg-white rounded-xl p-6 border border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Comprobante de Pago</h3>
                    <div class="mt-4">
                        <input type="file" wire:model="voucher" accept="image/jpeg,image/png,image/jpg"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('voucher')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="mt-6 text-center">
                        <button wire:click="uploadProof"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                            Subir Comprobante
                        </button>
                    </div>
                </div>
            </div>
        @elseif ($order->status === 'confirmed' && $order->seller_id === auth()->id())
            <div class="bg-white rounded-xl p-8 border border-gray-200 mt-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Esperando Comprobante</h2>
                </div>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    El comprador ha confirmado el pago. Por favor, espera a que suba el comprobante para revisar la
                    transacción.
                </p>
                <button wire:click="openStatusModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center mx-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Estado de la Orden
                </button>
            </div>
        @elseif ($order->status === 'proof_uploaded')
            <div class="bg-white rounded-xl p-8 border border-gray-200 mt-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Esperando Confirmación del Trader</h2>
                </div>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    El comprobante ha sido subido. El trader está revisando la transacción.
                </p>
                <button wire:click="openStatusModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center mx-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Estado de la Orden
                </button>
            </div>
        @elseif ($order->status === 'completed')
            <div class="bg-white rounded-xl p-8 border border-gray-200 mt-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Orden Completada</h2>
                </div>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Tu orden ha sido completada exitosamente. Revisa los detalles en el estado de la orden.
                </p>
                <button wire:click="openStatusModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center mx-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Estado de la Orden
                </button>
            </div>
        @elseif ($order->status === 'disputed')
            <div class="bg-white rounded-xl p-8 border border-gray-200 mt-6">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 rounded-full bg-red-500 flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Orden en Disputa</h2>
                </div>
                <p class="text-gray-600 mb-8 leading-relaxed">
                    Tu orden está en disputa. Por favor, contacta al soporte para resolver el problema.
                </p>
                <button wire:click="openStatusModal"
                    class="bg-blue-500 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center mx-auto">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Ver Estado de la Orden
                </button>
            </div>
        @endif
    </div>
    <!-- Este div cierra el contenedor principal del componente Livewire -->
    <x-modal wire:model="showStatusModal" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Estado de la Orden</h2>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Tipo</p>
                    <p class="text-gray-900">{{ $order->type === 'buy' ? 'Compra' : 'Venta' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tu Rol</p>
                    <p class="text-gray-900">{{ $order->buyer_id === auth()->id() ? 'Comprador' : 'Vendedor' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Monto USDT</p>
                    <p class="text-gray-900">{{ number_format($order->usdt_amount, 8) }} USDT</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Monto Local</p>
                    <p class="text-gray-900">{{ number_format($order->local_amount, 2) }}
                        {{ $order->local_currency }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Estado</p>
                    <p class="text-gray-900">
                        {{ $order->status === 'queued' ? 'En Cola' : ($order->status === 'confirmed' ? 'Pago Confirmado' : ($order->status === 'proof_uploaded' ? 'Comprobante Subido' : ($order->status === 'completed' ? 'Completada' : ($order->status === 'disputed' ? 'En Disputa' : 'Cancelada')))) }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Trader</p>
                    <p class="text-gray-900">
                        {{ $order->seller_id === auth()->id() ? $order->buyer->name : $order->seller->name }}</p>
                </div>
                @if ($order->voucher_path)
                    <div>
                        <p class="text-sm text-gray-500">Comprobante</p>
                        <a href="{{ Storage::url($order->voucher_path) }}" target="_blank"
                            class="text-blue-600 hover:underline">Ver Comprobante</a>
                    </div>
                @endif
                @if ($order->status === 'queued')
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Nota</p>
                        <p class="text-gray-900">La orden está en cola esperando la confirmación del trader.</p>
                    </div>
                @elseif ($order->status === 'disputed')
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Nota</p>
                        <p class="text-gray-900">La orden está en disputa. Contacta al soporte para más información.
                        </p>
                    </div>
                @endif
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showStatusModal', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>

