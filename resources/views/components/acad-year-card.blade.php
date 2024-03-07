<div class="acad-year-card border-solid border-2 border-slate-400 rounded-md px-4 py-4 mb-6 hover:border-sky-950 w-1/3">
    <div class="pb-4">
        <div>
            <h2>Acad Year: <span>{{ $data->acad_year }}</span></h2>
            <p>Date Start: <span>{{ $data->term_1_start }}</span></p>
            <p>Date End: <span>{{ $data->term_3_end ?? 'End date not set' }}</span></p>
        </div>
    </div>
    
    <div class="border-t-4 border-sky-400 py-2">
        <div>
            <h3 class="text-lg">Term 1: <span>{{ $data->term_1_start }} - {{ $data->term_1_end }}</span></h3>
        </div>
        <div class="border-dotted border-t-2 border-slate-400 py-2">
            <h3>Term 2: <span>{{ $data->term_2_start }} - {{ $data->term_2_end }}</span></h3>
        </div>
        <div class="border-dotted border-t-2 border-slate-400 py-2">
            <h3>Term 3: <span>{{ $data->term_3_start }} - {{ $data->term_3_end ?? 'End date not set' }}</span></h3>
        </div>
    </div>
    <span @click="showUpdateAcadYearTerm = true; selectedAcadYear = {{ json_encode($data) }}" class="underline text-gray-500 text-xs pb-1.5 hover:text-blue-600 cursor-pointer flex justify-end">Manage</span>
    <span class="underline text-red-400 text-xs pb-1.5 ml-4 hover:text-red-600 cursor-pointer flex justify-end">Delete</span>
</div>
