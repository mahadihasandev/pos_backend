<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In | POS SuperShop</title>
    
    <!-- Modern Typography: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Local Compiled Tailwind CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8">

    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo & Branding -->
        <div class="flex items-center justify-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/25">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold tracking-tight text-slate-900">POS SuperShop</h1>
                <span class="inline-block text-[11px] font-semibold text-indigo-600 uppercase tracking-wider">Back-Office Suite</span>
            </div>
        </div>

        <h2 class="mt-6 text-center text-2xl font-bold tracking-tight text-slate-900">
            Sign in to Admin Dashboard
        </h2>
        <p class="mt-2 text-center text-xs text-slate-500">
            Access real-time sales reporting, inventory control, and cashier management
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-xl shadow-slate-200/60 rounded-2xl border border-slate-200/80">
            
            @if ($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div>
                        <p class="font-semibold">Authentication Error</p>
                        <p class="mt-0.5">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5" id="loginForm">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700">Email Address</label>
                    <div class="mt-1.5 relative">
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email" 
                            required 
                            value="{{ old('email', 'admin@supershop.com') }}"
                            placeholder="admin@supershop.com"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                        />
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700">Password</label>
                    <div class="mt-1.5 relative">
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password" 
                            required 
                            value="password123"
                            placeholder="••••••••••••"
                            class="w-full px-3.5 py-2.5 bg-slate-50/70 border border-slate-200 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                        />
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500"
                        />
                        <span class="text-xs text-slate-600">Remember this workstation</span>
                    </label>
                </div>

                <button 
                    type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white font-semibold text-sm shadow-md shadow-indigo-500/25 transition flex items-center justify-center gap-2"
                >
                    <span>Sign In to Dashboard</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Quick Demo Accounts Switcher -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider text-center mb-3">
                    One-Click Quick Login
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        type="button" 
                        onclick="fillAccount('admin@supershop.com', 'password123')"
                        class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-left transition group"
                    >
                        <p class="text-xs font-semibold text-slate-700 group-hover:text-indigo-600">Store Manager</p>
                        <p class="text-[10px] text-slate-400">admin@supershop.com</p>
                    </button>
                    <button 
                        type="button" 
                        onclick="fillAccount('supervisor@supershop.com', 'password123')"
                        class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-200 text-left transition group"
                    >
                        <p class="text-xs font-semibold text-slate-700 group-hover:text-indigo-600">Supervisor</p>
                        <p class="text-[10px] text-slate-400">supervisor@supershop.com</p>
                    </button>
                </div>
            </div>

            <!-- Terminal POS Client Link -->
            <div class="mt-4 text-center">
                <a 
                    href="https://pos-frontend-lovat-eight.vercel.app" 
                    target="_blank" 
                    class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 hover:text-indigo-600 transition"
                >
                    <span>Open Cashier POS Terminal Client</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>

        </div>
    </div>

    <script>
        function fillAccount(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
