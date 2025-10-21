<!doctype html>
<html lang="sw">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>@yield('title', 'MauzoSheet')</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial; }
    /* subtle glass effect container */
    .glass { background: rgba(8, 10, 12, 0.62); backdrop-filter: blur(6px); }
  </style>
  @stack('head')
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-black text-white">

  <div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-4xl mx-auto">
      <div class="flex flex-col lg:flex-row gap-8 items-stretch">

        {{-- Left brand column --}}
        <div class="hidden lg:flex lg:flex-1 items-center justify-center">
          <div class="text-center">
            <div class="mb-6">
              {{-- logo circle --}}
              <div class="h-28 w-28 rounded-full bg-yellow-400 flex items-center justify-center text-black text-3xl font-bold mx-auto">MS</div>
            </div>
            <h2 class="text-2xl font-bold text-yellow-300">MauzoSheet</h2>
            <p class="text-gray-300 mt-2 max-w-xs">Taratibu zahiri za usimamizi wa mauzo, bidhaa, na wafanyakazi — kwa urahisi.</p>
          </div>
        </div>

        {{-- Right form column --}}
        <div class="flex-1 glass rounded-2xl p-8 shadow-xl border border-gray-700">
          @yield('content')
        </div>
      </div>

      {{-- footer small --}}
      <div class="mt-6 text-center text-gray-500 text-sm">
        &copy; MauzoSheet {{ date('Y') }}
      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
