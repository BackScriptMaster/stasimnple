<div class="container mx-auto px-6 py-12 max-w-7xl">
    <!-- Header -->
    <h1 class="text-3xl font-bold mb-12 text-gray-900">Registros del Sistema</h1>

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

    <!-- Tabla de logs -->
    <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
        @if ($logs->isEmpty())
            <p class="p-6 text-gray-600">No hay registros disponibles.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Descripción</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cambios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $log->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $log->user ? $log->user->name : 'Desconocido' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $log->action }}</td>
                            <td class="px-6 py-4">{{ $log->description }}</td>
                            <td class="px-6 py-4">
                                @if ($log->changes)
                                    {{ Str::limit(json_encode($log->changes), 50) }}
                                @else
                                    Sin cambios
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="showDetails({{ $log->id }})"
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

    <!-- Paginación -->
    <div class="mt-6">
        {{ $logs->links() }}
    </div>

    <!-- Modal para detalles del log -->
    <x-modal wire:model.live="showDetailsModal" max-width="lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Detalles del Registro</h2>
            @if ($selectedLog)
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ID</p>
                        <p class="text-gray-900">{{ $selectedLog->id }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Usuario</p>
                        <p class="text-gray-900">{{ $selectedLog->user ? $selectedLog->user->name : 'Desconocido' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Acción</p>
                        <p class="text-gray-900">{{ $selectedLog->action }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipo Relacionado</p>
                        <p class="text-gray-900">{{ $selectedLog->loggable_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ID Relacionado</p>
                        <p class="text-gray-900">{{ $selectedLog->loggable_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Descripción</p>
                        <p class="text-gray-900">{{ $selectedLog->description }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-500">Cambios</p>
                        <pre class="bg-gray-100 p-4 rounded text-sm text-gray-900 overflow-auto">
                            {{ json_encode($selectedLog->changes, JSON_PRETTY_PRINT) }}
                        </pre>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Fecha</p>
                        <p class="text-gray-900">{{ $selectedLog->created_at->format('d/m/Y H:i:s') }}</p>
                    </div>
                </div>
            @endif
            <div class="mt-6 flex justify-end">
                <x-secondary-button wire:click="$set('showDetailsModal', false)">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    </x-modal>
</div>
