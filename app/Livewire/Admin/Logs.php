<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class Logs extends Component
{
    use WithPagination;

    public $selectedLog;
    public $showDetailsModal = false;

    public function mount()
    {
        // Verificar que el usuario tenga el rol admin
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'No tienes permiso para acceder a esta página.');
        }
    }

    public function showDetails($logId)
    {
        $this->selectedLog = Log::with('user')->findOrFail($logId);
        $this->showDetailsModal = true;
    }

    public function render()
    {
        $logs = Log::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.admin.logs', [
            'logs' => $logs,
        ]);
    }
}