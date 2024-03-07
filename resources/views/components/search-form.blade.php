{{-- components/search-form.blade.php --}}

<div class="flex items-center space-x-4">
    <form action="{{ $action }}" method="GET" id="searchForm" x-ref="searchForm">
        <div class="relative flex">
            <x-text-input 
                class="h-6 center-placeholder searchbar" 
                name="query" 
                :placeholder="$placeholder"
                autocomplete="off"
                spellcheck="false"
                maxlength="38"
                x-model="searchTerm" />

            <button type="submit" class="input-icon" x-show="!searchTerm">
                <x-css-search />
            </button>

            <div x-show="searchTerm" @click.prevent="searchTerm = ''; $nextTick(() => $refs.searchForm.dispatchEvent(new Event('submit')));" class="input-icon" style="cursor: pointer;">
                <x-tabler-x />
            </div>
        </div>
    </form>              
</div>
