<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Program;

class DeleteProgram extends Component
{
    public $programId;

    protected $listeners = ['deleteProgram' => 'loadProgram'];

    public function loadProgram($programId)
    {
        $this->programId = $programId;
    }

    public function delete()
    {
        Program::find($this->programId)->delete();

        $this->emit('programDeleted'); // Notify the parent component that the deletion was successful
        $this->dispatchBrowserEvent('close-modal'); // Close the modal
    }

    public function render()
    {
        return view('livewire.delete-program');
    }
}
