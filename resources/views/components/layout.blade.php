@props(['title' => 'POS SuperShop Back-Office'])

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | POS SuperShop</title>
    
    <!-- Modern Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS Play CDN & Alpine.js for interactive UI -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased flex flex-col md:flex-row overflow-hidden bg-slate-950 text-slate-100">

    <!-- Admin Sidebar -->
    <aside class="w-full md:w-64 bg-slate-900 border-r border-slate-800 flex flex-col shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-slate-800 gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center font-bold text-white shadow-lg shadow-indigo-500/25">
                P
            </div>
            <span class="font-bold tracking-tight text-white text-base">POS SuperShop</span>
            <span class="text-[10px] uppercase font-semibold px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-400 border border-indigo-500/30 ml-auto">Admin</span>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <a href="/admin/orders" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium bg-indigo-600/10 text-indigo-400 border border-indigo-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Orders & Sales
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Inventory
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports
            </a>
        </nav>

        <div class="p-4 border-t border-slate-800 flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-semibold text-slate-300 text-xs">
                AD
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-white truncate">Administrator</p>
                <p class="text-[11px] text-slate-400 truncate">Branch #101 • Dhaka Main</p>
            </div>
        </div>
    </aside>

    <!-- Main Viewport -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navigation -->
        <header class="h-16 bg-slate-900/60 backdrop-blur-md border-b border-slate-800 px-6 flex items-center justify-between sticky top-0 z-30">
            <h1 class="text-lg font-bold text-white tracking-tight">{{ $title }}</h1>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    FrankenPHP Octane Active
                </div>
            </div>
        </header>

        <!-- Page Body -->
        <main class="p-6 md:p-8 space-y-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
