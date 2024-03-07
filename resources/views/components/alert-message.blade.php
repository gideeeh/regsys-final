<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)" 
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform scale-90"
    x-transition:enter-end="opacity-100 transform scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform scale-100"
    x-transition:leave-end="opacity-0 transform scale-90"
    style="display: none;"
    class="fixed top-0 left-1/2 transform -translate-x-1/2 mt-4 z-50"
>
    @if(session('status') == 'password-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ __('Your password has been updated.') }}</span>
        </div>
    @elseif(session('status') == 'profile-updated')
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ __('Your profile has been updated.') }}</span>
        </div>
    @elseif(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <strong class="font-bold">Success!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @elseif($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong class="font-bold">Whoops! Something went wrong.</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
