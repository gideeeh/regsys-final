@extends('admin.records')
@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-2xl font-semibold text-gray-900 mb-6">Schedule for Professor: {{ $professorRecord->first_name . ' ' . $professorRecord->last_name }} - Year: {{ $activeAcadYear }}, Term: {{ $activeTerm }}</h1>
        @if(Auth::check() && Auth::user()->role === 'admin')
        <a href="{{ route('export.schedule', ['prof_id' => $professorRecord->prof_id]) }}" class="button-class">Export to Excel</a>
        @else
        <a href="{{ route('dean-access.export.schedule', ['prof_id' => $professorRecord->prof_id]) }}" class="button-class">Export to Excel</a>
        @endif
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <table class="w-full leading-normal">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">Subject</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">Section</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">Sched F2F</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">Sched OL</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">Room</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($classes as $class)
                        @php
                            $daysF2F = json_decode($class->subjectSectionSchedule->class_days_f2f, true) ?? [];
                            $daysOL = json_decode($class->subjectSectionSchedule->class_days_online, true) ?? [];
                            
                            $dayMap = [
                                'Monday' => 'Mon',
                                'Tuesday' => 'Tue',
                                'Wednesday' => 'Wed',
                                'Thursday' => 'Thu',
                                'Friday' => 'Fri',
                                'Saturday' => 'Sat',
                                'Sunday' => 'Sun',
                            ];

                            $abbreviatedDaysF2F = array_map(function($day) use ($dayMap) {
                                return $dayMap[$day] ?? $day;
                            }, $daysF2F);

                            $abbreviatedDaysOL = array_map(function($day) use ($dayMap) {
                                return $dayMap[$day] ?? $day;
                            }, $daysOL);
                        @endphp
                        <tr class="border-b">
                            <td class="px-5 py-5 text-sm">{{ $class->subject->subject_name }}</td>
                            <td class="px-5 py-5 text-sm">{{ $class->section->section_name }}</td>
                            <td class="px-5 py-5 text-sm">{{ $class->subjectSectionSchedule->schedule_f2f_formatted }}</td>
                            <td class="px-5 py-5 text-sm">{{ $class->subjectSectionSchedule->schedule_ol_formatted }}</td>
                            <td class="px-5 py-5 text-sm">{{ $class->subjectSectionSchedule->room }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-5 text-sm text-center">No classes found for this term.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
