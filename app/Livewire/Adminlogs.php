<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AdminActivity;

class Adminlogs extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = AdminActivity::with('admin')
            ->when($this->search, fn($q) =>
                $q->where('action', 'like', "%{$this->search}%")
                  ->orWhere('model_type', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->latest()
            ->paginate(10);

        return view('livewire.adminlogs', compact('logs'));
    }
}