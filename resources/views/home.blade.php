<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mauzo Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside class="w-64 bg-green-800 text-white flex flex-col">
        <div class="p-6 text-center border-b border-gray-700">
            <div class="text-2xl font-bold mb-1">Mauzosheetai</div>
            <div class="text-sm">Boss</div>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="/" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏠 Home</a>
            <a href="/mauzo" class="block px-4 py-2 hover:bg-yellow-600 rounded">🛒 Mauzo</a>
            <a href="/madeni" class="block px-4 py-2 hover:bg-yellow-600 rounded">💳 Madeni</a>
            <a href="/matumizi" class="block px-4 py-2 hover:bg-yellow-600 rounded">💰 Matumizi</a>
            <a href="/bidhaa" class="block px-4 py-2 hover:bg-yellow-600 rounded">📦 Bidhaa</a>
            <a href="/manunuzi" class="block px-4 py-2 hover:bg-yellow-600 rounded">🚚 Manunuzi</a>
            <a href="/wafanyakazi" class="block px-4 py-2 hover:bg-yellow-600 rounded">👔 Wafanyakazi</a>
            <a href="/masaplaya" class="block px-4 py-2 hover:bg-yellow-600 rounded">🏆 Masaplaya</a>
            <a href="/wateja" class="block px-4 py-2 hover:bg-yellow-600 rounded">👥 Wateja</a>
            <a href="/uchambuzi" class="block px-4 py-2 hover:bg-yellow-600 rounded">📊 Uchambuzi</a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col">
        <header class="bg-white shadow px-6 py-4">
            <h1 class="text-xl font-bold text-green-800">
                Karibu kwenye Dashboard ya Mauzo
            </h1>
        </header>

        <main class="flex-1 p-8 overflow-auto">
            <p>Hii ni ukurasa wa kwanza (home page). Chagua module kutoka kwenye sidebar ili kuendelea.</p>
        </main>
    </div>
</div>

</body>
</html>
