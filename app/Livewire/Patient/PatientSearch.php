<?php

namespace App\Livewire\Patient;

use App\Helpers\Persian;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientSearch extends Component
{
    public string $query   = '';
    public bool   $isOpen  = false;
    public array  $results = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->isOpen  = false;
            return;
        }

        $searchTerm = Persian::toWestern($this->query);

        $this->results = Patient::search($searchTerm)
            ->select('id', 'first_name', 'last_name', 'phone', 'avatar_color', 'status')
            ->limit(6)
            ->get()
            ->map(fn ($p) => [
                'id'      => $p->id,
                'name'    => $p->full_name,
                'phone'   => $p->phone,
                'initial' => $p->avatar_initial,
                'color'   => $p->avatar_color,
                'code'    => $p->code,
                'status'  => $p->status_label,
                'url'     => route('patients.show', $p),
            ])
            ->toArray();

        $this->isOpen = count($this->results) > 0;
    }

    public function close(): void
    {
        $this->isOpen  = false;
        $this->query   = '';
        $this->results = [];
    }

    public function render(): View
    {
        return view('livewire.patient.patient-search');
    }
}