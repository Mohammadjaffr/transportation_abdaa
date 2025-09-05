<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Presentation;
use App\Models\Bus;

class Presentations extends Component
{
    public $presentations, $buses;
    public $bus_id, $atendTime, $atendStudents, $leaveTime, $leaveStudents, $note, $date;
    public $editMode = false, $selectedPresentation;
    public $search = '';
    public $showForm = false;
    public $deleteId = null;
    public $editId = null;
    protected $rules = [
        'bus_id' => 'required|exists:buses,id',
        'atendTime' => 'required|string|max:50',
        'atendStudents' => 'required|integer|min:0',
        'leaveTime' => 'required|string|max:30',
        'leaveStudents' => 'required|integer|min:0',
        'note' => 'nullable|string|max:60',
        'date' => 'required|string|max:20'
    ];
    protected $messages = [
        'bus_id.required' => 'رقم الباص مطلوب',
        'bus_id.exists'   => 'رقم الباص غير موجود في النظام',

        'atendTime.required' => 'وقت الحضور مطلوب',
        'atendTime.string'   => 'وقت الحضور يجب أن يكون نصًا',
        'atendTime.max'      => 'وقت الحضور لا يجب أن يتجاوز 50 حرفًا',

        'atendStudents.required' => 'عدد الحاضرين مطلوب',
        'atendStudents.integer'  => 'عدد الحاضرين يجب أن يكون رقمًا صحيحًا',
        'atendStudents.min'      => 'عدد الحاضرين لا يمكن أن يكون أقل من صفر',

        'leaveTime.required' => 'وقت الانصراف مطلوب',
        'leaveTime.string'   => 'وقت الانصراف يجب أن يكون نصًا',
        'leaveTime.max'      => 'وقت الانصراف لا يجب أن يتجاوز 30 حرفًا',

        'leaveStudents.required' => 'عدد المنصرفين مطلوب',
        'leaveStudents.integer'  => 'عدد المنصرفين يجب أن يكون رقمًا صحيحًا',
        'leaveStudents.min'      => 'عدد المنصرفين لا يمكن أن يكون أقل من صفر',

        'note.string' => 'الملاحظات يجب أن تكون نصًا',
        'note.max'    => 'الملاحظات لا يجب أن تتجاوز 60 حرفًا',

        'date.required' => 'التاريخ مطلوب',
        'date.string'   => 'التاريخ يجب أن يكون نصًا',
        'date.max'      => 'التاريخ لا يجب أن يتجاوز 20 حرفًا',
    ];


    public function mount()
    {
        $this->buses = Bus::all();
        $this->presentations = Presentation::with('bus')->get();
        $this->date = now()->format('Y-m-d');
    }

    public function createPresentation()
    {
        $this->validate();

        Presentation::create([
            'bus_id' => $this->bus_id,
            'atendTime' => $this->atendTime,
            'atendStudents' => $this->atendStudents,
            'leaveTime' => $this->leaveTime,
            'leaveStudents' => $this->leaveStudents,
            'note' => $this->note,
            'date' => $this->date
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تسجيل الحضور بنجاح']);
    }

    public function editPresentation($id)
    {
        $pres = Presentation::find($id);
        $this->selectedPresentation = $this->editId = $id;
        $this->editMode = true;
        $this->showForm = true;

        $this->bus_id = $pres->bus_id;
        $this->atendTime = $pres->atendTime;
        $this->atendStudents = $pres->atendStudents;
        $this->leaveTime = $pres->leaveTime;
        $this->leaveStudents = $pres->leaveStudents;
        $this->note = $pres->note;
        $this->date = $pres->date;
    }

    public function updatePresentation()
    {
        $this->validate();
        $pres = Presentation::find($this->selectedPresentation);
        $pres->update([
            'bus_id' => $this->bus_id,
            'atendTime' => $this->atendTime,
            'atendStudents' => $this->atendStudents,
            'leaveTime' => $this->leaveTime,
            'leaveStudents' => $this->leaveStudents,
            'note' => $this->note,
            'date' => $this->date
        ]);

        $this->resetForm();
        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم تحديث سجل الحضور بنجاح']);
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
    }

    public function deletePresentation()
    {
        if ($this->deleteId) {
            Presentation::find($this->deleteId)->delete();
            $this->deleteId = null;
            $this->dispatch('show-toast', ['type' => 'success', 'message' => 'تم حذف سجل الحضور بنجاح']);
        }
    }

    public function cancel()
    {
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['bus_id', 'atendTime', 'atendStudents', 'leaveTime', 'leaveStudents', 'note', 'date', 'editMode', 'selectedPresentation', 'showForm']);
        $this->showForm = false;
    }

    public function render()
    {
        $this->presentations = Presentation::with('bus')
            ->whereHas('bus', fn($q) => $q->where('BusType', 'like', '%' . $this->search . '%'))
            ->orWhere('date', 'like', '%' . $this->search . '%')
            ->get();

        return view('livewire.presentations');
    }
}
