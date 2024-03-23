<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function barChart()
    {
        // Replace this with your actual data retrieval logic
        $data = [
            'labels' => ['BSCS', 'BSIT', 'BSBA'],
            'data' => [65, 59, 80],
        ];
        return $data;
    }

    // public function enrollmentTrends()
    // {
    //     $enrollmentTrends = Enrollment::select(
    //         DB::raw('academic_year'), // Adjusted to use enrollment_date
    //         DB::raw('count(*) as total')
    //     )
    //     ->groupBy('academic_year')
    //     ->orderBy('academic_year', 'asc')
    //     ->get();

    //     $labels = $enrollmentTrends->pluck('academic_year');
    //     $data = $enrollmentTrends->pluck('total');

    //     return ['labels' => $labels, 'data' => $data];
    // }

    public function enrollmentTrendsPerTerm()
    {
        $enrollmentTrends = DB::table('enrollments')
            ->select(
                DB::raw('CONCAT(SUBSTRING(academic_year, 3, 2), "-", SUBSTRING(academic_year, 8, 2), " T-", term) as term_label'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('academic_year', 'term')
            ->orderBy('academic_year', 'asc')
            ->orderBy('term', 'asc')
            ->get();

        $labels = $enrollmentTrends->pluck('term_label');
        $data = $enrollmentTrends->pluck('total');

        return ['labels' => $labels, 'data' => $data];
    }

    public function enrollmentsByProgram()
    {
        $programEnrollments = DB::table('enrollments')
        ->join('programs', 'enrollments.program_id', '=', 'programs.program_id')
        ->select('programs.program_name', DB::raw('count(*) as total'))
        ->groupBy('programs.program_name')
        ->get();

        $programLabels = $programEnrollments->pluck('program_name');
        $programData = $programEnrollments->pluck('total');

        return ['labels' => $programLabels, 'data' => $programData];
    }
}
