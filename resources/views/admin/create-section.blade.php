@extends('admin.functions')
@section('content')
<div>
    <x-alert-message />
    <h3 class="flex w-full justify-center bg-sky-950 px-4 rounded-md text-white mb-6 border-b-4 border-amber-300">Create Section</h3>
    <form action="">
        @csrf
        <div class="flex items-center mb-2">
            <label for="acad_year" class="block text-md font-semibold text-gray-700 mr-2">Academic Year:</label>
            <select id="acad_year" name="acad_year" class="text-md border-gray-300 rounded-md shadow-sm" required>
                @foreach($acad_years as $year)
                <option value="{{ $year->acad_year }}" {{ (isset($activeAcadYear) && $activeAcadYear->id == $year->id) ? 'selected' : '' }}>
                    {{ $year->acad_year }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center">
            <label for="term" class="block text-md font-semibold text-gray-700 mr-2">Term:</label>
            <select id="term" name="term" class="text-md w-1/4 border-gray-300 rounded-md shadow-sm" placeholder="Select Term" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
        </div>
        <div>
            <label for="term" class="block text-md font-semibold text-gray-700 mr-2">Program:</label>
            <select id="programs" name="programs" class="text-md w-1/4 border-gray-300 rounded-md shadow-sm">
                @foreach($programs as $program)
                <option value="{{$program->program_id}}">{{$program->program_code}}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-center">
            <label for="year_level" class="block text-md font-semibold text-gray-700 mr-2">Year Level:</label>
            <select id="year_level" name="year_level" class="text-md w-1/4 border-gray-300 rounded-md shadow-sm" placeholder="Year Level" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="3">4</option>
            </select>
        </div>
        <div>
            <label for="program_name" class="block text-md font-semibold text-gray-700 mr-2">Section Name:</label>
            <input type="text" id="program_name" name="program_name" required class="mt-1 block w-1/4 border-gray-300 rounded-md shadow-sm">
        </div>
        
    </form>
</div>
@endsection