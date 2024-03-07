<?php
namespace App\Services;

use App\Models\Academic_Year;
use Carbon\Carbon;

class AcademicYearService
{
    /**
     * Determine the currently active academic year and term based on today's date.
     *
     * @return array|null
     */
    public function determineActiveAcademicYearAndTerm(): ?array
    {
        $today = Carbon::today();

        $activeAcadYear = Academic_Year::where('term_1_start', '<=', $today)
                                        ->where(function ($query) use ($today) {
                                            $query->where('term_3_end', '>=', $today)
                                                  ->orWhere(function ($q) use ($today) {
                                                      $q->where('term_3_end', null)
                                                        ->where('term_2_end', '>=', $today);
                                                  });
                                        })
                                        ->first();

        if ($activeAcadYear) {
            // Determine the active term based on today's date
            $activeTerm = null;
            if ($today->between($activeAcadYear->term_1_start, $activeAcadYear->term_1_end)) {
                $activeTerm = 1;
            } elseif ($activeAcadYear->term_2_start && $today->between($activeAcadYear->term_2_start, $activeAcadYear->term_2_end)) {
                $activeTerm = 2;
            } elseif ($activeAcadYear->term_3_start && $today->between($activeAcadYear->term_3_start, $activeAcadYear->term_3_end)) {
                $activeTerm = 3;
            }

            return [
                'activeAcadYear' => $activeAcadYear,
                'activeTerm' => $activeTerm
            ];
        }

        return null;
    }
}
