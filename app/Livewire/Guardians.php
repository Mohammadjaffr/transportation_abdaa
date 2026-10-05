<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Guardian;
use App\Services\AdminLoggerService;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GuardiansImport;

class Guardians extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $phone = '';
    public $national_id = '';
    public $address = '';
    public $is_active = true;

    public $search = '';

    public $showForm = false;
    public $editId = null;

    public $deleteId = null;
    public $deleteName = null;

    public $showImportForm = false;
    public $excelFile;
    public $showImportModal = false;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'يرجى إدخال اسم ولي الأمر.',
            'name.max' => 'اسم ولي الأمر يجب ألا يتجاوز 150 حرفًا.',

            'phone.required' => 'يرجى إدخال رقم هاتف ولي الأمر.',
            'phone.max' => 'رقم الهاتف يجب ألا يتجاوز 20 حرفًا.',

            'national_id.max' => 'رقم الهوية يجب ألا يتجاوز 50 حرفًا.',

            'address.max' => 'العنوان يجب ألا يتجاوز 255 حرفًا.',
        ];
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateForm()
    {
        $this->clearForm();

        $this->showForm = true;
        $this->is_active = true;
    }

    public function createGuardian()
    {
        $this->validate();

        $guardian = Guardian::create([
            'name' => trim($this->name),
            'phone' => trim($this->phone),
            'national_id' => $this->national_id
                ? trim($this->national_id)
                : null,
            'address' => $this->address
                ? trim($this->address)
                : null,
            'is_active' => (bool) $this->is_active,
        ]);

        AdminLoggerService::log(
            'إضافة ولي أمر',
            'Guardian',
            "تم إضافة ولي الأمر: {$guardian->name}"
        );

        $this->cancelForm();

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم إضافة ولي الأمر بنجاح'
        );
    }

    public function editGuardian($id)
    {
        $guardian = Guardian::findOrFail($id);

        $this->resetValidation();

        $this->editId = $guardian->id;

        $this->name = $guardian->name;
        $this->phone = $guardian->phone;
        $this->national_id = $guardian->national_id ?? '';
        $this->address = $guardian->address ?? '';
        $this->is_active = (bool) $guardian->is_active;

        $this->showForm = true;
    }

    public function updateGuardian()
    {
        $this->validate();

        $guardian = Guardian::findOrFail($this->editId);

        $guardian->update([
            'name' => trim($this->name),
            'phone' => trim($this->phone),
            'national_id' => $this->national_id
                ? trim($this->national_id)
                : null,
            'address' => $this->address
                ? trim($this->address)
                : null,
            'is_active' => (bool) $this->is_active,
        ]);

        AdminLoggerService::log(
            'تعديل ولي أمر',
            'Guardian',
            "تم تعديل بيانات ولي الأمر: {$guardian->name}"
        );

        $this->cancelForm();

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم تحديث بيانات ولي الأمر بنجاح'
        );
    }

    public function toggleActive($id)
    {
        $guardian = Guardian::findOrFail($id);

        $guardian->is_active = !$guardian->is_active;
        $guardian->save();

        $status = $guardian->is_active ? 'مفعل' : 'موقوف';

        AdminLoggerService::log(
            'تغيير حالة ولي أمر',
            'Guardian',
            "تم تغيير حالة ولي الأمر {$guardian->name} إلى {$status}"
        );

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: "تم تغيير حالة ولي الأمر إلى {$status}"
        );
    }

    public function confirmDelete($id)
    {
        $guardian = Guardian::findOrFail($id);

        $this->deleteId = $guardian->id;
        $this->deleteName = $guardian->name;
    }

    public function cancelDelete()
    {
        $this->deleteId = null;
        $this->deleteName = null;
    }

    public function deleteGuardian()
    {
        if (!$this->deleteId) {
            return;
        }

        $guardian = Guardian::findOrFail($this->deleteId);

        $guardianName = $guardian->name;

        $guardian->delete();

        AdminLoggerService::log(
            'حذف ولي أمر',
            'Guardian',
            "تم حذف ولي الأمر: {$guardianName}"
        );

        $this->cancelDelete();

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم حذف ولي الأمر بنجاح'
        );
    }

    public function cancelForm()
    {
        $this->clearForm();

        $this->showForm = false;
    }

    private function clearForm()
    {
        $this->reset([
            'name',
            'phone',
            'national_id',
            'address',
            'editId',
        ]);

        $this->is_active = true;

        $this->resetValidation();
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
    }

    public function importExcel()
    {
        $this->validate(
            [
                'excelFile' => 'required|mimes:xlsx,csv',
            ],
            [
                'excelFile.required' => 'يرجى اختيار ملف Excel',
                'excelFile.mimes' => 'يجب أن يكون الملف بصيغة Excel (xlsx) أو CSV فقط',
            ]
        );

        $import = new GuardiansImport();

        Excel::import(
            $import,
            $this->excelFile->getRealPath()
        );

        if ($import->failures()->isNotEmpty()) {
            $labels = [
                'alasm' => 'عمود الاسم',
                'rkm_alhatf' => 'عمود الهاتف',
                'rkm_alhoaa' => 'عمود الهوية',
                'alaanoan' => 'عمود العنوان',
            ];

            foreach ($import->failures() as $failure) {
                $row = $failure->row();
                $attr = $failure->attribute();
                $value = $failure->values()[$attr] ?? '';
                $label = $labels[$attr] ?? $attr;

                foreach ($failure->errors() as $msg) {
                    $pretty = "الصف {$row} – {$label}: {$msg}" . ($value !== '' ? " (القيمة: {$value})" : '');
                    $this->addError('excelFile', $pretty);
                }
            }

            return;
        }

        $fileName = $this->excelFile->getClientOriginalName();

        AdminLoggerService::log(
            'استيراد ملف Excel لأولياء الأمور',
            'Guardian',
            "تم استيراد أولياء الأمور من الملف: {$fileName}"
        );

        $this->reset('excelFile', 'showImportForm');

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: 'تم استيراد أولياء الأمور بنجاح'
        );
    }

    public function resetImportForm()
    {
        $this->reset('excelFile', 'showImportForm');
    }

    public function render()
    {
        $guardians = Guardian::query()
            ->withCount('students')
            ->when($this->search, function ($query) {

                $search = '%' . trim($this->search) . '%';

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', $search)
                        ->orWhere('phone', 'like', $search)
                        ->orWhere('national_id', 'like', $search)
                        ->orWhere('address', 'like', $search);
                });
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.guardians', [
            'guardians' => $guardians,
        ]);
    }
}