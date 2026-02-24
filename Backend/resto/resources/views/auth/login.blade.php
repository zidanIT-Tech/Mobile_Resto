<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Pegawai - RestoKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm border border-gray-100">
        <div class="text-center mb-8">
            <div class="inline-block p-3 rounded-full bg-orange-100 text-orange-600 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Login Pegawai</h1>
            <p class="text-gray-500 text-sm mt-1">Masuk untuk mengelola restoran</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-4 text-xs font-semibold border border-red-100 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('login.proses') }}" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Email</label>
                <input type="email" name="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition text-sm" placeholder="nama@resto.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-xs font-bold mb-2 uppercase">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 focus:bg-white transition text-sm" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3.5 rounded-xl hover:bg-orange-700 transition shadow-lg shadow-orange-200 transform active:scale-95 text-sm">
                MASUK SISTEM
            </button>
        </form>
    </div>

</body>
</html>