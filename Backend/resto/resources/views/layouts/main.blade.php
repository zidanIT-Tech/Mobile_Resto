<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RestoKu - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Animasi Fade In */
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen flex flex-col">

    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="bg-orange-500 text-white p-1.5 rounded-lg font-bold text-xl">R</div>
                <h1 class="text-xl font-bold text-gray-800 tracking-tight">RestoKu</h1>
            </div>

            <div class="text-sm text-gray-500 hidden md:block">
                {{ now()->format('l, d M Y') }}
            </div>
        </div>
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto p-4 lg:p-6 animate-fade-in">
        @yield('content')
    </main>

    <footer class="bg-white border-t mt-auto">
        <div class="max-w-7xl mx-auto py-4 px-4 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} RestoKu App. Selamat Menikmati.
        </div>
    </footer>

    @stack('scripts')
</body>
</html>