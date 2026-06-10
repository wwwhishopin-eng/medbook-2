@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title . ' | ' . config('app.name') : config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=vazirmatn:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased" style="font-family:'Vazirmatn','Figtree',sans-serif;">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('notify', ({ message, type }) => {
                    const toast = document.createElement('div');
                    toast.textContent = message;
                    Object.assign(toast.style, {
                        position: 'fixed', bottom: '24px', left: '24px',
                        padding: '12px 24px', borderRadius: '12px',
                        fontSize: '14px', fontFamily: "'Vazirmatn', sans-serif",
                        fontWeight: '600', zIndex: '9999',
                        color: type === 'success' ? '#15803D' : '#991B1B',
                        background: type === 'success' ? '#DCFCE7' : '#FEE2E2',
                        boxShadow: '0 8px 30px rgba(0,0,0,0.12)',
                        transition: 'all 0.3s ease',
                        opacity: '0', transform: 'translateY(12px)',
                    });
                    document.body.appendChild(toast);
                    requestAnimationFrame(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    });
                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(12px)';
                        setTimeout(() => toast.remove(), 300);
                    }, 3000);
                });
            });
        </script>
    </body>
</html>
