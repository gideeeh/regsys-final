<div>
    <form wire:submit.prevent="update">
        <div>
            <label>Program Code:</label>
            <input type="text" wire:model="program.program_code">
        </div>
        
        <!-- Repeat for other fields -->
        
        <div>
            <label>Department:</label>
            <select wire:model="program.department_id">
                @foreach($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->dept_name }}</option>
                @endforeach
            </select>
        </div>
        
        <button type="submit">Update Program</button>
    </form>
</div>
