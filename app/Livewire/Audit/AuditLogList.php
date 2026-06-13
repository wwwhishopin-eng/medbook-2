<?php

namespace App\Livewire\Audit;

use App\Models\AuditLog;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLogList extends Component
{
    use WithPagination;

    public string $actionFilter = '';
    public string $modelFilter = '';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $search = '';

    public function updatingActionFilter(): void { $this->resetPage(); }
    public function updatingModelFilter(): void { $this->resetPage(); }
    public function updatingDateFrom(): void { $this->resetPage(); }
    public function updatingDateTo(): void { $this->resetPage(); }
    public function updatingSearch(): void { $this->resetPage(); }

    public function render(): View
    {
        $logs = AuditLog::query()
            ->with('user')
            ->when($this->actionFilter, fn($q) => $q->where('action', $this->actionFilter))
            ->when($this->modelFilter, fn($q) => $q->where('model_type', $this->modelFilter))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->search, function ($q) {
                $q->whereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"))
                  ->orWhere('model_type', 'like', "%{$this->search}%");
            })
            ->orderByDesc('id')
            ->paginate(20);

        $modelTypes = AuditLog::query()
            ->distinct()
            ->pluck('model_type')
            ->filter()
            ->mapWithKeys(fn($t) => [$t => class_basename($t)])
            ->toArray();

        return view('livewire.audit.audit-log-list', [
            'logs' => $logs,
            'modelTypes' => $modelTypes,
        ]);
    }
}
