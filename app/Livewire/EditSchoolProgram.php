<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Program;

class EditSchoolProgram extends Component
{
    public $programId, $program_code, $program_name, $program_description, $degree_type, $department_id, $program_coordinator, $total_units;

    protected $listeners = ['editSchoolProgram' => 'loadProgram'];

    public function loadProgram($programId)
    {
        $program = Program::findOrFail($programId);
        $this->programId = $program->id;
        $this->program_code = $program->program_code;
        // Assign other properties similarly
    }

    public function update()
    {
        $program = Program::find($this->programId);
        // Update attributes
        $program->save();

        // Emit an event to refresh the parent component or show a message
        $this->emit('programUpdated');
    }

    public function render()
    {
        return view('livewire.edit-program');
    }
}

