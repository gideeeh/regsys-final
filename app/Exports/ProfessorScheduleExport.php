<?php

namespace App\Exports;

use App\Models\Professor;
use App\Models\SectionSubject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Collection;

class ProfessorScheduleExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $profId;
    protected $activeAcadYear;
    protected $activeTerm;

    public function __construct($profId, $activeAcadYear, $activeTerm)
    {
        $this->profId = $profId;
        $this->activeAcadYear = $activeAcadYear;
        $this->activeTerm = $activeTerm;
    }

    public function collection()
    {
        $classes = SectionSubject::with(['subjectSectionSchedule', 'section', 'subject'])
            ->whereHas('section', function($query) {
                $query->where('academic_year', $this->activeAcadYear)
                      ->where('term', $this->activeTerm);
            })
            ->whereHas('subjectSectionSchedule', function($query) {
                $query->where('prof_id', $this->profId);
            })
            ->get();
    
        $professor = Professor::find($this->profId);
        $professorName = "{$professor->first_name} {$professor->last_name}";
    
        $formattedClasses = $classes->map(function ($class) use ($professorName) {
            return [
                'Professor Name' => $professorName,
                'Academic Year' => $this->activeAcadYear,
                'Term' => $this->activeTerm,
                'Subject Code' => $class->subject->subject_code,
                'Subject' => $class->subject->subject_name,
                'Section' => $class->section->section_name,
                'Schedule F2F' => $class->subjectSectionSchedule->schedule_f2f_formatted,
                'Schedule OL' => $class->subjectSectionSchedule->schedule_ol_formatted,
                'Room' => $class->subjectSectionSchedule->room,
            ];
        });
    
        return $formattedClasses;
    }
    
    public function headings(): array
    {
        return ['Professor Name', 'Academic Year', 'Term', 'Subject Code', 'Subject', 'Section', 'Schedule F2F', 'Schedule OL', 'Room'];
    }
    
}
