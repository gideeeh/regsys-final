<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Registrar System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="shortcut icon" href="{{asset('images/favicon.ico')}}" type="image/x-icon">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.10.1/main.min.css' rel='stylesheet' />

        <!-- Select2 CSS -->
        
        <!-- Select2 JS -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">
        <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.5/cdn.js" integrity="sha512-8IPRU0MPrge2KfSxkAtO8pIkaMzThW/MBSvPqcyVisSymLWC986buo27pKAt5mWXmt58dT6jIsw7h8NNugtRwg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{asset('js/script.js')}}"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://unpkg.com/@popperjs/core@2"></script> <!-- Popper.js is a dependency for Tippy.js -->
        <script src="https://unpkg.com/tippy.js@6"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
    <body class="font-sans antialiased bg-gray-100">
    <script>
        localStorage.theme = 'light';
        if (localStorage.theme === 'light') {
            document.documentElement.classList.remove('dark');
        } else {
            document.documentElement.classList.add('dark');
        }
    </script>
        <x-alert-message />
        <div class="min-h-screen bg-stone-100">
        @can('isAdmin')
        <!-- Include Admin Navigation -->
        @include('admin.navigation')
        @elsecan('isDean')
            <!-- Include Dean Navigation -->
            @include('dean.navigation')
        @else
            <!-- Include Regular User Navigation -->
            @include('user.navigation')
        @endcan

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        @stack('scripts')
    <script>
        document.documentElement.classList.remove('dark');
    </script>
    </body>
</html>
