@props(['title' => 'POS SuperShop Back-Office'])

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} | POS SuperShop Back-Office</title>
    
    <!-- Modern Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
                            50: '#eef2ff',
                            100: '#e0e7ff',
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
<body class="h-full font-sans antialiased flex flex-col md:flex-row overflow-hidden bg-slate-50 text-slate-800">

    <!-- Admin Sidebar (White Spectrum) -->
    <aside class="w-full md:w-64 bg-white border-r border-slate-200/80 flex flex-col shrink-0 z-20 shadow-sm">
        <div class="h-16 flex items-center px-6 border-b border-slate-100 gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <span class="font-bold tracking-tight text-slate-900 text-sm block">POS SuperShop</span>
                <span class="text-[10px] text-indigo-600 font-semibold uppercase tracking-wider block">Admin Suite</span>
            </div>
        </div>

        <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
            <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.orders.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.orders.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>Orders & Sales</span>
            </a>
            
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.users.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.users.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span>Staff & Access</span>
            </a>

            <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.customers.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.customers.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span>Customers & CRM</span>
            </a>

            <a href="{{ route('admin.accounting.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition {{ request()->routeIs('admin.accounting.*') ? 'bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.accounting.*') ? 'text-indigo-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Accounting & P&L</span>
            </a>
        </nav>

        <!-- Authenticated Admin Profile Card & Logout -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs shrink-0">
                    {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <p class="text-[11px] text-slate-500 capitalize truncate">{{ str_replace('_', ' ', auth()->user()->role ?? 'Manager') }}</p>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button 
                        type="submit" 
                        title="Sign Out"
                        class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Viewport -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navigation Bar -->
        <header class="h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-6 flex items-center justify-between sticky top-0 z-10 shadow-xs">
            <div class="flex items-center gap-3">
                <h1 class="text-base font-bold text-slate-900 tracking-tight">{{ $title }}</h1>
            </div>
            
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    FrankenPHP Octane Active
                </div>
                <a 
                    href="https://pos-frontend-lovat-eight.vercel.app" 
                    target="_blank" 
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition"
                >
                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Cashier POS</span>
                </a>
            </div>
        </header>

        <!-- Page Body (White Spectrum) -->
        <main class="p-6 md:p-8 space-y-6">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
