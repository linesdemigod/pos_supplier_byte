<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AuditLog;
use App\Models\Supplier;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class SupplierCreateManager extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    #[URL(as: 'q')]
    public $search = '';
    public $perPage = 10;

    protected $listeners = ['deleteSupplier' => 'delete'];

    public $addName, $addContactInfo, $addAddress, $addEmail;
    public $editName, $editContactInfo, $editAddress, $editEmail, $editId;

    public function submitAdd()
    {
        $this->validate([
            'addName' => 'required|string',
            'addContactInfo' => 'required|string',
            'addAddress' => 'required|string',
            'addEmail' => 'nullable|sometimes|email',
        ]);


        Supplier::create([
            'name' => $this->addName,
            'contact_info' => $this->addContactInfo,
            'address' => $this->addAddress,
            'email' => $this->addEmail,
            'user_id' => auth()->id()
        ]);

        $this->reset(['addName', 'addContactInfo', 'addAddress', 'addEmail']);
        session()->flash('message', 'Supplier added successfully');
        $this->dispatch('formSubmitted', message: 'Supplier added successfully');

    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->editId = $id;
        $this->editName = $supplier->name;
        $this->editContactInfo = $supplier->contact_info;
        $this->editAddress = $supplier->address;
        $this->editEmail = $supplier->email;
    }



    public function submitEdit()
    {
        $this->validate([
            'editName' => 'required|string',
            'editContactInfo' => 'required|string',
            'editAddress' => 'required|string',
            'editEmail' => 'nullable|sometimes|email',
        ]);

        Supplier::where('id', $this->editId)->update([
            'name' => $this->editName,
            'contact_info' => $this->editContactInfo,
            'address' => $this->editAddress,
            'email' => $this->editEmail,
            'user_id' => auth()->id()
        ]);


        $this->reset(['editName', 'editContactInfo', 'editAddress', 'editEmail']);
        session()->flash('message', 'Supplier updated successfully');
        $this->dispatch('close-modal');

    }

    public function delete(Supplier $supplier)
    {
        $user = auth()->user();
        $originalData = $supplier->getOriginal();
        $now = now();

        DB::beginTransaction();
        try {
            # code...
            if (!$supplier) {
                return;
            }

            // delete it
            $supplier->delete();

            AuditLog::create([
                'user_id' => $user->id,
                'store_id' => $user->store_id,
                'ip_address' => request()->ip(),
                'description' => 'Supplier deletion',
                'data_before' => json_encode($originalData),
                'data_after' => json_encode($supplier->getAttributes()),
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::commit();
            session()->flash('message', 'Supplier deleted successfully');
            $this->dispatch('formSubmitted', message: 'Supplier deleted successfully');
        } catch (\Exception $e) {

            DB::rollBack();
            session()->flash('message', 'An error occured');
            $this->dispatch('formSubmitted', message: 'An error occured');
        }


    }

    public function render()
    {
        $suppliers = Supplier::search($this->search)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.supplier-create-manager', [
            'suppliers' => $suppliers
        ]);
    }
}
