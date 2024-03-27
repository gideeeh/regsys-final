<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClassesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Retrieve your data. This is just an example. Adjust it according to your needs.
        return Class::all(); // Or any query to get the data you need
    }

    public function headings(): array
    {
        return ["Subject", "Section", "Sched F2F", "Sched OL", "Room (F2F)"];
    }
}

